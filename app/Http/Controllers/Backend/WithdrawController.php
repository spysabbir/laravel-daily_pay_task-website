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

            $query->select('withdraws.*')->orderBy('type', 'desc')->orderBy('created_at', 'desc');

            $pendingRequest = $query->get();

            return DataTables::of($pendingRequest)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->user->name . '</span>
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
                ->rawColumns(['user_name', 'type', 'method', 'amount', 'payable_amount', 'created_at', 'action'])
                ->make(true);
        }

        return view('backend.withdraw.index');
    }

    public function withdrawRequestShow(string $id)
    {
        $withdraw = Withdraw::where('id', $id)->first();
        $reportsPending = Report::where('user_id', Auth::id())->where('status', 'Pending')->count();
        return view('backend.withdraw.show', compact('withdraw', 'reportsPending'));
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
            $query = Withdraw::where('status', 'Rejected');

            $query->select('withdraws.*')->orderBy('rejected_at', 'desc');

            $rejectedData = $query->get();

            return DataTables::of($rejectedData)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->user->name . '</span>
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

            $query->select('withdraws.*')->orderBy('approved_at', 'desc');

            $approvedData = $query->get();

            return DataTables::of($approvedData)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->user->name . '</span>
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
                ->rawColumns(['user_name', 'type', 'method', 'amount', 'payable_amount', 'created_at', 'approved_by', 'approved_at'])
                ->make(true);
        }

        return view('backend.withdraw.approved');
    }

    public function withdrawRequestDelete(string $id)
    {
        $withdraw = Withdraw::findOrFail($id);

        $withdraw->delete();
    }
}
