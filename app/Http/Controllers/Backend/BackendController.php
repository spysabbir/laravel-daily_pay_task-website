<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PostTask;
use App\Models\Withdraw;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BackendController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::where('user_type', 'Frontend')->count();
        $activeUsers = User::where('user_type', 'Frontend')->where('status', 'Active')->count();

        $totalPostTask = PostTask::count();
        $runningPostTasks = PostTask::where('status', 'Running')->count();

        $totalDeposit = Withdraw::where('status', 'Approved')->sum('amount');
        $totalWithdraw = Withdraw::where('status', 'Approved')->sum('amount');

        $totalData = [
            'totalUsers' => $totalUsers,
            'totalPostTask' => $totalPostTask,
            'activeUsers' => $activeUsers,
            'runningPostTasks' => $runningPostTasks,
            'totalDeposit' => $totalDeposit,
            'totalWithdraw' => $totalWithdraw,
        ];

        return view('backend.dashboard' , compact('totalData'));
    }

    public function profileEdit(Request $request)
    {
        $user = $request->user();
        $userDevices = UserDevice::where('user_id', $user->id)->latest()->take(5)->get();
        return view('profile.edit', compact('user', 'userDevices'));
    }

    public function profileSetting(Request $request)
    {
        $user = $request->user();
        return view('profile.setting', compact('user'));
    }

    public function notification(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $notificationsQuery = $user->notifications();

            if ($request->status) {
                if ($request->status == 'Read') {
                    $notificationsQuery->whereNotNull('read_at');
                } else {
                    $notificationsQuery->whereNull('read_at');
                }
            }

            // Clone the query for counts
            $readNotificationsCount = (clone $notificationsQuery)->whereNotNull('read_at')->count();
            $unreadNotificationsCount = (clone $notificationsQuery)->whereNull('read_at')->count();

            $notifications = $notificationsQuery->get();

            return DataTables::of($notifications)
                ->addIndexColumn()
                ->editColumn('title', function ($row) {
                    return $row->data['title'];
                })
                ->editColumn('message', function ($row) {
                    return $row->data['message'];
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->diffForHumans();
                })
                ->editColumn('status', function ($row) {
                    if ($row->read_at) {
                        $status = '<span class="badge bg-success">Read</span>';
                    } else {
                        $status = '<span class="badge bg-danger">Unread</span>';
                    }
                    return $status;
                })
                ->with([
                    'readNotificationsCount' => $readNotificationsCount,
                    'unreadNotificationsCount' => $unreadNotificationsCount,
                ])
                ->rawColumns(['status'])
                ->make(true);
        }

        return view('backend.notification.index');
    }

    public function notificationRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }
        return redirect()->route('backend.notification');
    }

    public function notificationReadAll(Request $request)
    {
        Auth::user()->unreadNotifications->markAsRead();
        return redirect()->route('backend.notification');
    }
}
