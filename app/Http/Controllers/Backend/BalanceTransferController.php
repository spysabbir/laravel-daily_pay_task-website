<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BalanceTransfer;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BalanceTransferController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('balance.transfer.history') , only:['balanceTransferHistory']),
        ];
    }
    public function balanceTransferHistory(Request $request)
    {
        if ($request->ajax()) {
            $query = BalanceTransfer::select('balance_transfers.*');

            if ($request->user_id){
                $query->where('balance_transfers.user_id', $request->user_id);
            }

            if ($request->send_method) {
                $query->where('balance_transfers.send_method', $request->send_method);
            }

            if ($request->receive_method) {
                $query->where('balance_transfers.receive_method', $request->receive_method);
            }

            $query->orderBy('created_at', 'desc');

            // sum of total balance transfers
            $totalBalanceTransferAmount = (clone $query)->sum('amount');

            $balanceTransferData = $query->get();

            return DataTables::of($balanceTransferData)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->user->id)) . '" class="text-primary" target="_blank">' . $row->user->name . '</a>
                        ';
                })
                ->editColumn('send_method', function ($row) {
                    if ($row->send_method == 'Deposit Balance') {
                        $send_method = '<span class="badge bg-info">' . $row->send_method . '</span>';
                    } else {
                        $send_method = '<span class="badge bg-primary">' . $row->send_method . '</span>';
                    }
                    return $send_method;
                })
                ->editColumn('receive_method', function ($row) {
                    if ($row->receive_method == 'Deposit Balance') {
                        $receive_method = '<span class="badge bg-info">' . $row->receive_method . '</span>';
                    } else {
                        $receive_method = '<span class="badge bg-primary">' . $row->receive_method . '</span>';
                    }
                    return $receive_method;
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
                ->with([
                    'totalBalanceTransferAmount' => $totalBalanceTransferAmount,
                ])
                ->rawColumns(['user_name', 'send_method', 'receive_method', 'amount', 'payable_amount', 'created_at'])
                ->make(true);
        }

        return view('backend.balance_transfer.index');
    }
}
