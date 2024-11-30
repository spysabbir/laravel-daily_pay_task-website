<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportReply;
use App\Models\Support;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\ReportReplyNotification;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Events\SupportEvent;
use App\Models\Contact;
use App\Models\PostTask;
use App\Models\UserStatus;
use App\Models\Withdraw;
use App\Notifications\UserStatusNotification;
use Carbon\Carbon;
use App\Models\UserDetail;

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
        $userDetails = UserDetail::where('user_id', $user->id)->latest()->take(5)->get();
        return view('profile.edit', compact('user', 'userDetails'));
    }

    public function profileSetting(Request $request)
    {
        $user = $request->user();
        return view('profile.setting', compact('user'));
    }

    // User List
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
                ->editColumn('last_login', function ($row) {
                    return '
                        <span class="badge text-white bg-dark">' . date('d M, Y  h:i:s A', strtotime($row->last_login_at)) ?? 'N/A' . '</span>
                        ';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-info bg-dark">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->editColumn('status', function ($row) {
                    return '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs statusBtn" data-bs-toggle="modal" data-bs-target=".statusModal">Status Details</button>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                    <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>
                    ';
                return $btn;
                })
                ->rawColumns(['last_login', 'created_at', 'status', 'action'])
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
                    return '
                        <span class="badge text-white bg-dark">' . date('d M, Y  h:i:s A', strtotime($row->last_login_at)) ?? 'N/A' . '</span>
                        ';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-info bg-dark">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                    <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>
                    ';
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
                ->editColumn('last_login', function ($row) {
                    return '
                        <span class="badge text-white bg-dark">' . date('d M, Y  h:i:s A', strtotime($row->last_login_at)) ?? 'N/A' . '</span>
                        ';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-info bg-dark">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->editColumn('status', function ($row) {
                    return '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs statusBtn" data-bs-toggle="modal" data-bs-target=".statusModal">Status Details</button>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                    <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>
                    ';
                return $btn;
                })
                ->rawColumns(['last_login', 'created_at', 'status', 'action'])
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
                ->editColumn('last_login', function ($row) {
                    return '
                        <span class="badge text-white bg-dark">' . date('d M, Y  h:i:s A', strtotime($row->last_login_at)) ?? 'N/A' . '</span>
                        ';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-info bg-dark">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->editColumn('status', function ($row) {
                    return '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs statusBtn" data-bs-toggle="modal" data-bs-target=".statusModal">Status Details</button>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                    <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>
                    ';
                return $btn;
                })
                ->rawColumns(['last_login', 'created_at', 'status', 'action'])
                ->make(true);
        }

        return view('backend.user.banned');
    }

    public function userView(string $id)
    {
        $user = User::where('id', $id)->first();
        return view('backend.user.show', compact('user'));
    }

    public function userStatus(string $id)
    {
        $user = User::where('id', $id)->first();
        $userStatuses = UserStatus::where('user_id', $id)->get();
        return view('backend.user.status', compact('user', 'userStatuses'));
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
                    $btn = '
                        <button type="button" data-id="'.$row->id.'" class="btn bg-success btn-xs restoreBtn">Restore</button>
                        <button type="button" data-id="'.$row->id.'" class="btn bg-danger btn-xs forceDeleteBtn">Delete</button>
                    ';
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

    // Report List
    public function reportPending(Request $request)
    {
        if ($request->ajax()) {
            $reportedUsers = Report::where('status', 'Pending');

            if ($request->report_id) {
                $reportedUsers->where('id', $request->report_id);
            }

            if ($request->type) {
                $reportedUsers->where('type', $request->type);
            }

            $query = $reportedUsers->select('reports.*')->orderBy('created_at', 'desc');

            $reportedList = $query->get();

            return DataTables::of($reportedList)
                ->addIndexColumn()
                ->editColumn('type', function ($row) {
                    if ($row->type == 'User') {
                        return '<span class="badge bg-success text-white">'.$row->type.'</span>';
                    } else if ($row->type == 'Post Task') {
                        return '<span class="badge bg-info text-white">'.$row->type.'</span>';
                    } else if ($row->type == 'Proof Task') {
                        return '<span class="badge bg-primary text-white">'.$row->type.'</span>';
                    }
                })
                ->editColumn('reported_user', function ($row) {
                    return '
                        <span class="badge bg-dark text-white">'.$row->reported->name.'</span>
                    ';
                })
                ->editColumn('reported_by', function ($row) {
                    return '
                        <span class="badge bg-dark text-white">'.$row->reportedBy->name.'</span>
                    ';
                })
                ->editColumn('created_at', function ($row) {
                    return date('d M Y h:i A', strtotime($row->created_at));
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                    ';
                    return $action;
                })
                ->rawColumns(['type', 'reported_user', 'reported_by', 'status', 'action'])
                ->make(true);
        }
        return view('backend.report.pending');
    }

    public function reportResolved(Request $request)
    {
        if ($request->ajax()) {
            $reportedUsers = Report::where('status', 'Resolved');

            if ($request->report_id) {
                $reportedUsers->where('id', $request->report_id);
            }

            if ($request->type) {
                $reportedUsers->where('type', $request->type);
            }

            $query = $reportedUsers->select('reports.*');

            $reportedList = $query->get();

            return DataTables::of($reportedList)
                ->addIndexColumn()
                ->editColumn('type', function ($row) {
                    if ($row->type == 'User') {
                        return '<span class="badge bg-success text-white">'.$row->type.'</span>';
                    } else if ($row->type == 'Post Task') {
                        return '<span class="badge bg-info text-white">'.$row->type.'</span>';
                    } else if ($row->type == 'Proof Task') {
                        return '<span class="badge bg-primary text-white">'.$row->type.'</span>';
                    }
                })
                ->editColumn('reported_user', function ($row) {
                    return '
                        <span class="text-info">'.$row->reported->name.'</span> <br>
                    ';
                })
                ->editColumn('reported_by', function ($row) {
                    return '
                        <span class="text-info">'.$row->reportedBy->name.'</span> <br>
                    ';
                })
                ->editColumn('created_at', function ($row) {
                    return date('d M Y h:i A', strtotime($row->created_at));
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                    ';
                    return $action;
                })
                ->rawColumns(['type', 'reported_user', 'reported_by', 'status', 'action'])
                ->make(true);
        }
        return view('backend.report.resolved');
    }

    public function reportView(string $id)
    {
        $report = Report::where('id', $id)->first();
        $report_reply = ReportReply::where('report_id', $id)->first();
        return view('backend.report.view', compact('report', 'report_reply'));
    }

    public function reportReply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reply' => 'required',
            'reply_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $report = Report::findOrFail($request->report_id);
            $report->update([
                'status' => 'Resolved',
            ]);

            $photo_name = null;
            if ($request->file('reply_photo')) {
                $manager = new ImageManager(new Driver());
                $photo_name = $report->user_id."-report_reply_photo_".date('YmdHis').".".$request->file('reply_photo')->getClientOriginalExtension();
                $image = $manager->read($request->file('reply_photo'));
                $image->toJpeg(80)->save(base_path("public/uploads/report_photo/").$photo_name);
            }

            $report_reply = new ReportReply();
            $report_reply->report_id = $request->report_id;
            $report_reply->reply = $request->reply;
            $report_reply->reply_photo = $photo_name;
            $report_reply->resolved_by = auth()->user()->id;
            $report_reply->resolved_at = now();
            $report_reply->save();

            $user = User::findOrFail($report->reported_by);

            $user->notify(new ReportReplyNotification($report, $report_reply));

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    // Support
    public function support()
    {
        $users = User::where('user_type', 'Frontend')->where('status', 'Active')->get();
        $unreadSupportUserIds = Support::select('sender_id')->groupBy('sender_id')->where('receiver_id', 1)->where('status', 'Unread')->get();
        $unreadSupportUsers = User::whereIn('id', $unreadSupportUserIds)->where('user_type', 'Frontend')->where('status', 'Active')->get();
        return view('backend.support.index', compact('users', 'unreadSupportUsers'));
    }

    public function getUserSupports($userId)
    {
        $user = User::find($userId);
        $messages = Support::where('sender_id', $userId)->orWhere('receiver_id', $userId)->orderBy('created_at', 'asc')->get();

        // Update the status of the messages to read
        $messages->where('receiver_id', Auth::id())->where('status', 'Unread')->each(function ($message) {
            $message->status = 'Read';
            $message->save();
        });

        // Format the response for the front-end
        $formattedMessages = $messages->map(function ($message) use ($user) {
            return [
                'message' => $message->message,
                'photo' => $message->photo,
                'status' => $message->status,
                'created_at' => $message->created_at->diffForHumans(),
                'profile_photo' => asset('uploads/profile_photo/' . $user->profile_photo),
                'sender_type' => $message->sender_id == auth()->id() ? 'me' : 'friend',
            ];
        });

        return response()->json([
            'name' => $user->name,
            'profile_photo' => asset('uploads/profile_photo/' . $user->profile_photo),
            'last_active' => Carbon::parse($user->last_login_at)->diffForHumans(),
            'active_status' => Carbon::parse($user->last_login_at)->diffInMinutes(now()) <= 5 ? 'online' : 'offline',
            'messages' => $formattedMessages
        ]);
    }

    public function getSearchSupportUser(Request $request)
    {
        $tab = $request->get('tab'); // Get the active tab
        $searchUserId = $request->input('searchUserId', null);
        $receiverId = 1; // Replace with the actual logged-in user's ID or dynamic value if applicable

        $supportUsers = []; // Default empty array for response

        if ($tab === 'unread-supports-users-tab') {
            // Query for unread support users
            $unreadSupportUserIds = Support::select('sender_id')
                ->groupBy('sender_id')
                ->where('receiver_id', $receiverId)
                ->where('status', 'Unread')
                ->pluck('sender_id');

            $query = User::whereIn('id', $unreadSupportUserIds)
                ->where('user_type', 'Frontend')
                ->where('status', 'Active');

            if (!empty($searchUserId)) {
                $query->where('id', $searchUserId);
            }

            $supportUsers = $query->get();
        } elseif ($tab === 'all-users-tab') {
            // Query for all users
            $query = User::where('user_type', 'Frontend')
                ->where('status', 'Active');

            if (!empty($searchUserId)) {
                $query->where('id', $searchUserId);
            }

            $supportUsers = $query->get();
        } else {
            return response()->json(['error' => 'Invalid tab selected'], 400);
        }

        // Process and prepare response
        $response = $supportUsers->map(function ($user) {
            $latestSupport = Support::where('sender_id', $user->id)->latest()->first();
            $message = 'No message';
            if ($latestSupport) {
                if ($latestSupport->message) {
                    $message = strlen($latestSupport->message) > 50
                        ? substr($latestSupport->message, 0, 50) . '...'
                        : $latestSupport->message;
                } elseif ($latestSupport->photo) {
                    $message = '<strong><i data-feather="image" class="icon-sm text-primary"></i> Image</strong>';
                }
            }

            return [
                'id' => $user->id,
                'name' => $user->name,
                'status' => $user->status,
                'profile_photo' => $user->profile_photo,
                'active_status' => Carbon::parse($user->last_login_at)->diffInMinutes(now()) <= 5 ? 'online' : 'offline',
                'message' => $message,
                'send_at' => $latestSupport ? $latestSupport->created_at->diffForHumans() : 'No message',
                'support_count' => Support::where('sender_id', $user->id)->where('status', 'Unread')->count(),
            ];
        });

        return response()->json(['supportUsers' => $response, 'tab' => $tab]);
    }


    public function supportSendMessageReply(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'nullable|string|max:5000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!$request->message && !$request->file('photo')) {
                $validator->errors()->add('validator_alert', 'Either a message or a photo is required.');
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()->toArray()
            ]);
        } else {
            $photo_name = null;
            if ($request->file('photo')) {
                $manager = new ImageManager(new Driver());
                $photo_name = Auth::id() . "-support_photo_" . date('YmdHis') . "." . $request->file('photo')->getClientOriginalExtension();
                $image = $manager->read($request->file('photo'));
                $image->toJpeg(80)->save(base_path("public/uploads/support_photo/") . $photo_name);
            }

            $support = Support::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $userId,
                'message' => $request->message,
                'photo' => $photo_name,
            ]);

            Support::where('receiver_id', Auth::id())->where('status', 'Unread')->each(function ($message) {
                $message->status = 'Read';
                $message->save();
            });

            SupportEvent::dispatch($support);

            return response()->json([
                'status' => 200,
                'support' => [
                    'message' => $support->message,
                    'photo' => $support->photo,
                    'sender_id' => $support->sender_id,
                    'created_at' => Carbon::parse($support->created_at)->diffForHumans(),
                ],
            ]);
        }
    }

    // Contact
    public function contact(Request $request)
    {
        if ($request->ajax()) {
            $query = Contact::select('contacts.*');

            $query->orderBy('created_at', 'desc');

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $allContact = $query->get();

            return DataTables::of($allContact)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    if ($row->status == 'Read') {
                        return '<span class="badge text-white bg-success">Read</span>';
                    } else {
                        return '<span class="badge text-white bg-danger">Unread</span>';
                    }
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-info bg-dark">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                    <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>
                    ';
                return $btn;
                })
                ->rawColumns(['status', 'created_at', 'action'])
                ->make(true);
        }

        return view('backend.contact.index');
    }

    public function contactView(string $id)
    {
        $contact = Contact::where('id', $id)->first();
        $contact->status = 'Read';
        $contact->save();

        return view('backend.contact.show', compact('contact'));
    }

    public function contactDelete(string $id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
    }
}
