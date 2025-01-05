<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserStatus;
use App\Notifications\UserStatusNotification;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\PostTask;
use App\Models\ProofTask;
use App\Models\Report;
use App\Models\Withdraw;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('user.active') , only:['userActiveList', 'userView']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('user.inactive') , only:['userInactiveList', 'userView']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('user.blocked') , only:['userBlockedList', 'userView']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('user.banned') , only:['userBannedList', 'userView']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('user.status') , only:['userStatus', 'userStatusUpdate']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('user.destroy'), only:['userDestroy']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('user.trash') , only:['userTrash']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('user.restore') , only:['userRestore']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('user.delete') , only:['userDelete']),
        ];
    }

    public function userActiveList(Request $request)
    {
        if ($request->ajax()) {
            $query = User::where('user_type', 'Frontend')->where('status', 'Active');

            if ($request->user_id) {
                $query->where('id', $request->user_id);
            }

            $query->select('users.*')->orderBy('created_at', 'desc');

            $allUser = $query->get();

            return DataTables::of($allUser)
                ->addIndexColumn()
                ->editColumn('deposit_balance', function ($row) {
                    return '
                        <span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->deposit_balance . '</span>
                        ';
                })
                ->editColumn('withdraw_balance', function ($row) {
                    return '
                        <span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->withdraw_balance . '</span>
                        ';
                })
                ->editColumn('hold_balance', function ($row) {
                    $postTaskChargeHold = 0;

                    // Fetch valid post tasks with necessary relationships and data in one query
                    $validPostTasks = PostTask::where('user_id', $row->id)
                        ->whereNotIn('status', ['Pending', 'Rejected'])
                        ->with(['proofTasks' => function ($query) {
                            $query->where(function ($query) {
                                $query->where('status', 'Reviewed')
                                    ->orWhere(function ($query) {
                                        $query->where('status', 'Rejected')
                                                ->whereNull('reviewed_at')
                                                ->where('rejected_at', '>', now()->subHours(get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time')));
                                    });
                            });
                        }])
                        ->get();

                    // Calculate the hold balance
                    foreach ($validPostTasks as $postTask) {
                        $chargePerTask = ($postTask->sub_cost + $postTask->site_charge) / $postTask->worker_needed;
                        $reviewedCount = $postTask->proofTasks->count();
                        $postTaskChargeHold += $chargePerTask * $reviewedCount;
                    }

                    return '
                        <span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . number_format($postTaskChargeHold, 2) . '</span>
                    ';
                })
                ->editColumn('report_count', function ($row) {
                    $report_count = Report::where('user_id', $row->id)->where('status', 'Received')->count();
                    return '
                        <span class="badge bg-warning text-dark">' . $report_count . ' time' . ($report_count > 1 ? 's' : '') . '</span>
                        ';
                })
                ->editColumn('block_count', function ($row) {
                    $block_count = UserStatus::where('user_id', $row->id)->where('status', 'Blocked')->count();
                    return '
                        <span class="badge bg-warning text-dark">' . $block_count . ' time' . ($block_count > 1 ? 's' : '') . '</span>
                        ';
                })
                ->editColumn('last_login', function ($row) {
                    $last_login_at = $row->last_login_at ? date('d M, Y  h:i:s A', strtotime($row->last_login_at)) : 'N/A';
                    return '
                        <span class="badge text-white bg-dark">' . $last_login_at . '</span>
                        ';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-info bg-dark">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $deletePermission = auth()->user()->can('user.destroy');
                    $statusPermission = auth()->user()->can('user.status');

                    $viewBtn = '<a href="' . route('backend.user.show', encrypt($row->id)) . '" class="btn btn-primary btn-xs">View</a>';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>'
                        : '';
                    $statusBtn = $statusPermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs statusBtn" data-bs-toggle="modal" data-bs-target=".statusModal">Status Details</button>'
                        : '';

                    $btn = $viewBtn . ' ' . $deleteBtn . ' ' . $statusBtn;
                    return $btn;
                })
                ->rawColumns(['deposit_balance', 'withdraw_balance', 'hold_balance', 'report_count', 'block_count', 'last_login', 'created_at', 'action'])
                ->make(true);
        }

        return view('backend.user.active');
    }

    public function userInactiveList(Request $request)
    {
        if ($request->ajax()) {
            $query = User::where('user_type', 'Frontend')->where('status', 'Inactive');

            if ($request->user_id) {
                $query->where('id', $request->user_id);
            }

            $query->select('users.*')->orderBy('created_at', 'desc');

            $allUser = $query->get();

            return DataTables::of($allUser)
                ->addIndexColumn()
                ->editColumn('last_login', function ($row) {
                    $last_login_at = $row->last_login_at ? date('d M, Y  h:i:s A', strtotime($row->last_login_at)) : 'N/A';
                    return '
                        <span class="badge text-white bg-dark">' . $last_login_at . '</span>
                        ';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-info bg-dark">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $deletePermission = auth()->user()->can('user.destroy');

                    $viewBtn = '<a href="' . route('backend.user.show', encrypt($row->id)) . '" class="btn btn-primary btn-xs">View</a>';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>'
                        : '';

                    $btn = $viewBtn . ' ' . $deleteBtn;
                    return $btn;
                })
                ->rawColumns(['last_login', 'created_at', 'action'])
                ->make(true);
        }

        return view('backend.user.inactive');
    }

    public function userBlockedList(Request $request)
    {
        if ($request->ajax()) {
            $query = User::where('user_type', 'Frontend')->where('status', 'Blocked');

            if ($request->user_id) {
                $query->where('id', $request->user_id);
            }

            $query->select('users.*')->orderBy('created_at', 'desc');

            $allUser = $query->get();

            return DataTables::of($allUser)
                ->addIndexColumn()
                ->editColumn('deposit_balance', function ($row) {
                    return '
                        <span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->deposit_balance . '</span>
                        ';
                })
                ->editColumn('withdraw_balance', function ($row) {
                    return '
                        <span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->withdraw_balance . '</span>
                        ';
                })
                ->editColumn('hold_balance', function ($row) {
                    $postTaskChargeHold = 0;

                    // Fetch valid post tasks with necessary relationships and data in one query
                    $validPostTasks = PostTask::where('user_id', $row->id)
                        ->whereNotIn('status', ['Pending', 'Rejected'])
                        ->with(['proofTasks' => function ($query) {
                            $query->where(function ($query) {
                                $query->where('status', 'Reviewed')
                                    ->orWhere(function ($query) {
                                        $query->where('status', 'Rejected')
                                                ->whereNull('reviewed_at')
                                                ->where('rejected_at', '>', now()->subHours(get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time')));
                                    });
                            });
                        }])
                        ->get();

                    // Calculate the hold balance
                    foreach ($validPostTasks as $postTask) {
                        $chargePerTask = ($postTask->sub_cost + $postTask->site_charge) / $postTask->worker_needed;
                        $reviewedCount = $postTask->proofTasks->count();
                        $postTaskChargeHold += $chargePerTask * $reviewedCount;
                    }

                    return '
                        <span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . number_format($postTaskChargeHold, 2) . '</span>
                    ';
                })
                ->editColumn('report_count', function ($row) {
                    $report_count = Report::where('user_id', $row->id)->where('status', 'Received')->count();
                    return '
                        <span class="badge bg-warning text-dark">' . $report_count . ' time' . ($report_count > 1 ? 's' : '') . '</span>
                        ';
                })
                ->editColumn('block_count', function ($row) {
                    $block_count = UserStatus::where('user_id', $row->id)->where('status', 'Blocked')->count();
                    return '
                        <span class="badge bg-warning text-dark">' . $block_count . ' time' . ($block_count > 1 ? 's' : '') . '</span>
                        ';
                })
                ->editColumn('last_login', function ($row) {
                    $last_login_at = $row->last_login_at ? date('d M, Y  h:i:s A', strtotime($row->last_login_at)) : 'N/A';
                    return '
                        <span class="badge text-white bg-dark">' . $last_login_at . '</span>
                        ';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-info bg-dark">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $deletePermission = auth()->user()->can('user.destroy');
                    $statusPermission = auth()->user()->can('user.status');

                    $viewBtn = '<a href="' . route('backend.user.show', encrypt($row->id)) . '" class="btn btn-primary btn-xs">View</a>';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>'
                        : '';
                    $statusBtn = $statusPermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs statusBtn" data-bs-toggle="modal" data-bs-target=".statusModal">Status Details</button>'
                        : '';

                    $btn = $viewBtn . ' ' . $deleteBtn . ' ' . $statusBtn;
                    return $btn;
                })
                ->rawColumns(['deposit_balance', 'withdraw_balance', 'hold_balance', 'report_count', 'block_count', 'last_login', 'created_at', 'action'])
                ->make(true);
        }

        return view('backend.user.blocked');
    }

    public function userBannedList(Request $request)
    {
        if ($request->ajax()) {
            $query = User::where('user_type', 'Frontend')->where('status', 'Banned');

            if ($request->user_id) {
                $query->where('id', $request->user_id);
            }

            $query->select('users.*')->orderBy('created_at', 'desc');

            $allUser = $query->get();

            return DataTables::of($allUser)
                ->addIndexColumn()
                ->editColumn('deposit_balance', function ($row) {
                    return '
                        <span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->deposit_balance . '</span>
                        ';
                })
                ->editColumn('withdraw_balance', function ($row) {
                    return '
                        <span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->withdraw_balance . '</span>
                        ';
                })
                ->editColumn('hold_balance', function ($row) {
                    $postTaskChargeHold = 0;

                    // Fetch valid post tasks with necessary relationships and data in one query
                    $validPostTasks = PostTask::where('user_id', $row->id)
                        ->whereNotIn('status', ['Pending', 'Rejected'])
                        ->with(['proofTasks' => function ($query) {
                            $query->where(function ($query) {
                                $query->where('status', 'Reviewed')
                                    ->orWhere(function ($query) {
                                        $query->where('status', 'Rejected')
                                                ->whereNull('reviewed_at')
                                                ->where('rejected_at', '>', now()->subHours(get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time')));
                                    });
                            });
                        }])
                        ->get();

                    // Calculate the hold balance
                    foreach ($validPostTasks as $postTask) {
                        $chargePerTask = ($postTask->sub_cost + $postTask->site_charge) / $postTask->worker_needed;
                        $reviewedCount = $postTask->proofTasks->count();
                        $postTaskChargeHold += $chargePerTask * $reviewedCount;
                    }

                    return '
                        <span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . number_format($postTaskChargeHold, 2) . '</span>
                    ';
                })
                ->editColumn('report_count', function ($row) {
                    $report_count = Report::where('user_id', $row->id)->where('status', 'Received')->count();
                    return '
                        <span class="badge bg-warning text-dark">' . $report_count . ' time' . ($report_count > 1 ? 's' : '') . '</span>
                        ';
                })
                ->editColumn('block_count', function ($row) {
                    $block_count = UserStatus::where('user_id', $row->id)->where('status', 'Blocked')->count();
                    return '
                        <span class="badge bg-warning text-dark">' . $block_count . ' time' . ($block_count > 1 ? 's' : '') . '</span>
                        ';
                })
                ->editColumn('banned_at', function ($row) {
                    $banned_at = UserStatus::where('user_id', $row->id)->where('status', 'Banned')->first()->created_at;
                    return '
                        <span class="badge text-danger bg-dark">' . date('d M, Y  h:i:s A', strtotime($banned_at)) . '</span>
                        ';
                })
                ->editColumn('last_login', function ($row) {
                    $last_login_at = $row->last_login_at ? date('d M, Y  h:i:s A', strtotime($row->last_login_at)) : 'N/A';
                    return '
                        <span class="badge text-white bg-dark">' . $last_login_at . '</span>
                        ';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-info bg-dark">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $deletePermission = auth()->user()->can('user.destroy');
                    $statusPermission = auth()->user()->can('user.status');

                    $viewBtn = '<a href="' . route('backend.user.show', encrypt($row->id)) . '" class="btn btn-primary btn-xs">View</a>';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>'
                        : '';
                    $statusBtn = $statusPermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs statusBtn" data-bs-toggle="modal" data-bs-target=".statusModal">Status Details</button>'
                        : '';

                    $btn = $viewBtn . ' ' . $deleteBtn . ' ' . $statusBtn;
                    return $btn;
                })
                ->rawColumns(['deposit_balance', 'withdraw_balance', 'hold_balance', 'report_count', 'block_count', 'banned_at', 'last_login', 'created_at', 'action'])
                ->make(true);
        }

        return view('backend.user.banned');
    }

    public function userView(string $id)
    {
        $id = decrypt($id);
        $user = User::withTrashed()->where('id', $id)->first();
        $userVerification = Verification::where('user_id', $id)->first();
        $userStatuses = UserStatus::where('user_id', $id)->get();
        $userDetails = UserDetail::where('user_id', $id)->get();
        // Deposit
        $depositBalance = $user->deposit_balance;
        $pendingDeposit = Deposit::where('user_id', $id)->where('status', 'Pending')->sum('amount');
        $approvedDeposit = Deposit::where('user_id', $id)->where('status', 'Approved')->whereNot('method', 'Withdraw Balance')->sum('amount');
        $rejectedDeposit = Deposit::where('user_id', $id)->where('status', 'Rejected')->sum('amount');
        $transferDeposit = Deposit::where('user_id', $id)->where('status', 'Approved')->where('method', 'Withdraw Balance')->sum('amount');
        // Withdraw
        $withdrawBalance = $user->withdraw_balance;
        $pendingWithdraw = Withdraw::where('user_id', $id)->where('status', 'Pending')->sum('amount');
        $approvedWithdraw = Withdraw::where('user_id', $id)->where('status', 'Approved')->whereNot('method', 'Deposit Balance')->sum('amount');
        $rejectedWithdraw = Withdraw::where('user_id', $id)->where('status', 'Rejected')->sum('amount');
        $transferWithdraw = Withdraw::where('user_id', $id)->where('status', 'Approved')->where('method', 'Deposit Balance')->sum('amount');
        // Posted Task
        $pendingPostedTask = PostTask::where('user_id', $id)->where('status', 'Pending')->count();
        $runningPostedTask = PostTask::where('user_id', $id)->where('status', 'Running')->count();
        $rejectedPostedTask = PostTask::where('user_id', $id)->where('status', 'Rejected')->count();
        $canceledPostedTask = PostTask::where('user_id', $id)->where('status', 'Canceled')->count();
        $pausedPostedTask = PostTask::where('user_id', $id)->where('status', 'Paused')->count();
        $completedPostedTask = PostTask::where('user_id', $id)->where('status', 'Completed')->count();
        // Posted Task Proof Submit
        return view('backend.user.show', compact('user', 'userStatuses', 'userDetails', 'userVerification' , 'depositBalance', 'pendingDeposit', 'approvedDeposit', 'rejectedDeposit', 'transferDeposit', 'withdrawBalance', 'pendingWithdraw', 'approvedWithdraw', 'rejectedWithdraw', 'transferWithdraw', 'pendingPostedTask', 'runningPostedTask', 'rejectedPostedTask', 'canceledPostedTask', 'pausedPostedTask', 'completedPostedTask'));
    }

    public function userStatus(string $id)
    {
        $user = User::where('id', $id)->first();
        $userStatuses = UserStatus::where('user_id', $id)->get();
        $depositRequests = Deposit::where('user_id', $id)->where('status', 'Pending')->count();
        $withdrawRequests = Withdraw::where('user_id', $id)->where('status', 'Pending')->count();
        $postedTasksRequests = PostTask::where('user_id', $id)->where('status', 'Pending')->count();
        $workedTasksRequests = ProofTask::where('user_id', $id)->where('status', 'Reviewed')->count();
        $reportRequestsPending = ProofTask::where('user_id', $id)->where('status', 'Pending')->count();
        $reportsReceived = ProofTask::where('user_id', $id)->where('status', 'Received')->count();
        return view('backend.user.status', compact('user', 'userStatuses', 'depositRequests', 'withdrawRequests', 'postedTasksRequests' , 'workedTasksRequests', 'reportRequestsPending', 'reportsReceived'));
    }

    public function userStatusUpdate(Request $request, string $id)
    {
        $rules = [
            'status' => 'required',
            'reason' => 'required',
        ];

        if ($request->status == 'Blocked') {
            $rules['blocked_duration'] = 'required|integer';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'error' => $validator->errors()->toArray()]);
        }

        $userStatus = UserStatus::create([
            'user_id' => $id,
            'status' => $request->status,
            'reason' => $request->reason,
            'blocked_duration' => $request->blocked_duration ?? null,
            'blocked_resolved' => $request->status == 'Active' ? now() : null,
            'created_by' => auth()->user()->id,
            'created_at' => now(),
        ]);

        $user = User::findOrFail($id);
        $user->notify(new UserStatusNotification($userStatus));

        $user = User::findOrFail($id);
        $user->status = $request->status;
        $user->save();

        return response()->json(['status' => 200, 'message' => 'User status updated successfully']);
    }

    public function userDestroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->updated_by = auth()->user()->id;
        $user->deleted_by = auth()->user()->id;
        $user->save();
        $user->delete();
    }

    public function userTrash(Request $request)
    {
        if ($request->ajax()) {
            $query = User::where('user_type', 'Frontend')->onlyTrashed();

            $trashUser = $query->orderBy('deleted_at', 'desc')->get();

            return DataTables::of($trashUser)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $restorePermission = auth()->user()->can('user.restore');
                    $deletePermission = auth()->user()->can('user.delete');

                    $viewBtn = '<a href="' . route('backend.user.show', encrypt($row->id)) . '" class="btn btn-primary btn-xs" target="_blank">View</a>';
                    $restoreBtn = $restorePermission
                        ? '<button type="button" data-id="'.$row->id.'" class="btn bg-success btn-xs restoreBtn">Restore</button>'
                        : '';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="'.$row->id.'" class="btn bg-danger btn-xs forceDeleteBtn">Delete</button>'
                        : '';

                    $btn = $restoreBtn . ' ' . $deleteBtn . ' ' . $viewBtn;
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.user.index');
    }

    public function userRestore(string $id)
    {
        User::onlyTrashed()->where('id', $id)->update([
            'deleted_by' => NULL
        ]);

        User::onlyTrashed()->where('id', $id)->restore();
    }

    public function userDelete(string $id)
    {
        $user = User::onlyTrashed()->where('id', $id)->first();
        $user->forceDelete();
    }
}
