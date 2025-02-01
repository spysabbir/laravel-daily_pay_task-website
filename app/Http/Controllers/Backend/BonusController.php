<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Notifications\BonusNotification;

class BonusController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('bonus.history') , only:['bonusHistory']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('bonus.store') , only:['bonusStore']),
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
                ->editColumn('bonus_by', function ($row) {
                    if ($row->type == 'Proof Task Approved Bonus') {
                        $bonus_by = '<span class="badge bg-success">Buyer</span>';
                    } else {
                        $bonus_by = '<span class="badge bg-primary">Site</span>';
                    }
                    return $bonus_by;
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
                ->rawColumns(['user_name', 'bonus_by', 'bonus_by_user_name', 'type', 'amount', 'created_at'])
                ->make(true);
        }

        $users = User::where('user_type', 'Frontend')->whereIn('status', ['Active', 'Blocked'])->get();
        return view('backend.bonus.index', compact('users'));
    }

    public function bonusStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            $userBonus = Bonus::create([
                'user_id' => $request->user_id,
                'bonus_by' => auth()->user()->id,
                'type' => 'Site Special Bonus',
                'amount' => $request->amount,
            ]);

            $user = User::where('id', $request->user_id)->first();
            $user->increment('withdraw_balance', $request->amount);

            $user->notify(new BonusNotification($userBonus));

            return response()->json([
                'status' => 200,
            ]);
        }
    }
}
