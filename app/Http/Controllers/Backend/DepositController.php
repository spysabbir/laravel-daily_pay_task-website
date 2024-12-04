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

            $pendingRequest = $query->get();

            return DataTables::of($pendingRequest)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->user->name . '</span>
                        ';
                })
                ->editColumn('method', function ($row) {
                    if ($row->method == 'Bkash') {
                        $method = '
                        <span class="badge bg-primary">' . $row->method . '</span>
                        ';
                    } else if ($row->method == 'Nagad') {
                        $method = '
                        <span class="badge bg-warning">' . $row->method . '</span>
                        ';
                    }else {
                        $method = '
                        <span class="badge bg-success">' . $row->method . '</span>
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
                        <span class="badge text-dark bg-light">' . $row->user->name . '</span>
                        ';
                })
                ->editColumn('method', function ($row) {
                    if ($row->method == 'Bkash') {
                        $method = '
                        <span class="badge bg-primary">' . $row->method . '</span>
                        ';
                    } else if ($row->method == 'Nagad') {
                        $method = '
                        <span class="badge bg-warning">' . $row->method . '</span>
                        ';
                    }else {
                        $method = '
                        <span class="badge bg-success">' . $row->method . '</span>
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
            $query = Deposit::where('status', 'Approved');

            if ($request->method){
                $query->where('deposits.method', $request->method);
            }

            if ($request->user_id){
                $query->where('deposits.user_id', $request->user_id);
            }

            $query->select('deposits.*')->orderBy('approved_at', 'desc');

            $approvedData = $query->get();

            return DataTables::of($approvedData)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->user->name . '</span>
                        ';
                })
                ->editColumn('method', function ($row) {
                    if ($row->method == 'Bkash') {
                        $method = '
                        <span class="badge bg-primary">' . $row->method . '</span>
                        ';
                    } else if ($row->method == 'Nagad') {
                        $method = '
                        <span class="badge bg-warning">' . $row->method . '</span>
                        ';
                    } else if ($row->method == 'Rocket') {
                        $method = '
                        <span class="badge bg-info">' . $row->method . '</span>
                        ';
                    } else {
                        $method = '
                        <span class="badge bg-success">' . $row->method . '</span>
                        ';
                    }
                    return $method;
                })
                ->editColumn('number', function ($row) {
                    if ($row->number) {
                        $number = $row->number;
                    } else {
                        $number = 'N/A';
                    }
                    return $number;
                })
                ->editColumn('transaction_id', function ($row) {
                    if ($row->transaction_id) {
                        $transaction_id = $row->transaction_id;
                    } else {
                        $transaction_id = 'N/A';
                    }
                    return $transaction_id;
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
                    if ($row->approvedBy) {
                        return '
                            <span class="badge text-dark bg-light">' . $row->approvedBy->name . '</span>'
                            ;
                    } else {
                        return '
                            <span class="badge text-warning bg-light">N/A</span>'
                            ;
                    }
                })
                ->editColumn('approved_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->approved_at)) . '</span>
                        ';
                })
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
