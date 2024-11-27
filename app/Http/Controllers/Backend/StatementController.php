<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deposit;
use App\Models\Withdraw;
use App\Models\Bonus;
use App\Models\Expense;
use App\Models\PostTask;
use App\Models\ProofTask;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;


class StatementController extends Controller
{
    public function earningsStatement(Request $request)
    {
        if ($request->ajax()) {
            // Fetch deposit charges grouped by date
            $queryDeposit = Deposit::where('status', 'Approved')
                ->select(
                    DB::raw('DATE(created_at) as earnings_date'),
                    DB::raw('(SUM(amount) - SUM(payable_amount)) as deposit_charge'),
                    DB::raw('0 as withdraw_charge'),
                    DB::raw('0 as post_task_charge') // Placeholder for union
                )
                ->groupBy(DB::raw('DATE(created_at)'));

            // Fetch withdraw charges grouped by date
            $queryWithdraw = Withdraw::where('status', 'Approved')
                ->select(
                    DB::raw('DATE(created_at) as earnings_date'),
                    DB::raw('0 as deposit_charge'),
                    DB::raw('(SUM(amount) - SUM(payable_amount)) as withdraw_charge'),
                    DB::raw('0 as post_task_charge') // Placeholder for union
                )
                ->groupBy(DB::raw('DATE(created_at)'));

            // Fetch Post Task Charges grouped by date
            $queryPostTaskCharge = ProofTask::join('post_tasks', 'proof_tasks.post_task_id', '=', 'post_tasks.id')
                ->whereNotIn('post_tasks.status', ['Pending', 'Rejected'])
                ->where('proof_tasks.status', 'Approved')
                ->select(
                    DB::raw('DATE(proof_tasks.created_at) as earnings_date'),
                    DB::raw('0 as deposit_charge'),
                    DB::raw('0 as withdraw_charge'),
                    DB::raw('SUM(
                        (post_tasks.site_charge / post_tasks.worker_needed) +
                        post_tasks.required_proof_photo_charge +
                        post_tasks.boosting_time_charge +
                        post_tasks.work_duration_charge
                    ) as post_task_charge')
                )
                ->groupBy(DB::raw('DATE(proof_tasks.created_at)'));

            // Merge queries
            $mergedQuery = DB::query()
                ->fromSub($queryDeposit->unionAll($queryWithdraw)->unionAll($queryPostTaskCharge), 'combined')
                ->select(
                    'earnings_date',
                    DB::raw('SUM(deposit_charge) as total_deposit_charge'),
                    DB::raw('SUM(withdraw_charge) as total_withdraw_charge'),
                    DB::raw('SUM(post_task_charge) as total_post_task_charge'),
                    DB::raw('SUM(deposit_charge + withdraw_charge + post_task_charge) as total_amount')
                )
                ->groupBy('earnings_date')
                ->orderBy('earnings_date', 'desc');

            // Apply date filters if provided
            if ($request->start_date) {
                $mergedQuery->whereDate('earnings_date', '>=', $request->start_date);
            }
            if ($request->end_date) {
                $mergedQuery->whereDate('earnings_date', '<=', $request->end_date);
            }

            // Fetch results
            $result = $mergedQuery->get();

            // Query to calculate the totals across all rows
            $totalQuery = DB::query()
                ->fromSub($mergedQuery, 'sub')
                ->select(
                    DB::raw('SUM(total_deposit_charge) as total_deposit_charge'),
                    DB::raw('SUM(total_withdraw_charge) as total_withdraw_charge'),
                    DB::raw('SUM(total_post_task_charge) as total_post_task_charge'),
                    DB::raw('SUM(total_amount) as total_amount')
                );

            // Date filter for totals query
            if ($request->start_date) {
                $totalQuery->whereDate('earnings_date', '>=', $request->start_date);
            }
            if ($request->end_date) {
                $totalQuery->whereDate('earnings_date', '<=', $request->end_date);
            }

            // Fetch totals
            $totals = $totalQuery->first();

            // Return DataTables response
            return DataTables::of($result)
                ->addIndexColumn()
                ->editColumn('earnings_date', function ($row) {
                    return '<span class="badge bg-primary">' . date('l j-F, Y', strtotime($row->earnings_date)) . '</span>';
                })
                ->editColumn('total_deposit_charge', function ($row) {
                    return '<span class="badge bg-dark">' . number_format($row->total_deposit_charge, 2) . '</span>';
                })
                ->editColumn('total_withdraw_charge', function ($row) {
                    return '<span class="badge bg-dark">' . number_format($row->total_withdraw_charge, 2) . '</span>';
                })
                ->editColumn('total_post_task_charge', function ($row) {
                    return '<span class="badge bg-warning">' . number_format($row->total_post_task_charge, 2) . '</span>';
                })
                ->editColumn('total_amount', function ($row) {
                    return '<span class="badge bg-success">' . number_format($row->total_amount, 2) . '</span>';
                })
                ->with([
                    'total_deposit_charge_sum' => get_site_settings('site_currency_symbol') . ' ' . number_format($totals->total_deposit_charge, 2),
                    'total_withdraw_charge_sum' => get_site_settings('site_currency_symbol') . ' ' . number_format($totals->total_withdraw_charge, 2),
                    'total_post_task_charge_sum' => get_site_settings('site_currency_symbol') . ' ' . number_format($totals->total_post_task_charge, 2),
                    'total_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . number_format($totals->total_amount, 2),
                ])
                ->rawColumns(['earnings_date', 'total_deposit_charge', 'total_withdraw_charge', 'total_post_task_charge', 'total_amount'])
                ->make(true);
        }

        return view('backend.statement.earnings');
    }

    public function expensesStatement(Request $request)
    {
        if ($request->ajax()) {
            // Query for Expense totals grouped by date
            $queryExpense = Expense::where('status', 'Active')
                ->select(
                    DB::raw('DATE(expense_date) as expenses_date'),
                    DB::raw('SUM(amount) as total_expense'),
                    DB::raw('0 as total_bonus') // Placeholder for union
                )
                ->groupBy(DB::raw('DATE(expense_date)'));

            // Query for Bonus totals grouped by date (excluding specific type)
            $queryBonus = Bonus::where('type', '!=', 'Proof Task Approved Bonus')
                ->select(
                    DB::raw('DATE(created_at) as expenses_date'),
                    DB::raw('0 as total_expense'), // Placeholder for union
                    DB::raw('SUM(amount) as total_bonus')
                )
                ->groupBy(DB::raw('DATE(created_at)'));

            // Merge Expense and Bonus queries
            $mergedQuery = DB::query()
                ->fromSub($queryExpense->unionAll($queryBonus), 'combined')
                ->select(
                    'expenses_date',
                    DB::raw('SUM(total_expense) as total_expense_amount'),
                    DB::raw('SUM(total_bonus) as total_bonus_amount'),
                    DB::raw('SUM(total_expense + total_bonus) as total_amount')
                )
                ->groupBy('expenses_date')
                ->orderBy('expenses_date', 'desc');

            // Apply date filters if provided
            if ($request->start_date) {
                $mergedQuery->whereDate('expenses_date', '>=', $request->start_date);
            }
            if ($request->end_date) {
                $mergedQuery->whereDate('expenses_date', '<=', $request->end_date);
            }

            // Fetch results
            $result = $mergedQuery->get();

            // Query to calculate the totals across all rows
            $totalQuery = DB::query()
                ->fromSub($mergedQuery, 'sub')
                ->select(
                    DB::raw('SUM(total_expense_amount) as total_expense_amount'),
                    DB::raw('SUM(total_bonus_amount) as total_bonus_amount'),
                    DB::raw('SUM(total_amount) as total_amount')
                );

            // Date filter for totals query
            if ($request->start_date) {
                $totalQuery->whereDate('expenses_date', '>=', $request->start_date);
            }
            if ($request->end_date) {
                $totalQuery->whereDate('expenses_date', '<=', $request->end_date);
            }

            // Fetch totals
            $totals = $totalQuery->first();

            // Return DataTables response
            return DataTables::of($result)
                ->addIndexColumn()
                ->editColumn('expenses_date', function ($row) {
                    return '<span class="badge bg-primary">' . date('l j-F, Y', strtotime($row->expenses_date)) . '</span>';
                })
                ->editColumn('total_expense_amount', function ($row) {
                    return '<span class="badge bg-danger">' . number_format($row->total_expense_amount, 2) . '</span>';
                })
                ->editColumn('total_bonus_amount', function ($row) {
                    return '<span class="badge bg-warning">' . number_format($row->total_bonus_amount, 2) . '</span>';
                })
                ->editColumn('total_amount', function ($row) {
                    return '<span class="badge bg-success">' . number_format($row->total_amount, 2) . '</span>';
                })
                ->with([
                    'total_expense_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . number_format($totals->total_expense_amount, 2),
                    'total_bonus_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . number_format($totals->total_bonus_amount, 2),
                    'total_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . number_format($totals->total_amount, 2),
                ])
                ->rawColumns(['expenses_date', 'total_expense_amount', 'total_bonus_amount', 'total_amount'])
                ->make(true);
        }

        return view('backend.statement.expenses');
    }

}
