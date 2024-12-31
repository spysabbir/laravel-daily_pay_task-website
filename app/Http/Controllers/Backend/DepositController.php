<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deposit;
use App\Models\User;
use App\Notifications\DepositNotification;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DepositController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('deposit.request') , only:['depositRequest']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('deposit.request.check') , only:['depositRequestShow', 'depositRequestStatusChange']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('deposit.request.rejected'), only:['depositRequestRejected']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('deposit.request.approved') , only:['depositRequestApproved']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('deposit.request.delete') , only:['depositRequestDelete']),
        ];
    }

    public function depositRequest(Request $request)
    {
        if ($request->ajax()) {
            $query = Deposit::where('status', 'Pending');

            if ($request->method){
                $query->where('deposits.method', $request->method);
            }

            if ($request->user_id){
                $query->where('deposits.user_id', $request->user_id);
            }

            $query->select('deposits.*')->orderBy('created_at', 'desc');

            // Clone the query for counts
            $totalDepositsCount = (clone $query)->count();

            $pendingRequest = $query->get();

            return DataTables::of($pendingRequest)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->user->id)) . '" class="text-primary" target="_blank">' . $row->user->name . '</a>
                        ';
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
                    }else {
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
                    'totalDepositsCount' => $totalDepositsCount,
                ])
                ->rawColumns(['user_name', 'method', 'amount', 'payable_amount', 'created_at', 'action'])
                ->make(true);
        }

        return view('backend.deposit.index');
    }

    public function depositRequestShow(string $id)
    {
        $deposit = Deposit::where('id', $id)->first();
        return view('backend.deposit.show', compact('deposit'));
    }

    public function depositRequestStatusChange(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'rejected_reason' => 'required_if:status,Rejected',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        }

        $deposit = Deposit::findOrFail($id);
        $user = User::where('id', $deposit->user_id)->first();

        $deposit->update([
            'status' => $request->status,
            'rejected_reason' => $request->status === 'Rejected' ? $request->rejected_reason : null,
            'rejected_by' => $request->status == 'Rejected' ? auth()->user()->id : NULL,
            'rejected_at' => $request->status == 'Rejected' ? now() : NULL,
            'approved_by' => $request->status == 'Approved' ? auth()->user()->id : NULL,
            'approved_at' => $request->status == 'Approved' ? now() : NULL,
        ]);

        if ($request->status == 'Approved') {
            $user->update([
                'deposit_balance' => $user->deposit_balance + $deposit->amount
            ]);
        }

        $user->notify(new DepositNotification($deposit));

        return response()->json([
            'status' => 200,
        ]);
    }

    public function depositRequestRejected(Request $request)
    {
        if ($request->ajax()) {
            $query = Deposit::where('status', 'Rejected');

            $query->select('deposits.*')->orderBy('rejected_at', 'desc');

            $rejectedData = $query->get();

            return DataTables::of($rejectedData)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->user->id)) . '" class="text-primary" target="_blank">' . $row->user->name . '</a>
                        ';
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
                    }else {
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
                ->rawColumns(['user_name', 'method', 'amount', 'payable_amount', 'created_at', 'rejected_by', 'rejected_at', 'action'])
                ->make(true);
        }

        return view('backend.deposit.index');
    }

    public function depositRequestApproved(Request $request)
    {
        if ($request->ajax()) {
            $query = Deposit::where('status', 'Approved')->whereNot('method', 'Withdraw Balance');

            if ($request->method){
                $query->where('deposits.method', $request->method);
            }

            if ($request->user_id){
                $query->where('deposits.user_id', $request->user_id);
            }

            $query->select('deposits.*')->orderBy('approved_at', 'desc');

            // Clone the query for counts
            $totalDepositsCount = (clone $query)->count();

            $approvedData = $query->get();

            return DataTables::of($approvedData)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->user->id)) . '" class="text-primary" target="_blank">' . $row->user->name . '</a>
                        ';
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
                ->editColumn('number', function ($row) {
                    return $row->number;
                })
                ->editColumn('transaction_id', function ($row) {
                    return  $row->transaction_id;
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->amount . '</span>';
                })
                ->editColumn('payable_amount', function ($row) {
                    return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->payable_amount . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y h:i A');
                })
                ->editColumn('approved_by', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->approvedBy->name . '</span>'
                        ;
                })
                ->editColumn('approved_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->approved_at)) . '</span>
                        ';
                })
                ->with([
                    'totalDepositsCount' => $totalDepositsCount,
                ])
                ->rawColumns(['user_name', 'method', 'number', 'transaction_id', 'amount','payable_amount', 'approved_by', 'approved_at'])
                ->make(true);
        }

        return view('backend.deposit.approved');
    }

    public function depositRequestDelete(string $id)
    {
        $deposit = Deposit::findOrFail($id);

        $deposit->delete();
    }
}
