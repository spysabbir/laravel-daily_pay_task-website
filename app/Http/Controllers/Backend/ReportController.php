<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use Illuminate\Http\Request;
use App\Models\Deposit;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PostTask;
use App\Models\ProofTask;
use App\Models\Withdraw;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function depositReport(Request $request)
    {
        if ($request->ajax()) {
            // Query to get individual row data
            $query = Deposit::select(
                DB::raw('DATE(deposits.created_at) as deposit_date'),
                DB::raw('SUM(CASE WHEN deposits.method = "Bkash" THEN deposits.amount ELSE 0 END) as bkash_amount'),
                DB::raw('SUM(CASE WHEN deposits.method = "Nagad" THEN deposits.amount ELSE 0 END) as nagad_amount'),
                DB::raw('SUM(CASE WHEN deposits.method = "Rocket" THEN deposits.amount ELSE 0 END) as rocket_amount'),
                DB::raw('SUM(CASE WHEN deposits.method = "Withdrawal Balance" THEN deposits.amount ELSE 0 END) as withdrawal_balance_amount'),
                DB::raw('SUM(CASE WHEN deposits.status = "Pending" THEN deposits.amount ELSE 0 END) as pending_amount'),
                DB::raw('SUM(CASE WHEN deposits.status = "Approved" THEN deposits.amount ELSE 0 END) as approved_amount'),
                DB::raw('SUM(CASE WHEN deposits.status = "Rejected" THEN deposits.amount ELSE 0 END) as rejected_amount'),
                DB::raw('SUM(deposits.payable_amount) as total_payable_amount'),
                DB::raw('(SUM(deposits.amount) - SUM(deposits.payable_amount)) as deposit_charge'),
                DB::raw('SUM(deposits.amount) as total_amount'),
            );

            // Apply filters based on the request input
            if ($request->method) {
                $query->where('deposits.method', $request->method);
            }

            if ($request->status) {
                $query->where('deposits.status', $request->status);
            }

            // Date filter: handle cases where either start_date or end_date is provided
            if ($request->start_date && !$request->end_date) {
                // Only start_date is selected
                $query->whereDate('deposits.created_at', '>=', $request->start_date);
            } elseif (!$request->start_date && $request->end_date) {
                // Only end_date is selected
                $query->whereDate('deposits.created_at', '<=', $request->end_date);
            } elseif ($request->start_date && $request->end_date) {
                // Both start_date and end_date are selected
                $query->whereBetween(DB::raw('DATE(deposits.created_at)'), [$request->start_date, $request->end_date]);
            }

            $query->groupBy(DB::raw('DATE(deposits.created_at)'));
            $query->orderBy('deposit_date', 'desc');

            // Retrieve the row-level data
            $pendingRequest = $query->get();

            // Query to calculate the totals across all rows
            $totalsQuery = Deposit::select(
                DB::raw('SUM(CASE WHEN deposits.method = "Bkash" THEN deposits.amount ELSE 0 END) as total_bkash_amount_sum'),
                DB::raw('SUM(CASE WHEN deposits.method = "Nagad" THEN deposits.amount ELSE 0 END) as total_nagad_amount_sum'),
                DB::raw('SUM(CASE WHEN deposits.method = "Rocket" THEN deposits.amount ELSE 0 END) as total_rocket_amount_sum'),
                DB::raw('SUM(CASE WHEN deposits.method = "Withdrawal Balance" THEN deposits.amount ELSE 0 END) as total_withdrawal_balance_amount_sum'),
                DB::raw('SUM(CASE WHEN deposits.status = "Pending" THEN deposits.amount ELSE 0 END) as total_pending_amount_sum'),
                DB::raw('SUM(CASE WHEN deposits.status = "Approved" THEN deposits.amount ELSE 0 END) as total_approved_amount_sum'),
                DB::raw('SUM(CASE WHEN deposits.status = "Rejected" THEN deposits.amount ELSE 0 END) as total_rejected_amount_sum'),
                DB::raw('SUM(deposits.payable_amount) as total_payable_amount_sum'),
                DB::raw('(SUM(deposits.amount) - SUM(deposits.payable_amount)) as deposit_charge_sum'),
                DB::raw('SUM(deposits.amount) as total_amount_sum'),
            );

            // Apply the same filters to the totals query
            if ($request->method) {
                $totalsQuery->where('deposits.method', $request->method);
            }

            if ($request->status) {
                $totalsQuery->where('deposits.status', $request->status);
            }

            // Date filter for totals query
            if ($request->start_date && !$request->end_date) {
                $totalsQuery->whereDate('deposits.created_at', '>=', $request->start_date);
            } elseif (!$request->start_date && $request->end_date) {
                $totalsQuery->whereDate('deposits.created_at', '<=', $request->end_date);
            } elseif ($request->start_date && $request->end_date) {
                $totalsQuery->whereBetween(DB::raw('DATE(deposits.created_at)'), [$request->start_date, $request->end_date]);
            }

            // Get the total values
            $totals = $totalsQuery->first();

            // Return the DataTables response with totals
            return DataTables::of($pendingRequest)
                ->addIndexColumn()
                ->editColumn('deposit_date', function ($row) {
                    return '<span class="badge bg-primary">' . date('l j-F, Y', strtotime($row->deposit_date)) . '</span>';
                })
                ->editColumn('bkash_amount', function ($row) {
                    return '<span class="badge bg-dark">' . $row->bkash_amount . '</span>';
                })
                ->editColumn('nagad_amount', function ($row) {
                    return '<span class="badge bg-dark">' . $row->nagad_amount . '</span>';
                })
                ->editColumn('rocket_amount', function ($row) {
                    return '<span class="badge bg-dark">' . $row->rocket_amount . '</span>';
                })
                ->editColumn('withdrawal_balance_amount', function ($row) {
                    return '<span class="badge bg-dark">' . $row->withdrawal_balance_amount . '</span>';
                })
                ->editColumn('pending_amount', function ($row) {
                    return '<span class="badge bg-warning">' . $row->pending_amount . '</span>';
                })
                ->editColumn('approved_amount', function ($row) {
                    return '<span class="badge bg-success">' . $row->approved_amount . '</span>';
                })
                ->editColumn('rejected_amount', function ($row) {
                    return '<span class="badge bg-danger">' . $row->rejected_amount . '</span>';
                })
                ->editColumn('total_payable_amount', function ($row) {
                    return '<span class="badge bg-primary">' . $row->total_payable_amount . '</span>';
                })
                ->editColumn('deposit_charge', function ($row) {
                    return '<span class="badge bg-info">' . $row->deposit_charge . '</span>';
                })
                ->editColumn('total_amount', function ($row) {
                    return '<span class="badge bg-primary">' . $row->total_amount . '</span>';
                })
                ->with([
                    'total_bkash_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_bkash_amount_sum,
                    'total_nagad_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_nagad_amount_sum,
                    'total_rocket_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_rocket_amount_sum,
                    'total_withdrawal_balance_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_withdrawal_balance_amount_sum,
                    'total_pending_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_pending_amount_sum,
                    'total_approved_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_approved_amount_sum,
                    'total_rejected_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_rejected_amount_sum,
                    'total_payable_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_payable_amount_sum,
                    'deposit_charge_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->deposit_charge_sum,
                    'total_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_amount_sum,
                ])
                ->rawColumns(['deposit_date', 'bkash_amount', 'nagad_amount', 'rocket_amount', 'withdrawal_balance_amount', 'pending_amount', 'approved_amount', 'rejected_amount', 'total_payable_amount', 'deposit_charge', 'total_amount'])
                ->make(true);
        }

        return view('backend.report.deposit');
    }

    public function withdrawReport(Request $request)
    {
        if ($request->ajax()) {
            // Query to get individual row data
            $query = Withdraw::select(
                DB::raw('DATE(withdraws.created_at) as withdraw_date'),
                DB::raw('SUM(CASE WHEN withdraws.method = "Bkash" THEN withdraws.amount ELSE 0 END) as bkash_amount'),
                DB::raw('SUM(CASE WHEN withdraws.method = "Nagad" THEN withdraws.amount ELSE 0 END) as nagad_amount'),
                DB::raw('SUM(CASE WHEN withdraws.method = "Rocket" THEN withdraws.amount ELSE 0 END) as rocket_amount'),
                DB::raw('SUM(CASE WHEN withdraws.status = "Pending" THEN withdraws.amount ELSE 0 END) as pending_amount'),
                DB::raw('SUM(CASE WHEN withdraws.status = "Approved" THEN withdraws.amount ELSE 0 END) as approved_amount'),
                DB::raw('SUM(CASE WHEN withdraws.status = "Rejected" THEN withdraws.amount ELSE 0 END) as rejected_amount'),
                DB::raw('SUM(CASE WHEN withdraws.type = "Ragular" THEN withdraws.amount ELSE 0 END) as ragular_amount'),
                DB::raw('SUM(CASE WHEN withdraws.type = "Instant" THEN withdraws.amount ELSE 0 END) as instant_amount'),
                DB::raw('SUM(withdraws.payable_amount) as total_payable_amount'),
                DB::raw('(SUM(withdraws.amount) - SUM(withdraws.payable_amount)) as withdraw_charge'),
                DB::raw('SUM(withdraws.amount) as total_amount'),
            );

            // Apply filters based on the request input
            if ($request->type) {
                $query->where('withdraws.type', $request->type);
            }

            if ($request->method) {
                $query->where('withdraws.method', $request->method);
            }

            if ($request->status) {
                $query->where('withdraws.status', $request->status);
            }

            // Date filter: handle cases where either start_date or end_date is provided
            if ($request->start_date && !$request->end_date) {
                // Only start_date is selected
                $query->whereDate('withdraws.created_at', '>=', $request->start_date);
            } elseif (!$request->start_date && $request->end_date) {
                // Only end_date is selected
                $query->whereDate('withdraws.created_at', '<=', $request->end_date);
            } elseif ($request->start_date && $request->end_date) {
                // Both start_date and end_date are selected
                $query->whereBetween(DB::raw('DATE(withdraws.created_at)'), [$request->start_date, $request->end_date]);
            }

            $query->groupBy(DB::raw('DATE(withdraws.created_at)'));
            $query->orderBy('withdraw_date', 'desc');

            // Retrieve the row-level data
            $pendingRequest = $query->get();

            // Query to calculate the totals across all rows
            $totalsQuery = Withdraw::select(
                DB::raw('SUM(CASE WHEN withdraws.method = "Bkash" THEN withdraws.amount ELSE 0 END) as total_bkash_amount_sum'),
                DB::raw('SUM(CASE WHEN withdraws.method = "Nagad" THEN withdraws.amount ELSE 0 END) as total_nagad_amount_sum'),
                DB::raw('SUM(CASE WHEN withdraws.method = "Rocket" THEN withdraws.amount ELSE 0 END) as total_rocket_amount_sum'),
                DB::raw('SUM(CASE WHEN withdraws.status = "Pending" THEN withdraws.amount ELSE 0 END) as total_pending_amount_sum'),
                DB::raw('SUM(CASE WHEN withdraws.status = "Approved" THEN withdraws.amount ELSE 0 END) as total_approved_amount_sum'),
                DB::raw('SUM(CASE WHEN withdraws.status = "Rejected" THEN withdraws.amount ELSE 0 END) as total_rejected_amount_sum'),
                DB::raw('SUM(CASE WHEN withdraws.type = "Ragular" THEN withdraws.amount ELSE 0 END) as total_ragular_amount_sum'),
                DB::raw('SUM(CASE WHEN withdraws.type = "Instant" THEN withdraws.amount ELSE 0 END) as total_instant_amount_sum'),
                DB::raw('SUM(withdraws.payable_amount) as total_payable_amount_sum'),
                DB::raw('(SUM(withdraws.amount) - SUM(withdraws.payable_amount)) as withdraw_charge_sum'),
                DB::raw('SUM(withdraws.amount) as total_amount_sum'),
            );

            // Apply the same filters to the totals query
            if ($request->type) {
                $totalsQuery->where('withdraws.type', $request->type);
            }

            if ($request->method) {
                $totalsQuery->where('withdraws.method', $request->method);
            }

            if ($request->status) {
                $totalsQuery->where('withdraws.status', $request->status);
            }

            // Date filter for totals query
            if ($request->start_date && !$request->end_date) {
                $totalsQuery->whereDate('withdraws.created_at', '>=', $request->start_date);
            } elseif (!$request->start_date && $request->end_date) {
                $totalsQuery->whereDate('withdraws.created_at', '<=', $request->end_date);
            } elseif ($request->start_date && $request->end_date) {
                $totalsQuery->whereBetween(DB::raw('DATE(withdraws.created_at)'), [$request->start_date, $request->end_date]);
            }

            // Get the total values
            $totals = $totalsQuery->first();

            // Return the DataTables response with totals
            return DataTables::of($pendingRequest)
                ->addIndexColumn()
                ->editColumn('withdraw_date', function ($row) {
                    return '<span class="badge bg-primary">' . date('l j-F, Y', strtotime($row->withdraw_date)) . '</span>';
                })
                ->editColumn('bkash_amount', function ($row) {
                    return '<span class="badge bg-dark">' . $row->bkash_amount . '</span>';
                })
                ->editColumn('nagad_amount', function ($row) {
                    return '<span class="badge bg-dark">' . $row->nagad_amount . '</span>';
                })
                ->editColumn('rocket_amount', function ($row) {
                    return '<span class="badge bg-dark">' . $row->rocket_amount . '</span>';
                })
                ->editColumn('pending_amount', function ($row) {
                    return '<span class="badge bg-warning">' . $row->pending_amount . '</span>';
                })
                ->editColumn('approved_amount', function ($row) {
                    return '<span class="badge bg-success">' . $row->approved_amount . '</span>';
                })
                ->editColumn('rejected_amount', function ($row) {
                    return '<span class="badge bg-danger">' . $row->rejected_amount . '</span>';
                })
                ->editColumn('ragular_amount', function ($row) {
                    return '<span class="badge bg-primary">' . $row->ragular_amount . '</span>';
                })
                ->editColumn('instant_amount', function ($row) {
                    return '<span class="badge bg-primary">' . $row->instant_amount . '</span>';
                })
                ->editColumn('total_payable_amount', function ($row) {
                    return '<span class="badge bg-primary">' . $row->total_payable_amount . '</span>';
                })
                ->editColumn('withdraw_charge', function ($row) {
                    return '<span class="badge bg-info">' . $row->withdraw_charge . '</span>';
                })
                ->editColumn('total_amount', function ($row) {
                    return '<span class="badge bg-primary">' . $row->total_amount . '</span>';
                })
                ->with([
                    'total_bkash_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_bkash_amount_sum,
                    'total_nagad_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_nagad_amount_sum,
                    'total_rocket_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_rocket_amount_sum,
                    'total_pending_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_pending_amount_sum,
                    'total_approved_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_approved_amount_sum,
                    'total_rejected_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_rejected_amount_sum,
                    'total_ragular_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_ragular_amount_sum,
                    'total_instant_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_instant_amount_sum,
                    'total_payable_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_payable_amount_sum,
                    'withdraw_charge_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->withdraw_charge_sum,
                    'total_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_amount_sum,
                ])
                ->rawColumns(['withdraw_date', 'bkash_amount', 'nagad_amount', 'rocket_amount', 'pending_amount', 'approved_amount', 'rejected_amount', 'ragular_amount', 'instant_amount', 'total_payable_amount', 'withdraw_charge', 'total_amount'])
                ->make(true);
        }

        return view('backend.report.withdraw');
    }

    public function bonusReport(Request $request)
    {
        if ($request->ajax()) {
            // Query to get individual row data
            $query = Bonus::select(
                DB::raw('DATE(bonuses.created_at) as bonus_date'),
                DB::raw('SUM(CASE WHEN bonuses.type = "Referral Registration Bonus" THEN bonuses.amount ELSE 0 END) as referral_registration_bonus_amount'),
                DB::raw('SUM(CASE WHEN bonuses.type = "Referral Withdrawal Bonus" THEN bonuses.amount ELSE 0 END) as referral_withdrawal_bonus_amount'),
                DB::raw('SUM(CASE WHEN bonuses.type = "Proof Task Approved Bonus" THEN bonuses.amount ELSE 0 END) as proof_task_approved_bonus_amount'),
                DB::raw('SUM(CASE WHEN bonuses.type != "Proof Task Approved Bonus" THEN bonuses.amount ELSE 0 END) as site_bonus_amount'),
                DB::raw('SUM(CASE WHEN bonuses.type = "Proof Task Approved Bonus" THEN bonuses.amount ELSE 0 END) as user_bonus_amount'),
                DB::raw('SUM(bonuses.amount) as total_amount'),
            );

            // Apply filters based on the request input
            if ($request->type) {
                $query->where('bonuses.type', $request->type);
            }

            // Date filter: handle cases where either start_date or end_date is provided
            if ($request->start_date && !$request->end_date) {
                $query->whereDate('bonuses.created_at', '>=', $request->start_date);
            } elseif (!$request->start_date && $request->end_date) {
                $query->whereDate('bonuses.created_at', '<=', $request->end_date);
            } elseif ($request->start_date && $request->end_date) {
                $query->whereBetween(DB::raw('DATE(bonuses.created_at)'), [$request->start_date, $request->end_date]);
            }

            $query->groupBy(DB::raw('DATE(bonuses.created_at)'));
            $query->orderBy('bonus_date', 'desc');

            // Retrieve the row-level data
            $pendingRequest = $query->get();

            // Query to calculate the totals across all rows
            $totalsQuery = Bonus::select(
                DB::raw('SUM(CASE WHEN bonuses.type = "Referral Registration Bonus" THEN bonuses.amount ELSE 0 END) as total_referral_registration_bonus_amount_sum'),
                DB::raw('SUM(CASE WHEN bonuses.type = "Referral Withdrawal Bonus" THEN bonuses.amount ELSE 0 END) as total_referral_withdrawal_bonus_amount_sum'),
                DB::raw('SUM(CASE WHEN bonuses.type = "Proof Task Approved Bonus" THEN bonuses.amount ELSE 0 END) as total_proof_task_approved_bonus_amount_sum'),
                DB::raw('SUM(CASE WHEN bonuses.type != "Proof Task Approved Bonus" THEN bonuses.amount ELSE 0 END) as total_site_bonus_amount_sum'),
                DB::raw('SUM(CASE WHEN bonuses.type = "Proof Task Approved Bonus" THEN bonuses.amount ELSE 0 END) as total_user_bonus_amount_sum'),
                DB::raw('SUM(bonuses.amount) as total_amount_sum'),
            );

            // Apply the same filters to the totals query
            if ($request->type) {
                $totalsQuery->where('bonuses.type', $request->type);
            }

            // Date filter for totals query
            if ($request->start_date && !$request->end_date) {
                $totalsQuery->whereDate('bonuses.created_at', '>=', $request->start_date);
            } elseif (!$request->start_date && $request->end_date) {
                $totalsQuery->whereDate('bonuses.created_at', '<=', $request->end_date);
            } elseif ($request->start_date && $request->end_date) {
                $totalsQuery->whereBetween(DB::raw('DATE(bonuses.created_at)'), [$request->start_date, $request->end_date]);
            }

            // Get the total values
            $totals = $totalsQuery->first();

            // Return the DataTables response with totals
            return DataTables::of($pendingRequest)
                ->addIndexColumn()
                ->editColumn('bonus_date', function ($row) {
                    return '<span class="badge bg-primary">' . date('l j-F, Y', strtotime($row->bonus_date)) . '</span>';
                })
                ->editColumn('referral_registration_bonus_amount', function ($row) {
                    return '<span class="badge bg-dark">' . $row->referral_registration_bonus_amount . '</span>';
                })
                ->editColumn('referral_withdrawal_bonus_amount', function ($row) {
                    return '<span class="badge bg-dark">' . $row->referral_withdrawal_bonus_amount . '</span>';
                })
                ->editColumn('proof_task_approved_bonus_amount', function ($row) {
                    return '<span class="badge bg-dark">' . $row->proof_task_approved_bonus_amount . '</span>';
                })
                ->editColumn('site_bonus_amount', function ($row) {
                    return '<span class="badge bg-success">' . $row->site_bonus_amount . '</span>';
                })
                ->editColumn('user_bonus_amount', function ($row) {
                    return '<span class="badge bg-info">' . $row->user_bonus_amount . '</span>';
                })
                ->editColumn('total_amount', function ($row) {
                    return '<span class="badge bg-primary">' . $row->total_amount . '</span>';
                })
                ->with([
                    'total_referral_registration_bonus_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_referral_registration_bonus_amount_sum,
                    'total_referral_withdrawal_bonus_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_referral_withdrawal_bonus_amount_sum,
                    'total_proof_task_approved_bonus_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_proof_task_approved_bonus_amount_sum,
                    'total_site_bonus_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_site_bonus_amount_sum,
                    'total_user_bonus_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_user_bonus_amount_sum,
                    'total_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_amount_sum,
                ])
                ->rawColumns(['bonus_date', 'referral_registration_bonus_amount', 'referral_withdrawal_bonus_amount', 'proof_task_approved_bonus_amount', 'site_bonus_amount', 'user_bonus_amount', 'total_amount'])
                ->make(true);
        }

        return view('backend.report.bonus');
    }

    public function expenseReport(Request $request)
    {
        if ($request->ajax()) {
            $categories = ExpenseCategory::where('status', 'Active')->get();
            // Query to get individual row data
            $query = Expense::select(
                DB::raw('DATE(expenses.expense_date) as expense_date'),
                DB::raw('SUM(CASE WHEN expenses.status = "Active" THEN expenses.amount ELSE 0 END) as active_amount'),
                DB::raw('SUM(CASE WHEN expenses.status = "Inactive" THEN expenses.amount ELSE 0 END) as inactive_amount'),
                DB::raw('SUM(expenses.amount) as total_amount'),
            );

            foreach ($categories as $category) {
                $query->addSelect(DB::raw(
                    'SUM(CASE WHEN expenses.expense_category_id = ' . $category->id . ' THEN expenses.amount ELSE 0 END) as ' . Str::slug($category->name, '_') . '_amount'
                ));
            }

            // Apply filters based on the request input
            if ($request->expense_category_id) {
                $query->where('expenses.expense_category_id', $request->expense_category_id);
            }

            if ($request->status) {
                $query->where('expenses.status', $request->status);
            }

            // Date filter: handle cases where either start_date or end_date is provided
            if ($request->start_date && !$request->end_date) {
                $query->whereDate('expenses.expense_date', '>=', $request->start_date);
            } elseif (!$request->start_date && $request->end_date) {
                $query->whereDate('expenses.expense_date', '<=', $request->end_date);
            } elseif ($request->start_date && $request->end_date) {
                $query->whereBetween(DB::raw('DATE(expenses.expense_date)'), [$request->start_date, $request->end_date]);
            }

            $query->groupBy(DB::raw('DATE(expenses.expense_date)'));
            $query->orderBy('expense_date', 'desc');

            // Retrieve the row-level data
            $pendingRequest = $query->get();

            // Query to calculate the totals across all rows
            $totalsQuery = Expense::select(
                DB::raw('SUM(CASE WHEN expenses.status = "Active" THEN expenses.amount ELSE 0 END) as total_active_amount_sum'),
                DB::raw('SUM(CASE WHEN expenses.status = "Inactive" THEN expenses.amount ELSE 0 END) as total_inactive_amount_sum'),
                DB::raw('SUM(expenses.amount) as total_amount_sum'),
            );

            foreach ($categories as $category) {
                $totalsQuery->addSelect(DB::raw(
                    'SUM(CASE WHEN expenses.expense_category_id = ' . $category->id . ' THEN expenses.amount ELSE 0 END) as total_' . Str::slug($category->name, '_') . '_amount_sum'
                ));
            }

            // Apply the same filters to the totals query
            if ($request->expense_category_id) {
                $totalsQuery->where('expenses.expense_category_id', $request->expense_category_id);
            }

            if ($request->status) {
                $totalsQuery->where('expenses.status', $request->status);
            }

            // Date filter for totals query
            if ($request->start_date && !$request->end_date) {
                $totalsQuery->whereDate('expenses.expense_date', '>=', $request->start_date);
            } elseif (!$request->start_date && $request->end_date) {
                $totalsQuery->whereDate('expenses.expense_date', '<=', $request->end_date);
            } elseif ($request->start_date && $request->end_date) {
                $totalsQuery->whereBetween(DB::raw('DATE(expenses.expense_date)'), [$request->start_date, $request->end_date]);
            }

            // Get the total values
            $totals = $totalsQuery->first();

            // Return the DataTables response with totals
            return DataTables::of($pendingRequest)
                ->addIndexColumn()
                ->editColumn('expense_date', function ($row) {
                    return '<span class="badge bg-primary">' . date('l j-F, Y', strtotime($row->expense_date)) . '</span>';
                })
                ->editColumn('active_amount', function ($row) {
                    return '<span class="badge bg-success">' . $row->active_amount . '</span>';
                })
                ->editColumn('inactive_amount', function ($row) {
                    return '<span class="badge bg-danger">' . $row->inactive_amount . '</span>';
                })
                ->editColumn('total_amount', function ($row) {
                    return '<span class="badge bg-primary">' . $row->total_amount . '</span>';
                })
                ->with([
                    'category_totals' => $categories->map(function ($category) use ($totals) {
                    $categoryKey = 'total_' . Str::slug($category->name, '_') . '_amount_sum';
                        return [
                            'category' => $category->name,
                            'total' => get_site_settings('site_currency_symbol') . ' ' . $totals->{$categoryKey},
                        ];
                    }),
                    'total_active_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_active_amount_sum,
                    'total_inactive_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_inactive_amount_sum,
                    'total_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_amount_sum,
                ])
                ->rawColumns(['expense_date', 'active_amount', 'inactive_amount', 'total_amount'])
                ->make(true);
        }

        $expenseCategories = ExpenseCategory::where('status', 'Active')->get();
        return view('backend.report.expense', compact('expenseCategories'));
    }

    public function postedTaskReport(Request $request)
    {
        if ($request->ajax()) {
            // Query to get individual row data
            $query = PostTask::select(
                DB::raw('DATE(post_tasks.created_at) as posted_date'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Pending" THEN 1 ELSE 0 END) as pending_count'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Rejected" THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Running" THEN 1 ELSE 0 END) as running_count'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Canceled" THEN 1 ELSE 0 END) as canceled_count'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Paused" THEN 1 ELSE 0 END) as paused_count'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Completed" THEN 1 ELSE 0 END) as completed_count'),
                DB::raw('COUNT(post_tasks.id) as total_task_count'),
                DB::raw('SUM(post_tasks.charge) as posted_charge'),
                DB::raw('SUM(post_tasks.site_charge) as site_charge'),
                DB::raw('SUM(post_tasks.total_charge) as total_charge')
            );

            // Apply filters based on the request input
            if ($request->status) {
                $query->where('post_tasks.status', $request->status);
            }

            // Date filter: handle cases where either start_date or end_date is provided
            if ($request->start_date && !$request->end_date) {
                $query->whereDate('post_tasks.created_at', '>=', $request->start_date);
            } elseif (!$request->start_date && $request->end_date) {
                $query->whereDate('post_tasks.created_at', '<=', $request->end_date);
            } elseif ($request->start_date && $request->end_date) {
                $query->whereBetween(DB::raw('DATE(post_tasks.created_at)'), [$request->start_date, $request->end_date]);
            }

            $query->groupBy(DB::raw('DATE(post_tasks.created_at)'));
            $query->orderBy('posted_date', 'desc');

            // Retrieve the row-level data
            $pendingRequest = $query->get();

            // Query to calculate the totals across all rows
            $totalsQuery = PostTask::select(
                DB::raw('SUM(CASE WHEN post_tasks.status = "Pending" THEN 1 ELSE 0 END) as total_pending_count_sum'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Rejected" THEN 1 ELSE 0 END) as total_rejected_count_sum'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Running" THEN 1 ELSE 0 END) as total_running_count_sum'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Canceled" THEN 1 ELSE 0 END) as total_canceled_count_sum'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Paused" THEN 1 ELSE 0 END) as total_paused_count_sum'),
                DB::raw('SUM(CASE WHEN post_tasks.status = "Completed" THEN 1 ELSE 0 END) as total_completed_count_sum'),
                DB::raw('COUNT(post_tasks.id) as total_task_count_sum'),
                DB::raw('SUM(post_tasks.charge) as total_posted_charge_sum'),
                DB::raw('SUM(post_tasks.site_charge) as total_site_charge_sum'),
                DB::raw('SUM(post_tasks.total_charge) as total_charge_sum')
            );

            // Apply the same filters to the totals query
            if ($request->status) {
                $totalsQuery->where('post_tasks.status', $request->status);
            }

            // Date filter for totals query
            if ($request->start_date && !$request->end_date) {
                $totalsQuery->whereDate('post_tasks.created_at', '>=', $request->start_date);
            } elseif (!$request->start_date && $request->end_date) {
                $totalsQuery->whereDate('post_tasks.created_at', '<=', $request->end_date);
            } elseif ($request->start_date && $request->end_date) {
                $totalsQuery->whereBetween(DB::raw('DATE(post_tasks.created_at)'), [$request->start_date, $request->end_date]);
            }

            // Get the total values
            $totals = $totalsQuery->first();

            // Return the DataTables response with totals
            return DataTables::of($pendingRequest)
                ->addIndexColumn()
                ->editColumn('posted_date', function ($row) {
                    return '<span class="badge bg-primary">' . date('l j-F, Y', strtotime($row->posted_date)) . '</span>';
                })
                ->editColumn('pending_count', function ($row) {
                    return '<span class="badge bg-info">' . $row->pending_count . '</span>';
                })
                ->editColumn('rejected_count', function ($row) {
                    return '<span class="badge bg-warning">' . $row->rejected_count . '</span>';
                })
                ->editColumn('running_count', function ($row) {
                    return '<span class="badge bg-dark">' . $row->running_count . '</span>';
                })
                ->editColumn('canceled_count', function ($row) {
                    return '<span class="badge bg-danger">' . $row->canceled_count . '</span>';
                })
                ->editColumn('paused_count', function ($row) {
                    return '<span class="badge bg-secondary">' . $row->paused_count . '</span>';
                })
                ->editColumn('completed_count', function ($row) {
                    return '<span class="badge bg-success">' . $row->completed_count . '</span>';
                })
                ->editColumn('total_task_count', function ($row) {
                    return '<span class="badge bg-success">' . $row->total_task_count . '</span>';
                })
                ->editColumn('posted_charge', function ($row) {
                    return '<span class="badge bg-primary">' . $row->posted_charge . '</span>';
                })
                ->editColumn('site_charge', function ($row) {
                    return '<span class="badge bg-primary">' . $row->site_charge . '</span>';
                })
                ->editColumn('total_charge', function ($row) {
                    return '<span class="badge bg-primary">' . $row->total_charge . '</span>';
                })
                ->with([
                    'total_pending_count_sum' => $totals->total_pending_count_sum,
                    'total_rejected_count_sum' => $totals->total_rejected_count_sum,
                    'total_running_count_sum' => $totals->total_running_count_sum,
                    'total_canceled_count_sum' => $totals->total_canceled_count_sum,
                    'total_paused_count_sum' => $totals->total_paused_count_sum,
                    'total_completed_count_sum' => $totals->total_completed_count_sum,
                    'total_task_count_sum' => $totals->total_task_count_sum,
                    'total_posted_charge_sum' => get_site_settings('site_currency_symbol') . ' ' . number_format($totals->total_posted_charge_sum, 2),
                    'total_site_charge_sum' => get_site_settings('site_currency_symbol') . ' ' . number_format($totals->total_site_charge_sum, 2),
                    'total_charge_sum' => get_site_settings('site_currency_symbol') . ' ' . number_format($totals->total_charge_sum, 2),
                ])
                ->rawColumns(['posted_date', 'pending_count', 'rejected_count', 'running_count', 'canceled_count', 'paused_count', 'completed_count', 'total_task_count', 'posted_charge', 'site_charge', 'total_charge'])
                ->make(true);
        }

        return view('backend.report.posted_task');
    }

    public function workedTaskReport(Request $request)
    {
        if ($request->ajax()) {
            // Query to get individual row data
            $query = ProofTask::select(
                DB::raw('DATE(proof_tasks.created_at) as worked_date'),
                DB::raw('SUM(CASE WHEN proof_tasks.status = "Pending" THEN 1 ELSE 0 END) as pending_count'),
                DB::raw('SUM(CASE WHEN proof_tasks.status = "Approved" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN proof_tasks.status = "Rejected" THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN proof_tasks.status = "Reviewed" THEN 1 ELSE 0 END) as reviewed_count'),
                DB::raw('COUNT(proof_tasks.id) as total_task_count'),
            );

            // Apply filters based on the request input
            if ($request->status) {
                $query->where('proof_tasks.status', $request->status);
            }

            // Date filter: handle cases where either start_date or end_date is provided
            if ($request->start_date && !$request->end_date) {
                $query->whereDate('proof_tasks.created_at', '>=', $request->start_date);
            } elseif (!$request->start_date && $request->end_date) {
                $query->whereDate('proof_tasks.created_at', '<=', $request->end_date);
            } elseif ($request->start_date && $request->end_date) {
                $query->whereBetween(DB::raw('DATE(proof_tasks.created_at)'), [$request->start_date, $request->end_date]);
            }

            $query->groupBy(DB::raw('DATE(proof_tasks.created_at)'));
            $query->orderBy('worked_date', 'desc');

            // Retrieve the row-level data
            $pendingRequest = $query->get();

            // Query to calculate the totals across all rows
            $totalsQuery = ProofTask::select(
                DB::raw('SUM(CASE WHEN proof_tasks.status = "Pending" THEN 1 ELSE 0 END) as total_pending_count_sum'),
                DB::raw('SUM(CASE WHEN proof_tasks.status = "Approved" THEN 1 ELSE 0 END) as total_approved_count_sum'),
                DB::raw('SUM(CASE WHEN proof_tasks.status = "Rejected" THEN 1 ELSE 0 END) as total_rejected_count_sum'),
                DB::raw('SUM(CASE WHEN proof_tasks.status = "Reviewed" THEN 1 ELSE 0 END) as total_reviewed_count_sum'),
                DB::raw('COUNT(proof_tasks.id) as total_task_count_sum'),
            );

            // Apply the same filters to the totals query
            if ($request->status) {
                $totalsQuery->where('proof_tasks.status', $request->status);
            }

            // Date filter for totals query
            if ($request->start_date && !$request->end_date) {
                $totalsQuery->whereDate('proof_tasks.created_at', '>=', $request->start_date);
            } elseif (!$request->start_date && $request->end_date) {
                $totalsQuery->whereDate('proof_tasks.created_at', '<=', $request->end_date);
            } elseif ($request->start_date && $request->end_date) {
                $totalsQuery->whereBetween(DB::raw('DATE(proof_tasks.created_at)'), [$request->start_date, $request->end_date]);
            }

            // Get the total values
            $totals = $totalsQuery->first();

            // Return the DataTables response with totals
            return DataTables::of($pendingRequest)
                ->addIndexColumn()
                ->editColumn('worked_date', function ($row) {
                    return '<span class="badge bg-primary">' . date('l j-F, Y', strtotime($row->worked_date)) . '</span>';
                })
                ->editColumn('pending_count', function ($row) {
                    return '<span class="badge bg-info">' . $row->pending_count . '</span>';
                })
                ->editColumn('approved_count', function ($row) {
                    return '<span class="badge bg-dark">' . $row->approved_count . '</span>';
                })
                ->editColumn('rejected_count', function ($row) {
                    return '<span class="badge bg-warning">' . $row->rejected_count . '</span>';
                })
                ->editColumn('reviewed_count', function ($row) {
                    return '<span class="badge bg-danger">' . $row->reviewed_count . '</span>';
                })
                ->editColumn('total_task_count', function ($row) {
                    return '<span class="badge bg-success">' . $row->total_task_count . '</span>';
                })
                ->with([
                    'total_pending_count_sum' => $totals->total_pending_count_sum,
                    'total_approved_count_sum' => $totals->total_approved_count_sum,
                    'total_rejected_count_sum' => $totals->total_rejected_count_sum,
                    'total_reviewed_count_sum' => $totals->total_reviewed_count_sum,
                    'total_task_count_sum' => $totals->total_task_count_sum,
                ])
                ->rawColumns(['worked_date', 'pending_count', 'approved_count', 'rejected_count', 'reviewed_count', 'total_task_count'])
                ->make(true);
        }

        return view('backend.report.worked_task');
    }
}
