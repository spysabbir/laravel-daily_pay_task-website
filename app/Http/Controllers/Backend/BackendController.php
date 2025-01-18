<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PostTask;
use App\Models\ProofTask;
use App\Models\UserDevice;
use App\Models\Verification;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BackendController extends Controller
{
    public function dashboard()
    {
        $statuses = ['Active', 'Inactive', 'Blocked', 'Banned'];

        $userStatusData = User::select('status', DB::raw('count(*) as total'))
            ->where('user_type', 'Frontend')
            ->groupBy('status')
            ->pluck('total', 'status');

        $formattedUserStatusData = collect($statuses)->map(fn($status) => [
            'label' => $status,
            'data' => $userStatusData[$status] ?? 0,
        ]);

        // Get last 7 days data
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push(Carbon::today()->subDays($i)->format('M d Y'));
        }
        $lastSevenDaysCategories = $dates->toArray();

        // Get counts for verified users
        $verifiedUsersData = Verification::select(
            DB::raw('DATE(approved_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('approved_at', '!=', null)
            ->where('approved_at', '>=', Carbon::today()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');
        // Prepare data for the chart
        $formattedVerifiedUsersData = $dates->map(function ($date) use ($verifiedUsersData) {
            $dbDate = Carbon::createFromFormat('M d Y', $date)->format('Y-m-d');
            return $verifiedUsersData[$dbDate]->count ?? 0; // Default to 0 if no data
        })->toArray();

        // Get counts for posted tasks
        $postedTasksData = PostTask::select(
            DB::raw('DATE(approved_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('approved_at', '!=', null)
            ->where('approved_at', '>=', Carbon::today()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');
        // Prepare data for the chart
        $formattedPostedTasksData = $dates->map(function ($date) use ($postedTasksData) {
            $dbDate = Carbon::createFromFormat('M d Y', $date)->format('Y-m-d');
            return $postedTasksData[$dbDate]->count ?? 0; // Default to 0 if no data
        })->toArray();

        // Get counts for worked tasks
        $workedTasksData = ProofTask::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', Carbon::today()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');
        // Prepare data for the chart
        $formattedWorkedTasksData = $dates->map(function ($date) use ($workedTasksData) {
            $dbDate = Carbon::createFromFormat('M d Y', $date)->format('Y-m-d');
            return $workedTasksData[$dbDate]->count ?? 0; // Default to 0 if no data
        })->toArray();

        // Get counts for posted tasks status wise
        $postedTasksStatusStatuses = ['Pending','Running','Rejected','Canceled','Paused','Completed'];
        $totalPostedTasksStatusWise = PostTask::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');
        $postedTasksStatusStatusesData = array_map(function($status) use ($totalPostedTasksStatusWise) {
            return $totalPostedTasksStatusWise->get($status, 0); // Default to 0 if status is not found
        }, $postedTasksStatusStatuses);

        // Get counts for worked tasks status wise
        $workedTasksStatusStatuses = ["Pending", "Approved", "Rejected", "Reviewed"];
        $totalWorkedTasksStatusWise = ProofTask::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');
        $workedTasksStatusStatusesData = array_map(function($status) use ($totalWorkedTasksStatusWise) {
            return $totalWorkedTasksStatusWise->get($status, 0); // Default to 0 if status is not found
        }, $workedTasksStatusStatuses);


        return view('backend.dashboard' , compact( 'formattedUserStatusData', 'formattedVerifiedUsersData', 'lastSevenDaysCategories', 'formattedPostedTasksData', 'formattedWorkedTasksData', 'workedTasksStatusStatuses', 'workedTasksStatusStatusesData', 'postedTasksStatusStatuses', 'postedTasksStatusStatusesData'));
    }

    public function profileEdit(Request $request)
    {
        $user = $request->user();
        return view('profile.edit', compact('user'));
    }

    public function profileSetting(Request $request)
    {
        $user = $request->user();
        $userDevices = UserDevice::where('user_id', $user->id)->latest()->take(5)->get();
        return view('profile.setting', compact('user', 'userDevices'));
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
