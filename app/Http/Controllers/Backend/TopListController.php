<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deposit;
use App\Models\PostTask;
use App\Models\ProofTask;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TopListController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('top.deposit.user') , only:['topDepositUser']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('top.withdraw.user') , only:['topWithdrawUser']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('top.posted_task.user') , only:['topPostedTaskUser']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('top.worked_task.user') , only:['topWorkedTaskUser']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('top.referred.user') , only:['topReferredUser']),
        ];
    }

    public function topDepositUser(Request $request)
    {
        if ($request->ajax()) {
            // Default date range: all data unless filtered
            $startDate = $request->start_date ?? null;
            $endDate = $request->end_date ?? null;

            // Base query for user totals grouped by status
            $baseQuery = Deposit::select(
                'deposits.user_id',
                DB::raw('SUM(CASE WHEN deposits.status = "Pending" AND deposits.method != "Withdraw Balance" THEN deposits.amount ELSE 0 END) as pending_amount'),
                DB::raw('SUM(CASE WHEN deposits.status = "Rejected" AND deposits.method != "Withdraw Balance" THEN deposits.amount ELSE 0 END) as rejected_amount'),
                DB::raw('SUM(CASE WHEN deposits.status = "Approved" AND deposits.method != "Withdraw Balance" THEN deposits.amount ELSE 0 END) as approved_amount'),
                DB::raw('SUM(deposits.amount) as total_amount'),
                DB::raw('SUM(CASE WHEN deposits.method = "Withdraw Balance" THEN deposits.amount ELSE 0 END) as transfer_amount') // Include only Withdraw Balance
            );

            // Apply date filters
            if ($startDate && $endDate) {
                $baseQuery->whereBetween(DB::raw('DATE(deposits.created_at)'), [$startDate, $endDate]);
            } elseif ($startDate) {
                $baseQuery->whereDate('deposits.created_at', '>=', $startDate);
            } elseif ($endDate) {
                $baseQuery->whereDate('deposits.created_at', '<=', $endDate);
            }

            $baseQuery->groupBy('deposits.user_id')
                    ->orderBy('total_amount', 'desc');

            $topDepositUsers = $baseQuery->get();

            // Query to calculate overall totals for all users
            $totalsQuery = Deposit::select(
                DB::raw('SUM(CASE WHEN deposits.status = "Pending" AND deposits.method != "Withdraw Balance" THEN deposits.amount ELSE 0 END) as total_pending'),
                DB::raw('SUM(CASE WHEN deposits.status = "Rejected" AND deposits.method != "Withdraw Balance" THEN deposits.amount ELSE 0 END) as total_rejected'),
                DB::raw('SUM(CASE WHEN deposits.status = "Approved" AND deposits.method != "Withdraw Balance" THEN deposits.amount ELSE 0 END) as total_approved'),
                DB::raw('SUM(deposits.amount) as grand_total'), // Includes all rows
                DB::raw('SUM(CASE WHEN deposits.method = "Withdraw Balance" THEN deposits.amount ELSE 0 END) as total_transfer') // Includes only Withdraw Balance rows
            );

            // Apply the same date filters to totals query
            if ($startDate && $endDate) {
                $totalsQuery->whereBetween(DB::raw('DATE(deposits.created_at)'), [$startDate, $endDate]);
            } elseif ($startDate) {
                $totalsQuery->whereDate('deposits.created_at', '>=', $startDate);
            } elseif ($endDate) {
                $totalsQuery->whereDate('deposits.created_at', '<=', $endDate);
            }

            $totals = $totalsQuery->first();

            // Return the DataTables response with totals
            return DataTables::of($topDepositUsers)
                ->addIndexColumn()
                ->editColumn('user_id', function ($row) {
                    return '<span class="badge bg-primary">' . $row->user_id . '</span>';
                })
                ->editColumn('user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->user->id)) . '" class="text-primary" target="_blank">' . $row->user->name . '</a>
                        ';
                })
                ->addColumn('pending_amount', function ($row) {
                    return '<span class="badge bg-warning">' . get_site_settings('site_currency_symbol') . ' ' . $row->pending_amount . '</span>';
                })
                ->addColumn('rejected_amount', function ($row) {
                    return '<span class="badge bg-danger">' . get_site_settings('site_currency_symbol') . ' ' . $row->rejected_amount . '</span>';
                })
                ->addColumn('approved_amount', function ($row) {
                    return '<span class="badge bg-success">' . get_site_settings('site_currency_symbol') . ' ' . $row->approved_amount . '</span>';
                })
                ->addColumn('transfer_amount', function ($row) {
                    return '<span class="badge bg-success">' . get_site_settings('site_currency_symbol') . ' ' . $row->transfer_amount . '</span>';
                })
                ->addColumn('total_amount', function ($row) {
                    return '<span class="badge bg-info">' . get_site_settings('site_currency_symbol') . ' ' . $row->total_amount . '</span>';
                })
                ->with([
                    'total_pending' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_pending,
                    'total_rejected' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_rejected,
                    'total_approved' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_approved,
                    'total_transfer' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_transfer,
                    'grand_total' => get_site_settings('site_currency_symbol') . ' ' . $totals->grand_total,
                ])
                ->rawColumns(['user_id', 'user_name', 'pending_amount', 'rejected_amount', 'approved_amount', 'transfer_amount', 'total_amount'])
                ->make(true);
        }

        return view('backend.top_list.deposit');
    }

    public function topWithdrawUser(Request $request)
    {
        if ($request->ajax()) {
            // Default date range: all data unless filtered
            $startDate = $request->start_date ?? null;
            $endDate = $request->end_date ?? null;

            // Base query for user totals grouped by status
            $baseQuery = Withdraw::select(
                'withdraws.user_id',
                DB::raw('SUM(CASE WHEN withdraws.status = "Pending" THEN withdraws.amount ELSE 0 END) as pending_amount'),
                DB::raw('SUM(CASE WHEN withdraws.status = "Rejected" THEN withdraws.amount ELSE 0 END) as rejected_amount'),
                DB::raw('SUM(CASE WHEN withdraws.status = "Approved" THEN withdraws.amount ELSE 0 END) as approved_amount'),
                DB::raw('SUM(withdraws.amount) as total_amount')
            );

            // Apply date filters
            if ($startDate && $endDate) {
                $baseQuery->whereBetween(DB::raw('DATE(withdraws.created_at)'), [$startDate, $endDate]);
            } elseif ($startDate) {
                $baseQuery->whereDate('withdraws.created_at', '>=', $startDate);
            } elseif ($endDate) {
                $baseQuery->whereDate('withdraws.created_at', '<=', $endDate);
            }

            $baseQuery->groupBy('withdraws.user_id')
                    ->orderBy('total_amount', 'desc');

            $topDepositUsers = $baseQuery->get();

            // Query to calculate overall totals for all users
            $totalsQuery = Withdraw::select(
                DB::raw('SUM(CASE WHEN withdraws.status = "Pending" THEN withdraws.amount ELSE 0 END) as total_pending'),
                DB::raw('SUM(CASE WHEN withdraws.status = "Rejected" THEN withdraws.amount ELSE 0 END) as total_rejected'),
                DB::raw('SUM(CASE WHEN withdraws.status = "Approved" THEN withdraws.amount ELSE 0 END) as total_approved'),
                DB::raw('SUM(withdraws.amount) as grand_total')
            );

            // Apply the same date filters to totals query
            if ($startDate && $endDate) {
                $totalsQuery->whereBetween(DB::raw('DATE(withdraws.created_at)'), [$startDate, $endDate]);
            } elseif ($startDate) {
                $totalsQuery->whereDate('withdraws.created_at', '>=', $startDate);
            } elseif ($endDate) {
                $totalsQuery->whereDate('withdraws.created_at', '<=', $endDate);
            }

            $totals = $totalsQuery->first();

            // Return the DataTables response with totals
            return DataTables::of($topDepositUsers)
                ->addIndexColumn()
                ->editColumn('user_id', function ($row) {
                    return '<span class="badge bg-primary">' . $row->user_id . '</span>';
                })
                ->editColumn('user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->user->id)) . '" class="text-primary" target="_blank">' . $row->user->name . '</a>
                        ';
                })
                ->addColumn('pending_amount', function ($row) {
                    return '<span class="badge bg-warning">' . get_site_settings('site_currency_symbol') . ' ' . $row->pending_amount . '</span>';
                })
                ->addColumn('rejected_amount', function ($row) {
                    return '<span class="badge bg-danger">' . get_site_settings('site_currency_symbol') . ' ' . $row->rejected_amount . '</span>';
                })
                ->addColumn('approved_amount', function ($row) {
                    return '<span class="badge bg-success">' . get_site_settings('site_currency_symbol') . ' ' . $row->approved_amount . '</span>';
                })
                ->addColumn('total_amount', function ($row) {
                    return '<span class="badge bg-info">' . get_site_settings('site_currency_symbol') . ' ' . $row->total_amount . '</span>';
                })
                ->with([
                    'total_pending' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_pending,
                    'total_rejected' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_rejected,
                    'total_approved' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_approved,
                    'grand_total' => get_site_settings('site_currency_symbol') . ' ' . $totals->grand_total,
                ])
                ->rawColumns(['user_id', 'user_name', 'pending_amount', 'rejected_amount', 'approved_amount', 'total_amount'])
                ->make(true);
        }

        return view('backend.top_list.withdraw');
    }

    public function topPostedTaskUser(Request $request)
    {
        if ($request->ajax()) {
            // Default date range: all data unless filtered
            $startDate = $request->start_date ?? null;
            $endDate = $request->end_date ?? null;

            // Base query for user task counts grouped by status
            $query = PostTask::select(
                'post_tasks.user_id',
                DB::raw('SUM(CASE WHEN post_tasks.status = "Pending" THEN 1 ELSE 0 END) as pending_count'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Rejected" THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Running" THEN 1 ELSE 0 END) as running_count'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Canceled" THEN 1 ELSE 0 END) as canceled_count'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Paused" THEN 1 ELSE 0 END) as paused_count'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Completed" THEN 1 ELSE 0 END) as completed_count'),
                DB::raw('COUNT(post_tasks.id) as total_tasks')
            );

            // Apply date filters
            if ($startDate && $endDate) {
                $query->whereBetween(DB::raw('DATE(post_tasks.created_at)'), [$startDate, $endDate]);
            } elseif ($startDate) {
                $query->whereDate('post_tasks.created_at', '>=', $startDate);
            } elseif ($endDate) {
                $query->whereDate('post_tasks.created_at', '<=', $endDate);
            }

            $query->groupBy('post_tasks.user_id')
                ->orderBy('total_tasks', 'desc');

            $topPostedTaskUsers = $query->get();

            // Query to calculate overall totals for all users
            $totalsQuery = PostTask::select(
                DB::raw('SUM(CASE WHEN post_tasks.status = "Pending" THEN 1 ELSE 0 END) as total_pending'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Rejected" THEN 1 ELSE 0 END) as total_rejected'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Running" THEN 1 ELSE 0 END) as total_running'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Canceled" THEN 1 ELSE 0 END) as total_canceled'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Paused" THEN 1 ELSE 0 END) as total_paused'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Completed" THEN 1 ELSE 0 END) as total_completed'),
                DB::raw('COUNT(post_tasks.id) as total_tasks_sum')
            );

            // Apply the same date filters to totals query
            if ($startDate && $endDate) {
                $totalsQuery->whereBetween(DB::raw('DATE(post_tasks.created_at)'), [$startDate, $endDate]);
            } elseif ($startDate) {
                $totalsQuery->whereDate('post_tasks.created_at', '>=', $startDate);
            } elseif ($endDate) {
                $totalsQuery->whereDate('post_tasks.created_at', '<=', $endDate);
            }

            $totals = $totalsQuery->first();

            // Return the DataTables response with totals
            return DataTables::of($topPostedTaskUsers)
                ->addIndexColumn()
                ->editColumn('user_id', function ($row) {
                    return '<span class="badge bg-primary">' . $row->user_id . '</span>';
                })
                ->editColumn('user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->user->id)) . '" class="text-primary" target="_blank">' . $row->user->name . '</a>
                        ';
                })
                ->addColumn('pending_count', function ($row) {
                    return '<span class="badge bg-warning">' . $row->pending_count . '</span>';
                })
                ->addColumn('rejected_count', function ($row) {
                    return '<span class="badge bg-danger">' . $row->rejected_count . '</span>';
                })
                ->addColumn('running_count', function ($row) {
                    return '<span class="badge bg-info">' . $row->running_count . '</span>';
                })
                ->addColumn('canceled_count', function ($row) {
                    return '<span class="badge bg-secondary">' . $row->canceled_count . '</span>';
                })
                ->addColumn('paused_count', function ($row) {
                    return '<span class="badge bg-dark">' . $row->paused_count . '</span>';
                })
                ->addColumn('completed_count', function ($row) {
                    return '<span class="badge bg-success">' . $row->completed_count . '</span>';
                })
                ->addColumn('total_tasks', function ($row) {
                    return '<span class="badge bg-primary">' . $row->total_tasks . '</span>';
                })
                ->with([
                    'total_pending' => $totals->total_pending,
                    'total_rejected' => $totals->total_rejected,
                    'total_running' => $totals->total_running,
                    'total_canceled' => $totals->total_canceled,
                    'total_paused' => $totals->total_paused,
                    'total_completed' => $totals->total_completed,
                    'total_tasks_sum' => $totals->total_tasks_sum,
                ])
                ->rawColumns(['user_id', 'user_name', 'pending_count', 'rejected_count', 'running_count', 'canceled_count', 'paused_count', 'completed_count', 'total_tasks'])
                ->make(true);
        }

        return view('backend.top_list.posted_task');
    }

    public function topWorkedTaskUser(Request $request)
    {
        if ($request->ajax()) {
            // Default date range: all data unless filtered
            $startDate = $request->start_date ?? null;
            $endDate = $request->end_date ?? null;

            // Base query for user task counts grouped by status
            $query = ProofTask::select(
                'proof_tasks.user_id',
                DB::raw('SUM(CASE WHEN proof_tasks.status = "Pending" THEN 1 ELSE 0 END) as total_pending'),
                DB::raw('SUM(CASE WHEN proof_tasks.status = "Rejected" THEN 1 ELSE 0 END) as total_rejected'),
                DB::raw('SUM(CASE WHEN proof_tasks.status = "Approved" THEN 1 ELSE 0 END) as total_approved'),
                DB::raw('SUM(CASE WHEN proof_tasks.status = "Reviewed" THEN 1 ELSE 0 END) as total_reviewed'),
                DB::raw('COUNT(proof_tasks.id) as total_tasks')
            );

            // Apply date filters only if provided
            if ($startDate && $endDate) {
                $query->whereBetween(DB::raw('DATE(proof_tasks.created_at)'), [$startDate, $endDate]);
            } elseif ($startDate) {
                $query->whereDate('proof_tasks.created_at', '>=', $startDate);
            } elseif ($endDate) {
                $query->whereDate('proof_tasks.created_at', '<=', $endDate);
            }

            // Query to calculate overall totals for all users
            $query->groupBy('proof_tasks.user_id')
                ->orderBy('total_tasks', 'desc');

            $topWorkedTaskUsers = $query->get();

            // Query to calculate overall totals for the filtered range
            $totalsQuery = DB::table('proof_tasks')->select(
                DB::raw('SUM(CASE WHEN proof_tasks.status = "Pending" THEN 1 ELSE 0 END) as total_pending'),
                DB::raw('SUM(CASE WHEN proof_tasks.status = "Rejected" THEN 1 ELSE 0 END) as total_rejected'),
                DB::raw('SUM(CASE WHEN proof_tasks.status = "Approved" THEN 1 ELSE 0 END) as total_approved'),
                DB::raw('SUM(CASE WHEN proof_tasks.status = "Reviewed" THEN 1 ELSE 0 END) as total_reviewed'),
                DB::raw('COUNT(proof_tasks.id) as total_tasks_sum')
            );

            // Apply the same date filters to the totals query
            if ($startDate && $endDate) {
                $totalsQuery->whereBetween(DB::raw('DATE(proof_tasks.created_at)'), [$startDate, $endDate]);
            } elseif ($startDate) {
                $totalsQuery->whereDate('proof_tasks.created_at', '>=', $startDate);
            } elseif ($endDate) {
                $totalsQuery->whereDate('proof_tasks.created_at', '<=', $endDate);
            }

            $totals = $totalsQuery->first();

            // Return the DataTables response with totals
            return DataTables::of($topWorkedTaskUsers)
                ->addIndexColumn()
                ->editColumn('user_id', function ($row) {
                    return '<span class="badge bg-primary">' . $row->user_id . '</span>';
                })
                ->editColumn('user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->user->id)) . '" class="text-primary" target="_blank">' . $row->user->name . '</a>
                        ';
                })
                ->addColumn('pending_count', function ($row) {
                    return '<span class="badge bg-warning">' . $row->total_pending . '</span>';
                })
                ->addColumn('rejected_count', function ($row) {
                    return '<span class="badge bg-danger">' . $row->total_rejected . '</span>';
                })
                ->addColumn('approved_count', function ($row) {
                    return '<span class="badge bg-success">' . $row->total_approved . '</span>';
                })
                ->addColumn('reviewed_count', function ($row) {
                    return '<span class="badge bg-info">' . $row->total_reviewed . '</span>';
                })
                ->editColumn('total_tasks', function ($row) {
                    return '<span class="badge bg-primary">' . $row->total_tasks . '</span>';
                })
                ->with([
                    'total_pending' => $totals->total_pending,
                    'total_rejected' => $totals->total_rejected,
                    'total_approved' => $totals->total_approved,
                    'total_reviewed' => $totals->total_reviewed,
                    'total_tasks_sum' => $totals->total_tasks_sum,
                ])
                ->rawColumns(['user_id', 'user_name', 'pending_count', 'rejected_count', 'approved_count', 'reviewed_count', 'total_tasks'])
                ->make(true);
        }

        return view('backend.top_list.worked_task');
    }

    public function topReferredUser(Request $request)
    {
        if ($request->ajax()) {
            // Default date range: all data unless filtered
            $startDate = $request->start_date ?? null;
            $endDate = $request->end_date ?? null;

            // Query to get referred counts by user and status
            $query = User::select(
                'users.id',
                'users.name',
                DB::raw("SUM(CASE WHEN referred_users.status = 'Active' THEN 1 ELSE 0 END) as active_count"),
                DB::raw("SUM(CASE WHEN referred_users.status = 'Inactive' THEN 1 ELSE 0 END) as inactive_count"),
                DB::raw("SUM(CASE WHEN referred_users.status = 'Blocked' THEN 1 ELSE 0 END) as blocked_count"),
                DB::raw("SUM(CASE WHEN referred_users.status = 'Banned' THEN 1 ELSE 0 END) as banned_count"),
                DB::raw('COUNT(referred_users.id) as total_referred')
            )
            ->leftJoin('users as referred_users', 'referred_users.referred_by', '=', 'users.id')
            ->whereNotNull('referred_users.referred_by'); // Only users with referrals

            // Apply date filters to the referred users
            if ($startDate && $endDate) {
                $query->whereBetween(DB::raw('DATE(referred_users.created_at)'), [$startDate, $endDate]);
            } elseif ($startDate) {
                $query->whereDate('referred_users.created_at', '>=', $startDate);
            } elseif ($endDate) {
                $query->whereDate('referred_users.created_at', '<=', $endDate);
            }

            $query->groupBy('users.id', 'users.name')
                ->orderBy('total_referred', 'desc');

            $topReferredUsers = $query->get();

            // Query to calculate overall totals for all users
            $totalsQuery = User::leftJoin('users as referred_users', 'referred_users.referred_by', '=', 'users.id')
                ->whereNotNull('referred_users.referred_by')
                ->select(
                    DB::raw("SUM(CASE WHEN referred_users.status = 'Active' THEN 1 ELSE 0 END) as active_count"),
                    DB::raw("SUM(CASE WHEN referred_users.status = 'Inactive' THEN 1 ELSE 0 END) as inactive_count"),
                    DB::raw("SUM(CASE WHEN referred_users.status = 'Blocked' THEN 1 ELSE 0 END) as blocked_count"),
                    DB::raw("SUM(CASE WHEN referred_users.status = 'Banned' THEN 1 ELSE 0 END) as banned_count"),
                    DB::raw('COUNT(referred_users.id) as total_referred')
                );

            // Apply the same date filters to totals query
            if ($startDate && $endDate) {
                $totalsQuery->whereBetween(DB::raw('DATE(referred_users.created_at)'), [$startDate, $endDate]);
            } elseif ($startDate) {
                $totalsQuery->whereDate('referred_users.created_at', '>=', $startDate);
            } elseif ($endDate) {
                $totalsQuery->whereDate('referred_users.created_at', '<=', $endDate);
            }

            $totals = $totalsQuery->first();

            // Return the DataTables response with totals
            return DataTables::of($topReferredUsers)
                ->addIndexColumn()
                ->addColumn('user_id', function ($row) {
                    return '<span class="badge bg-primary">' . $row->id . '</span>';
                })
                ->addColumn('user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->id)) . '" class="text-primary" target="_blank">' . $row->name . '</a>
                        ';
                })
                ->addColumn('active_count', function ($row) {
                    return '<span class="badge bg-success">' . $row->active_count . '</span>';
                })
                ->addColumn('inactive_count', function ($row) {
                    return '<span class="badge bg-warning">' . $row->inactive_count . '</span>';
                })
                ->addColumn('blocked_count', function ($row) {
                    return '<span class="badge bg-danger">' . $row->blocked_count . '</span>';
                })
                ->addColumn('banned_count', function ($row) {
                    return '<span class="badge bg-dark">' . $row->banned_count . '</span>';
                })
                ->addColumn('total_referred', function ($row) {
                    return '<span class="badge bg-primary">' . $row->total_referred . '</span>';
                })
                ->with([
                    'active_count' => $totals->active_count,
                    'inactive_count' => $totals->inactive_count,
                    'blocked_count' => $totals->blocked_count,
                    'banned_count' => $totals->banned_count,
                    'total_referred' => $totals->total_referred,
                ])
                ->rawColumns(['user_id', 'user_name', 'active_count', 'inactive_count', 'blocked_count', 'banned_count', 'total_referred'])
                ->make(true);
        }

        return view('backend.top_list.referred');
    }
}
