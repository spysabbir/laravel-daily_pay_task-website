<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Withdraw;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use App\Notifications\WithdrawNotification;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\BonusNotification;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class WithdrawController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('withdraw.request') , only:['withdrawRequest']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('withdraw.request.send') , only:['withdrawRequestSend']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('withdraw.request.check') , only:['withdrawRequestShow', 'withdrawRequestStatusChange']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('withdraw.request.rejected'), only:['withdrawRequestRejected']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('withdraw.request.approved') , only:['withdrawRequestApproved']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('withdraw.request.delete') , only:['withdrawRequestDelete']),
        ];
    }

    public function withdrawRequest(Request $request)
    {
        if ($request->ajax()) {
            $query = Withdraw::where('status', 'Pending');

            if ($request->method){
                $query->where('withdraws.method', $request->method);
            }

            if ($request->type){
                $query->where('withdraws.type', $request->type);
            }

            if ($request->user_id){
                $query->where('withdraws.user_id', $request->user_id);
            }

            if ($request->number){
                $query->where('withdraws.number', $request->number);
            }

            $query->select('withdraws.*')->orderBy('type', 'desc')->orderBy('created_at', 'desc');

            // Clone the query for counts
            $totalWithdrawsCount = (clone $query)->count();

            $pendingRequest = $query->get();

            return DataTables::of($pendingRequest)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->user->id)) . '" class="text-primary" target="_blank">' . $row->user->name . '</a>
                        ';
                })
                ->editColumn('type', function ($row) {
                    if ($row->type == 'Ragular') {
                        $type = '
                        <span class="badge bg-dark">' . $row->type . '</span>
                        ';
                    } else {
                        $type = '
                        <span class="badge bg-primary">' . $row->type . '</span>
                        ';
                    }
                    return $type;
                })
                ->editColumn('method', function ($row) {
                    if ($row->method == 'Bkash') {
                        $method = '
                        <span class="badge bg-primary">' . $row->method . '</span>
                        ';
                    } else if ($row->method == 'Nagad') {
                        $method = '
                        <span class="badge bg-success">' . $row->method . '</span>
                        ';
                    } else {
                        $method = '
                        <span class="badge bg-info">' . $row->method . '</span>
                        ';
                    }
                    return $method;
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->amount . '</span>';
                })
                ->editColumn('payable_amount', function ($row) {
                    return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->payable_amount . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">Check</button>
                    ';
                return $btn;
                })
                ->with([
                    'totalWithdrawsCount' => $totalWithdrawsCount,
                ])
                ->rawColumns(['user_name', 'type', 'method', 'amount', 'payable_amount', 'created_at', 'action'])
                ->make(true);
        }

        $users = User::where('user_type', 'Frontend')->whereIn('status', ['Active', 'Blocked'])->get();
        return view('backend.withdraw.index', compact('users'));
    }

    public function withdrawRequestShow(string $id)
    {
        $withdraw = Withdraw::where('id', $id)->first();
        $reportsPending = Report::where('user_id', Auth::id())->where('status', 'Pending')->count();

        $withdrawNumber = Withdraw::where('user_id', $withdraw->user_id)->whereNot('method', 'Deposit Balance')->groupBy('number')->pluck('number')->toArray();
        $sameNumberUserIds = Withdraw::whereNot('user_id', $withdraw->user_id)->whereNot('method', 'Deposit Balance')->whereIn('number', $withdrawNumber)->groupBy('user_id')->pluck('user_id')->toArray();
        $sameNumberUserIds = User::whereIn('id', $sameNumberUserIds)->whereIn('status', ['Active', 'Blocked'])->get();

        return view('backend.withdraw.show', compact('withdraw', 'reportsPending', 'sameNumberUserIds'));
    }

    public function withdrawRequestStatusChange(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'rejected_reason' => 'required_if:status,Rejected',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray(),
            ]);
        }

        $withdraw = Withdraw::findOrFail($id);
        $user = User::findOrFail($withdraw->user_id);
        $referrer = $user->referred_by ? User::find($user->referred_by) : null;

        $withdraw->update([
            'status' => $request->status,
            'rejected_reason' => $request->status === 'Rejected' ? $request->rejected_reason : null,
            'rejected_by' => $request->status === 'Rejected' ? auth()->user()->id : null,
            'rejected_at' => $request->status === 'Rejected' ? now() : null,
            'approved_by' => $request->status === 'Approved' ? auth()->user()->id : null,
            'approved_at' => $request->status === 'Approved' ? now() : null,
        ]);

        if ($request->status === 'Approved') {
            if ($referrer) {
                $bonusAmount = ($withdraw->amount * get_default_settings('referral_withdrawal_bonus_percentage')) / 100;

                $referrer->update([
                    'withdraw_balance' => $referrer->withdraw_balance + $bonusAmount,
                ]);

                $referrerBonus = Bonus::create([
                    'user_id' => $referrer->id,
                    'bonus_by' => $user->id,
                    'type' => 'Referral Withdrawal Bonus',
                    'amount' => $bonusAmount,
                ]);

                $referrer->notify(new BonusNotification($referrerBonus));
            }
        } elseif ($request->status === 'Rejected') {
            $user->update([
                'withdraw_balance' => $user->withdraw_balance + $withdraw->amount,
            ]);
        }

        $user->notify(new WithdrawNotification($withdraw));

        return response()->json([
            'status' => 200,
        ]);
    }

    public function withdrawRequestRejected(Request $request)
    {
        if ($request->ajax()) {
            $query = Withdraw::where('status', 'Rejected')->whereNot('method', 'Deposit Balance');

            $query->select('withdraws.*')->orderBy('rejected_at', 'desc');

            $rejectedData = $query->get();

            return DataTables::of($rejectedData)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->user->id)) . '" class="text-primary" target="_blank">' . $row->user->name . '</a>
                        ';
                })
                ->editColumn('type', function ($row) {
                    if ($row->type == 'Ragular') {
                        $type = '
                        <span class="badge bg-dark">' . $row->type . '</span>
                        ';
                    } else {
                        $type = '
                        <span class="badge bg-primary">' . $row->type . '</span>
                        ';
                    }
                    return $type;
                })
                ->editColumn('method', function ($row) {
                    if ($row->method == 'Bkash') {
                        $method = '
                        <span class="badge bg-primary">' . $row->method . '</span>
                        ';
                    } else if ($row->method == 'Nagad') {
                        $method = '
                        <span class="badge bg-success">' . $row->method . '</span>
                        ';
                    } else {
                        $method = '
                        <span class="badge bg-info">' . $row->method . '</span>
                        ';
                    }
                    return $method;
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->amount . '</span>';
                })
                ->editColumn('payable_amount', function ($row) {
                    return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->payable_amount . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->editColumn('rejected_by', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->rejectedBy->name . '</span>
                        ';
                })
                ->editColumn('rejected_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->rejected_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $deletePermission = auth()->user()->can('deposit.request.delete');

                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>'
                        : '';

                    return $deleteBtn;
                })
                ->rawColumns(['user_name', 'type', 'method', 'amount', 'payable_amount', 'created_at', 'rejected_by', 'rejected_at', 'action'])
                ->make(true);
        }

        return view('backend.withdraw.index');
    }

    public function withdrawRequestApproved(Request $request)
    {
        if ($request->ajax()) {
            $query = Withdraw::where('status', 'Approved');

            if ($request->method){
                $query->where('withdraws.method', $request->method);
            }

            if ($request->type){
                $query->where('withdraws.type', $request->type);
            }

            if ($request->user_id){
                $query->where('withdraws.user_id', $request->user_id);
            }

            if ($request->number){
                $query->where('withdraws.number', $request->number);
            }

            $query->select('withdraws.*')->orderBy('approved_at', 'desc');

            // Clone the query for counts
            $totalWithdrawsCount = (clone $query)->count();

            $approvedData = $query->get();

            return DataTables::of($approvedData)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->user->id)) . '" class="text-primary" target="_blank">' . $row->user->name . '</a>
                        ';
                })
                ->editColumn('type', function ($row) {
                    if ($row->type == 'Ragular') {
                        $type = '
                        <span class="badge bg-dark">' . $row->type . '</span>
                        ';
                    } else {
                        $type = '
                        <span class="badge bg-primary">' . $row->type . '</span>
                        ';
                    }
                    return $type;
                })
                ->editColumn('method', function ($row) {
                    if ($row->method == 'Bkash') {
                        $method = '
                        <span class="badge bg-primary">' . $row->method . '</span>
                        ';
                    } else if ($row->method == 'Nagad') {
                        $method = '
                        <span class="badge bg-success">' . $row->method . '</span>
                        ';
                    } else {
                        $method = '
                        <span class="badge bg-info">' . $row->method . '</span>
                        ';
                    }
                    return $method;
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->amount . '</span>';
                })
                ->editColumn('payable_amount', function ($row) {
                    return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->payable_amount . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->editColumn('approved_by', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->approvedBy->name . '</span>
                        ';
                })
                ->editColumn('approved_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->approved_at)) . '</span>
                        ';
                })
                ->with([
                    'totalWithdrawsCount' => $totalWithdrawsCount,
                ])
                ->rawColumns(['user_name', 'type', 'method', 'amount', 'payable_amount', 'created_at', 'approved_by', 'approved_at'])
                ->make(true);
        }

        return view('backend.withdraw.approved');
    }

    public function withdrawRequestSend(Request $request)
    {
        $currencySymbol = get_site_settings('site_currency_symbol');
        $withdrawChargePercentage = get_default_settings('withdraw_charge_percentage');
        $instantWithdrawCharge = get_default_settings('instant_withdraw_charge');

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:Ragular,Instant',
            'amount' => "required|numeric|min:25",
            'method' => 'required|in:Bkash,Nagad,Rocket',
            'number' => ['required', 'string', 'regex:/^(?:\+8801|01)[3-9]\d{8}$/'],
        ],
        [
            'number.regex' => 'The phone number must be a valid Bangladeshi number (+8801XXXXXXXX or 01XXXXXXXX).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        }

        $user = User::findOrFail($request->user_id);

        if ($request->amount > $user->withdraw_balance) {
            return response()->json([
                'status' => 402,
                'error' => 'Insufficient balance in this user account to withdraw ' . $currencySymbol . $request->amount .
                        '. Your current balance is ' . $currencySymbol . $user->withdraw_balance
            ]);
        }

        $payableAmount = $request->amount - ($request->amount * $withdrawChargePercentage / 100);
        if ($request->type == 'Instant') {
            $payableAmount -= $instantWithdrawCharge;
        }

        Withdraw::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'method' => $request->method,
            'number' => $request->number,
            'amount' => $request->amount,
            'payable_amount' => $payableAmount,
            'status' => 'Pending',
            'created_by' => auth()->user()->id,
        ]);

        return response()->json([
            'status' => 200,
        ]);
    }

    public function withdrawRequestDelete(string $id)
    {
        $withdraw = Withdraw::findOrFail($id);

        $withdraw->delete();
    }
}
