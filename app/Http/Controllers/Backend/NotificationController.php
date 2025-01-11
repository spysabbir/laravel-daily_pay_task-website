<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Notifications\CustomNotification;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class NotificationController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('send.notification') , only:['sendNotification', 'sendNotificationShow']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('send.notification.store') , only:['sendNotificationStore']),
        ];
    }

    public function sendNotification(Request $request)
    {
        if ($request->ajax()) {
            $notificationsQuery = DatabaseNotification::query()
                ->where('type', 'App\Notifications\CustomNotification');

            if ($request->status) {
                if ($request->status == 'Read') {
                    $notificationsQuery->whereNotNull('read_at');
                } else {
                    $notificationsQuery->whereNull('read_at');
                }
            }

            if ($request->user_id) {
                $notificationsQuery->where('notifiable_id', $request->user_id);
            }

            if ($request->type) {
                if ($request->type == 'All User') {
                    $notificationsQuery->whereHasMorph('notifiable', User::class, function ($query) {
                        $query->where('user_type', 'Frontend');
                    });
                } else {
                    $notificationsQuery->whereHasMorph('notifiable', User::class, function ($query) {
                        $query->where('user_type', 'Backend');
                    });
                }
            }

            $readNotificationsCount = (clone $notificationsQuery)->whereNotNull('read_at')->count();
            $unreadNotificationsCount = (clone $notificationsQuery)->whereNull('read_at')->count();

            $notifications = $notificationsQuery->get();

            return DataTables::of($notifications)
                ->addIndexColumn()
                ->editColumn('type', function ($row) {
                    return $row->notifiable->user_type == 'Frontend' ? 'User' : 'Employee';
                })
                ->editColumn('user_id', function ($row) {
                    return $row->notifiable->id;
                })
                ->editColumn('user_name', function ($row) {
                    return $row->notifiable->name;
                })
                ->editColumn('title', function ($row) {
                    return $row->data['title'] ?? '-'; // Handle missing title
                })
                ->editColumn('message', function ($row) {
                    return $row->data['message'] ?? '-'; // Handle missing message
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->diffForHumans();
                })
                ->editColumn('status', function ($row) {
                    if ($row->read_at) {
                        return '<span class="badge bg-success">Read</span>';
                    } else {
                        return '<span class="badge bg-danger">Unread</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    return '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">View</button>';
                })
                ->with([
                    'readNotificationsCount' => $readNotificationsCount,
                    'unreadNotificationsCount' => $unreadNotificationsCount,
                ])
                ->rawColumns(['type', 'user_id', 'user_name', 'status', 'action'])
                ->make(true);
        }

        $allUser = User::where('user_type', 'Frontend')
            ->whereIn('status', ['Active', 'Blocked'])
            ->get();
        $allEmployee = User::where('user_type', 'Backend')
            ->where('status', 'Active')
            ->where('id', '!=', 1)
            ->get();

        return view('backend.send_notification.index', compact('allUser', 'allEmployee'));
    }


    public function sendNotificationShow(string $id)
    {
        $notification = DatabaseNotification::findOrFail($id);

        return view('backend.send_notification.view', compact('notification',));
    }


    public function sendNotificationStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
            'user_id' => [
                'required_if:type,Single User',
                'required_if:type,Single Employee',
                'nullable', // Prevents validation error when not required
            ],
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ], [
            'user_id.required_if' => 'The Name field is required when type is Single Employee or Single User.',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        } else {
            $notificationData = [
                'title' => $request->title,
                'message' => $request->message,
            ];

            if($request->type == 'All Employee') {
                $allEmployee = User::where('user_type', 'Backend')->where('status', 'Active')->get();
                foreach($allEmployee as $employee) {
                    $employee->notify(new CustomNotification($notificationData));
                }
            } else if($request->type == 'Single Employee') {
                $employee = User::where('id', $request->user_id)->first();
                $employee->notify(new CustomNotification($notificationData));
            } else if($request->type == 'All User') {
                $allUser = User::where('user_type', 'Frontend')->whereIn('status', ['Active', 'Blocked'])->get();
                foreach($allUser as $user) {
                    $user->notify(new CustomNotification($notificationData));
                }
            } else if($request->type == 'Single User') {
                $user = User::where('id', $request->user_id)->first();
                $user->notify(new CustomNotification($notificationData));
            }

            return response()->json([
                'status' => 200,
                'message' => 'Notification sent successfully!'
            ]);
        }
    }
}
