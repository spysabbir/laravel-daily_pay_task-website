<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BonusController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('bonus.history') , only:['bonusHistory']),
        ];
    }
    public function bonusHistory(Request $request)
    {
        if ($request->ajax()) {
            $query = Bonus::select('bonuses.*');

            if ($request->user_id){
                $query->where('bonuses.user_id', $request->user_id);
            }

            if ($request->bonus_by) {
                $query->where('bonuses.bonus_by', $request->bonus_by);
            }

            if ($request->type) {
                $query->where('bonuses.type', $request->type);
            }

            $query->orderBy('created_at', 'desc');

            // sum of total bonuses
            $totalBonusAmount = (clone $query)->sum('amount');

            $bonusData = $query->get();

            return DataTables::of($bonusData)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->user->id)) . '" class="text-primary" target="_blank">' . $row->user->name . '</a>
                        ';
                })
                ->editColumn('bonus_by_user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->bonusBy->id)) . '" class="text-primary" target="_blank">' . $row->bonusBy->name . '</a>
                        ';
                })
                ->editColumn('type', function ($row) {
                    if ($row->type == 'Proof Task Approved Bonus') {
                        $type = '<span class="badge bg-info">' . $row->type . '</span>';
                    } else {
                        $type = '<span class="badge bg-primary">' . $row->type . '</span>';
                    }
                    return $type;
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->amount . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->with([
                    'totalBonusAmount' => $totalBonusAmount,
                ])
                ->rawColumns(['user_name', 'bonus_by_user_name', 'type', 'amount', 'created_at'])
                ->make(true);
        }

        return view('backend.bonus.index');
    }
}
