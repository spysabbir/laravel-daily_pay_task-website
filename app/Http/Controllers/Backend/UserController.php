<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserStatus;
use App\Notifications\UserStatusNotification;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\PostTask;
use App\Models\ProofTask;
use App\Models\Report;
use App\Models\Withdraw;
use App\Models\BalanceTransfer;
use Carbon\Carbon;

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
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('user.device') , only:['userDevice']),
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

            if ($request->duplicate_device_check) {
                $userIps = UserDevice::groupBy('ip_address')->pluck('ip_address')->toArray();
                $sameIpUserIds = UserDevice::whereIn('ip_address', $userIps)
                    ->groupBy('user_id')
                    ->pluck('user_id')
                    ->toArray();

                $query->whereIn('id', $sameIpUserIds);
            }

            if ($request->last_activity) {
                if ($request->last_activity == 'Online') {
                    $query->where('last_activity_at', '>=', now()->subMinutes(5));
                } else if($request->last_activity == 'Offline') {
                    $query->where('last_activity_at', '<', now()->subMinutes(5));
                }
            }

            $query->select('users.*')->orderBy('created_at', 'desc');

            $allUser = $query->get();

            if ($request->duplicate_device_check === 'Matched') {
                $allUser = $allUser->filter(function ($user) {
                    $userIps = UserDevice::where('user_id', $user->id)->groupBy('ip_address')->pluck('ip_address')->toArray();
                    $sameIpUserIds = UserDevice::whereIn('ip_address', $userIps)
                        ->where('user_id', '!=', $user->id)
                        ->groupBy('user_id')
                        ->pluck('user_id')
                        ->toArray();

                    return User::whereIn('id', $sameIpUserIds)
                        ->whereIn('status', ['Active', 'Blocked'])
                        ->where('user_type', 'Frontend')
                        ->exists();
                });
            } elseif ($request->duplicate_device_check === 'Not Matched') {
                $allUser = $allUser->filter(function ($user) {
                    $userIps = UserDevice::where('user_id', $user->id)->groupBy('ip_address')->pluck('ip_address')->toArray();
                    $sameIpUserIds = UserDevice::whereIn('ip_address', $userIps)
                        ->where('user_id', '!=', $user->id)
                        ->groupBy('user_id')
                        ->pluck('user_id')
                        ->toArray();

                    return !User::whereIn('id', $sameIpUserIds)
                        ->whereIn('status', ['Active', 'Blocked'])
                        ->where('user_type', 'Frontend')
                        ->exists();
                });
            }

            // Clone the query for counts
            $totalUsersCount = (clone $allUser)->count();

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
                ->editColumn('last_activity_at', function ($row) {
                    $lastActivity = $row->last_activity_at ? Carbon::parse($row->last_activity_at) : null;
                    $isOnline = $lastActivity && $lastActivity->gte(now()->subMinutes(5));
                    $timeDiff = $lastActivity ? $lastActivity->diffForHumans() : 'No activity';

                    $statusBadge = $isOnline
                        ? '<span class="badge bg-success text-white">Online</span>'
                        : '<span class="badge bg-danger text-white">Offline</span>';

                    return '<div>' . $statusBadge . ' <small>' . $timeDiff . '</small></div>';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-info bg-dark">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->editColumn('duplicate_device_check', function ($row) {
                    $userIps = UserDevice::where('user_id', $row->id)->groupBy('ip_address')->pluck('ip_address')->toArray();
                    $sameIpUserIds = UserDevice::whereIn('ip_address', $userIps)
                        ->where('user_id', '!=', $row->id)
                        ->groupBy('user_id')
                        ->pluck('user_id')
                        ->toArray();
                    $sameIpUsers = User::whereIn('id', $sameIpUserIds)
                        ->whereIn('status', ['Active', 'Blocked'])
                        ->where('user_type', 'Frontend')
                        ->get();

                        return $sameIpUsers->count() > 0
                        ? '<span class="badge bg-danger">Matched</span>'
                        : '<span class="badge bg-success">Not Matched</span>';
                })
                ->addColumn('action', function ($row) {
                    $deletePermission = auth()->user()->can('user.destroy');
                    $statusPermission = auth()->user()->can('user.status');
                    $devicePermission = auth()->user()->can('user.device');

                    $viewBtn = '<a href="' . route('backend.user.show', encrypt($row->id)) . '" class="btn btn-primary btn-xs">View</a>';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>'
                        : '';
                    $statusBtn = $statusPermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs statusBtn">Status Details</button>'
                        : '';
                    $deviceBtn = $devicePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs deviceBtn">Device Details</button>'
                        : '';

                    $btn = $viewBtn . ' ' . $deleteBtn . ' ' . $statusBtn . ' ' . $deviceBtn;
                    return $btn;
                })
                ->with([
                    'totalUsersCount' => $totalUsersCount,
                ])
                ->rawColumns(['deposit_balance', 'withdraw_balance', 'hold_balance', 'report_count', 'block_count', 'last_activity_at', 'created_at', 'duplicate_device_check', 'action'])
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

            if ($request->last_activity) {
                if ($request->last_activity == 'Online') {
                    $query->where('last_activity_at', '>=', now()->subMinutes(5));
                } else if($request->last_activity == 'Offline') {
                    $query->where('last_activity_at', '<', now()->subMinutes(5));
                } else {
                    $query->where('last_activity_at', null);
                }
            }

            $query->select('users.*')->orderBy('created_at', 'desc');

            // Clone the query for counts
            $totalUsersCount = (clone $query)->count();

            $allUser = $query->get();

            return DataTables::of($allUser)
                ->addIndexColumn()
                ->editColumn('last_activity_at', function ($row) {
                    $lastActivity = $row->last_activity_at ? Carbon::parse($row->last_activity_at) : null;

                    if (!$lastActivity) {
                        return '<div class="badge bg-danger text-white">No activity</div>';
                    }

                    $isOnline = $lastActivity->gte(now()->subMinutes(5));
                    $timeDiff = $lastActivity->diffForHumans();

                    $statusBadge = $isOnline
                        ? '<span class="badge bg-success text-white">Online</span>'
                        : '<span class="badge bg-warning text-white">Offline</span>';

                    return '<div>' . $statusBadge . ' <small>' . $timeDiff . '</small></div>';
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
                ->with([
                    'totalUsersCount' => $totalUsersCount,
                ])
                ->rawColumns(['last_activity_at', 'created_at', 'action'])
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

            if ($request->duplicate_device_check) {
                $userIps = UserDevice::groupBy('ip_address')->pluck('ip_address')->toArray();
                $sameIpUserIds = UserDevice::whereIn('ip_address', $userIps)
                    ->groupBy('user_id')
                    ->pluck('user_id')
                    ->toArray();

                $query->whereIn('id', $sameIpUserIds);
            }

            if ($request->last_activity) {
                if ($request->last_activity == 'Online') {
                    $query->where('last_activity_at', '>=', now()->subMinutes(5));
                } else if($request->last_activity == 'Offline') {
                    $query->where('last_activity_at', '<', now()->subMinutes(5));
                }
            }

            $query->select('users.*')->orderBy('created_at', 'desc');

            $allUser = $query->get();

            if ($request->duplicate_device_check === 'Matched') {
                $allUser = $allUser->filter(function ($user) {
                    $userIps = UserDevice::where('user_id', $user->id)->groupBy('ip_address')->pluck('ip_address')->toArray();
                    $sameIpUserIds = UserDevice::whereIn('ip_address', $userIps)
                        ->where('user_id', '!=', $user->id)
                        ->groupBy('user_id')
                        ->pluck('user_id')
                        ->toArray();

                    return User::whereIn('id', $sameIpUserIds)
                        ->whereIn('status', ['Active', 'Blocked'])
                        ->where('user_type', 'Frontend')
                        ->exists();
                });
            } elseif ($request->duplicate_device_check === 'Not Matched') {
                $allUser = $allUser->filter(function ($user) {
                    $userIps = UserDevice::where('user_id', $user->id)->groupBy('ip_address')->pluck('ip_address')->toArray();
                    $sameIpUserIds = UserDevice::whereIn('ip_address', $userIps)
                        ->where('user_id', '!=', $user->id)
                        ->groupBy('user_id')
                        ->pluck('user_id')
                        ->toArray();

                    return !User::whereIn('id', $sameIpUserIds)
                        ->whereIn('status', ['Active', 'Blocked'])
                        ->where('user_type', 'Frontend')
                        ->exists();
                });
            }

            // Clone the query for counts
            $totalUsersCount = (clone $allUser)->count();

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
                ->editColumn('last_activity_at', function ($row) {
                    $lastActivity = $row->last_activity_at ? Carbon::parse($row->last_activity_at) : null;
                    $isOnline = $lastActivity && $lastActivity->gte(now()->subMinutes(5));
                    $timeDiff = $lastActivity ? $lastActivity->diffForHumans() : 'No activity';

                    $statusBadge = $isOnline
                        ? '<span class="badge bg-success text-white">Online</span>'
                        : '<span class="badge bg-danger text-white">Offline</span>';

                    return '<div>' . $statusBadge . ' <small>' . $timeDiff . '</small></div>';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-info bg-dark">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->editColumn('duplicate_device_check', function ($row) {
                    $userIps = UserDevice::where('user_id', $row->id)->groupBy('ip_address')->pluck('ip_address')->toArray();
                    $sameIpUserIds = UserDevice::whereIn('ip_address', $userIps)
                        ->where('user_id', '!=', $row->id)
                        ->groupBy('user_id')
                        ->pluck('user_id')
                        ->toArray();
                    $sameIpUsers = User::whereIn('id', $sameIpUserIds)
                        ->whereIn('status', ['Active', 'Blocked'])
                        ->where('user_type', 'Frontend')
                        ->get();

                        return $sameIpUsers->count() > 0
                        ? '<span class="badge bg-danger">Matched</span>'
                        : '<span class="badge bg-success">Not Matched</span>';
                })
                ->addColumn('action', function ($row) {
                    $deletePermission = auth()->user()->can('user.destroy');
                    $statusPermission = auth()->user()->can('user.status');
                    $devicePermission = auth()->user()->can('user.device');

                    $viewBtn = '<a href="' . route('backend.user.show', encrypt($row->id)) . '" class="btn btn-primary btn-xs">View</a>';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>'
                        : '';
                    $statusBtn = $statusPermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs statusBtn" data-bs-toggle="modal" data-bs-target=".statusModal">Status Details</button>'
                        : '';
                    $deviceBtn = $devicePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs deviceBtn" data-bs-toggle="modal" data-bs-target=".deviceModal">Device Details</button>'
                        : '';

                    $btn = $viewBtn . ' ' . $deleteBtn . ' ' . $statusBtn . ' ' . $deviceBtn;
                    return $btn;
                })
                ->with([
                    'totalUsersCount' => $totalUsersCount,
                ])
                ->rawColumns(['deposit_balance', 'withdraw_balance', 'hold_balance', 'report_count', 'block_count', 'last_activity_at', 'created_at', 'duplicate_device_check', 'action'])
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

            if ($request->last_activity) {
                if ($request->last_activity == 'Online') {
                    $query->where('last_activity_at', '>=', now()->subMinutes(5));
                } else if($request->last_activity == 'Offline') {
                    $query->where('last_activity_at', '<', now()->subMinutes(5));
                }
            }

            $query->select('users.*')->orderBy('created_at', 'desc');

            // Clone the query for counts
            $totalUsersCount = (clone $query)->count();

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
                ->editColumn('last_activity_at', function ($row) {
                    $lastActivity = $row->last_activity_at ? Carbon::parse($row->last_activity_at) : null;
                    $isOnline = $lastActivity && $lastActivity->gte(now()->subMinutes(5));
                    $timeDiff = $lastActivity ? $lastActivity->diffForHumans() : 'No activity';

                    $statusBadge = $isOnline
                        ? '<span class="badge bg-success text-white">Online</span>'
                        : '<span class="badge bg-danger text-white">Offline</span>';

                    return '<div>' . $statusBadge . ' <small>' . $timeDiff . '</small></div>';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-info bg-dark">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $deletePermission = auth()->user()->can('user.destroy');
                    $statusPermission = auth()->user()->can('user.status');
                    $devicePermission = auth()->user()->can('user.device');

                    $viewBtn = '<a href="' . route('backend.user.show', encrypt($row->id)) . '" class="btn btn-primary btn-xs">View</a>';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>'
                        : '';
                    $statusBtn = $statusPermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs statusBtn" data-bs-toggle="modal" data-bs-target=".statusModal">Status Details</button>'
                        : '';
                    $deviceBtn = $devicePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs deviceBtn" data-bs-toggle="modal" data-bs-target=".deviceModal">Device Details</button>'
                        : '';

                    $btn = $viewBtn . ' ' . $deleteBtn . ' ' . $statusBtn . ' ' . $deviceBtn;
                    return $btn;
                })
                ->with([
                    'totalUsersCount' => $totalUsersCount,
                ])
                ->rawColumns(['deposit_balance', 'withdraw_balance', 'hold_balance', 'report_count', 'block_count', 'banned_at', 'last_activity_at', 'created_at', 'action'])
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
        $userDevices = UserDevice::where('user_id', $id)->get();
        $depositBalance = $user->deposit_balance;
        $withdrawBalance = $user->withdraw_balance;

        $holdBalance = 0;
        $validPostTasks = PostTask::where('user_id', $id)
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
                $holdBalance += $chargePerTask * $reviewedCount;
            }

        $transferDepositBalance = BalanceTransfer::where('user_id', $id)->where('send_method', 'Deposit Balance')->sum('amount');
        $transferWithdrawBalance = BalanceTransfer::where('user_id', $id)->where('send_method', 'Withdraw Balance')->sum('amount');

        $reportsReceived = Report::where('user_id', $id)->where('status', 'Received')->count();
        // Deposit
        $pendingDeposit = Deposit::where('user_id', $id)->where('status', 'Pending')->sum('amount');
        $approvedDeposit = Deposit::where('user_id', $id)->where('status', 'Approved')->sum('amount');
        $rejectedDeposit = Deposit::where('user_id', $id)->where('status', 'Rejected')->sum('amount');
        // Withdraw
        $pendingWithdraw = Withdraw::where('user_id', $id)->where('status', 'Pending')->sum('amount');
        $approvedWithdraw = Withdraw::where('user_id', $id)->where('status', 'Approved')->sum('amount');
        $rejectedWithdraw = Withdraw::where('user_id', $id)->where('status', 'Rejected')->sum('amount');
        // Posted Task
        $pendingPostedTask = PostTask::where('user_id', $id)->where('status', 'Pending')->count();
        $runningPostedTask = PostTask::where('user_id', $id)->where('status', 'Running')->count();
        $rejectedPostedTask = PostTask::where('user_id', $id)->where('status', 'Rejected')->count();
        $canceledPostedTask = PostTask::where('user_id', $id)->where('status', 'Canceled')->count();
        $pausedPostedTask = PostTask::where('user_id', $id)->where('status', 'Paused')->count();
        $completedPostedTask = PostTask::where('user_id', $id)->where('status', 'Completed')->count();
        // Posted Task Proof Submit
        $postedTaskIds = PostTask::where('user_id', $id)->pluck('id');
        $pendingPostedTaskProofSubmit = ProofTask::whereIn('post_task_id', $postedTaskIds)->where('status', 'Pending')->count();
        $approvedPostedTaskProofSubmit = ProofTask::whereIn('post_task_id', $postedTaskIds)->where('status', 'Approved')->count();
        $rejectedPostedTaskProofSubmit = ProofTask::whereIn('post_task_id', $postedTaskIds)->where('status', 'Rejected')->count();
        $reviewedPostedTaskProofSubmit = ProofTask::whereIn('post_task_id', $postedTaskIds)->where('status', 'Reviewed')->count();
        // Worked Task
        $pendingWorkedTask = ProofTask::where('user_id', 'id')->where('status', 'Pending')->count();
        $approvedWorkedTask = ProofTask::where('user_id', 'id')->where('status', 'Approved')->count();
        $rejectedWorkedTask = ProofTask::where('user_id', 'id')->where('status', 'Rejected')->count();
        $reviewedWorkedTask = ProofTask::where('user_id', 'id')->where('status', 'Reviewed')->count();
        // Report
        $reportsSendPending = Report::where('reported_by', $id)->where('status', 'Pending')->count();
        $reportsSendFalse = Report::where('reported_by', $id)->where('status', 'False')->count();
        $reportsSendReceived = Report::where('reported_by', $id)->where('status', 'Received')->count();
        return view('backend.user.show', compact('user', 'userStatuses', 'userDevices', 'userVerification' , 'depositBalance', 'pendingDeposit', 'approvedDeposit', 'rejectedDeposit', 'transferDepositBalance', 'withdrawBalance', 'pendingWithdraw', 'approvedWithdraw', 'rejectedWithdraw', 'transferWithdrawBalance', 'pendingPostedTask', 'runningPostedTask', 'rejectedPostedTask', 'canceledPostedTask', 'pausedPostedTask', 'completedPostedTask', 'pendingPostedTaskProofSubmit', 'approvedPostedTaskProofSubmit', 'rejectedPostedTaskProofSubmit', 'reviewedPostedTaskProofSubmit', 'pendingWorkedTask', 'approvedWorkedTask', 'rejectedWorkedTask', 'reviewedWorkedTask', 'reportsSendPending', 'reportsSendFalse', 'reportsSendReceived', 'reportsReceived', 'holdBalance'));
    }

    public function userStatus(string $id)
    {
        $user = User::where('id', $id)->first();
        $withdrawBalance = $user->withdraw_balance;
        $depositBalance = $user->deposit_balance;
        $holdBalance = 0;
        $validPostTasks = PostTask::where('user_id', $id)
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
                $holdBalance += $chargePerTask * $reviewedCount;
            }

        $userStatuses = UserStatus::where('user_id', $id)->get();
        $depositRequests = Deposit::where('user_id', $id)->where('status', 'Pending')->count();
        $withdrawRequests = Withdraw::where('user_id', $id)->where('status', 'Pending')->count();
        $postedTasksRequests = PostTask::where('user_id', $id)->where('status', 'Pending')->count();
        $postedTaskIds = PostTask::where('user_id', $id)->pluck('id');
        $postedTasksProofSubmitRequests = ProofTask::whereIn('post_task_id', $postedTaskIds)->whereIn('status', ['Pending', 'Reviewed'])->count();
        $workedTasksRequests = ProofTask::where('user_id', $id)->where('status', 'Reviewed')->count();
        $reportRequestsPending = ProofTask::where('user_id', $id)->where('status', 'Pending')->count();
        return view('backend.user.status', compact('user', 'userStatuses', 'depositRequests', 'withdrawRequests', 'postedTasksRequests', 'postedTasksProofSubmitRequests', 'workedTasksRequests', 'reportRequestsPending', 'depositBalance', 'withdrawBalance', 'holdBalance'));
    }

    public function userDevice(string $id)
    {
        $user = User::where('id', $id)->first();
        $userDevices = UserDevice::where('user_id', $id)->get();
        return view('backend.user.device', compact('user', 'userDevices'));
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

        if ($request->status == User::where('id', $id)->first()->status) {
            return response()->json([
                'status' => 402,
                'error' => 'User status is already ' . $request->status . '. Please check user ' . $request->status . ' list.'
            ]);
        }

        $depositRequests = Deposit::where('user_id', $id)->where('status', 'Pending')->count();
        $withdrawRequests = Withdraw::where('user_id', $id)->where('status', 'Pending')->count();
        $postedTasksRequests = PostTask::where('user_id', $id)->where('status', 'Pending')->count();
        $postedTaskIds = PostTask::where('user_id', $id)->pluck('id');
        $postedTasksProofSubmitRequests = ProofTask::whereIn('post_task_id', $postedTaskIds)->whereIn('status', ['Pending', 'Reviewed'])->count();
        $workedTasksRequests = ProofTask::where('user_id', $id)->where('status', 'Reviewed')->count();
        $reportRequestsPending = ProofTask::where('user_id', $id)->where('status', 'Pending')->count();

        if ($depositRequests > 0 || $withdrawRequests > 0 || $postedTasksRequests > 0 || $postedTasksProofSubmitRequests > 0 || $workedTasksRequests > 0 || $reportRequestsPending > 0) {
            return response()->json(['status' => 401, 'error' => 'User has some pending requests. Please resolve them first.']);
        }

        $blockedStatusCount = UserStatus::where('user_id', $id)->where('status', 'Blocked')->count();
        if ($blockedStatusCount == get_default_settings('user_max_blocked_time_for_banned') && $request->status == 'Blocked') {
            return response()->json(['status' => 401, 'error' => 'User has reached the maximum blocked time. Please change the status to Active or Banned.']);
        }

        $userStatus = UserStatus::where('user_id', $id)->latest()->first();
        if ($request->status == 'Active') {
            $userStatus->update([
                'updated_by' => auth()->user()->id,
                'resolved_at' => now(),
            ]);
        }

        $userStatus = UserStatus::create([
            'user_id' => $id,
            'status' => $request->status,
            'reason' => $request->reason,
            'blocked_duration' => $request->blocked_duration ?? null,
            'resolved_at' => $request->status == 'Active' ? now() : null,
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
        $depositRequests = Deposit::where('user_id', $id)->where('status', 'Pending')->count();
        $withdrawRequests = Withdraw::where('user_id', $id)->where('status', 'Pending')->count();
        $postedTasksRequests = PostTask::where('user_id', $id)->where('status', 'Pending')->count();
        $postedTaskIds = PostTask::where('user_id', $id)->pluck('id');
        $postedTasksProofSubmitRequests = ProofTask::whereIn('post_task_id', $postedTaskIds)->whereIn('status', ['Pending', 'Reviewed'])->count();
        $workedTasksRequests = ProofTask::where('user_id', $id)->where('status', 'Reviewed')->count();
        $reportRequestsPending = ProofTask::where('user_id', $id)->where('status', 'Pending')->count();

        if ($depositRequests > 0 || $withdrawRequests > 0 || $postedTasksRequests > 0 || $postedTasksProofSubmitRequests > 0 || $workedTasksRequests > 0 || $reportRequestsPending > 0) {
            return response()->json(['status' => 401, 'error' => 'User has some pending requests. Please resolve them first.']);
        }

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
