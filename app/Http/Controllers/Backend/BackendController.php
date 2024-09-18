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


class BackendController extends Controller
{
    public function dashboard()
    {
        return view('backend.dashboard');
    }

    public function profileEdit(Request $request)
    {
        $user = $request->user();
        return view('profile.edit', compact('user'));
    }

    public function profileSetting(Request $request)
    {
        $user = $request->user();
        return view('profile.setting', compact('user'));
    }

    public function userActiveList(Request $request)
    {
        if ($request->ajax()) {
            $query = User::where('user_type', 'Frontend')->where('status', 'Active');

            $query->select('users.*')->orderBy('created_at', 'desc');

            $allUser = $query->get();

            return DataTables::of($allUser)
                ->addIndexColumn()
                ->editColumn('last_login', function ($row) {
                    return '
                        <span class="badge text-white bg-dark">' . date('F j, Y  H:i:s A', strtotime($row->last_login_at)) ?? 'N/A' . '</span>
                        ';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-info bg-dark">' . date('F j, Y  H:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs editBtn" data-bs-toggle="modal" data-bs-target=".editModal">Edit</button>
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                    <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>
                    ';
                return $btn;
                })
                ->rawColumns(['last_login', 'created_at', 'action'])
                ->make(true);
        }

        return view('backend.user.active');
    }

    public function userInactiveList(Request $request)
    {
        if ($request->ajax()) {
            $query = User::where('user_type', 'Frontend')->where('status', 'Inactive');

            $query->select('users.*')->orderBy('created_at', 'desc');

            $allUser = $query->get();

            return DataTables::of($allUser)
                ->addIndexColumn()
                ->editColumn('last_login', function ($row) {
                    return '
                        <span class="badge text-white bg-dark">' . date('F j, Y  H:i:s A', strtotime($row->last_login_at)) ?? 'N/A' . '</span>
                        ';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-info bg-dark">' . date('F j, Y  H:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs editBtn" data-bs-toggle="modal" data-bs-target=".editModal">Edit</button>
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

            $query->select('users.*')->orderBy('created_at', 'desc');

            $allUser = $query->get();

            return DataTables::of($allUser)
                ->addIndexColumn()
                ->editColumn('last_login', function ($row) {
                    return '
                        <span class="badge text-white bg-dark">' . date('F j, Y  H:i:s A', strtotime($row->last_login_at)) ?? 'N/A' . '</span>
                        ';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-info bg-dark">' . date('F j, Y  H:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs editBtn" data-bs-toggle="modal" data-bs-target=".editModal">Edit</button>
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                    <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>
                    ';
                return $btn;
                })
                ->rawColumns(['last_login', 'created_at', 'action'])
                ->make(true);
        }

        return view('backend.user.blocked');
    }

    public function userBannedList(Request $request)
    {
        if ($request->ajax()) {
            $query = User::where('user_type', 'Frontend')->where('status', 'Banned');

            $query->select('users.*')->orderBy('created_at', 'desc');

            $allUser = $query->get();

            return DataTables::of($allUser)
                ->addIndexColumn()
                ->editColumn('last_login', function ($row) {
                    return '
                        <span class="badge text-white bg-dark">' . date('F j, Y  H:i:s A', strtotime($row->last_login_at)) ?? 'N/A' . '</span>
                        ';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-info bg-dark">' . date('F j, Y  H:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs editBtn" data-bs-toggle="modal" data-bs-target=".editModal">Edit</button>
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                    <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>
                    ';
                return $btn;
                })
                ->rawColumns(['last_login', 'created_at', 'action'])
                ->make(true);
        }

        return view('backend.user.banned');
    }

    public function userView(string $id)
    {
        $user = User::where('id', $id)->first();
        return view('backend.user.show', compact('user'));
    }

    public function userEdit(string $id)
    {
        $user = User::where('id', $id)->first();
        return response()->json([
            'user' => $user,
        ]);
    }

    public function userUpdate(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $user = User::findOrFail($id);
            $user->update([
                'status' => $request->status,
                'updated_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
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

    public function reportUserPending(Request $request)
    {
        if ($request->ajax()) {
            $reportedUsers = Report::where('status', 'Pending');

            $query = $reportedUsers->select('reports.*');

            $reportedList = $query->get();

            return DataTables::of($reportedList)
                ->addIndexColumn()
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
                ->rawColumns(['reported_user', 'reported_by', 'status', 'action'])
                ->make(true);
        }
        return view('backend.report_user.pending');
    }

    public function reportUserResolved(Request $request)
    {
        if ($request->ajax()) {
            $reportedUsers = Report::where('status', 'Resolved');

            $query = $reportedUsers->select('reports.*');

            $reportedList = $query->get();

            return DataTables::of($reportedList)
                ->addIndexColumn()
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
                ->rawColumns(['reported_user', 'reported_by', 'status', 'action'])
                ->make(true);
        }
        return view('backend.report_user.resolved');
    }

    public function reportUserView(string $id)
    {
        $report = Report::where('id', $id)->first();
        $report_reply = ReportReply::where('report_id', $id)->first();
        return view('backend.report_user.view', compact('report', 'report_reply'));
    }

    public function reportUserReply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reply' => 'required',
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

            $report_reply = new ReportReply();
            $report_reply->report_id = $request->report_id;
            $report_reply->reply = $request->reply;
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

    public function support()
    {
        $users = User::where('user_type', 'Frontend')->where('status', 'Active')->get();
        $supportUserIds = Support::select('sender_id')->groupBy('sender_id')->get();
        $supportUsers = User::whereIn('id', $supportUserIds)->where('user_type', 'Frontend')->where('status', 'Active')->get();
        return view('backend.support.index', compact('users', 'supportUsers'));
    }

    public function getUserChat($userId)
    {
        $user = User::find($userId);
        $messages = Support::where('sender_id', $userId)->orWhere('receiver_id', $userId)->orderBy('created_at', 'asc')->get();

        // Format the response for the front-end
        $formattedMessages = $messages->map(function ($message) use ($user) {
            return [
                'text' => $message->message,
                'time' => $message->created_at->format('g:i A'),
                'profile_photo' => asset('uploads/profile_photo/' . $user->profile_photo),
                'sender_type' => $message->sender_id == auth()->id() ? 'me' : 'friend',
            ];
        });

        return response()->json([
            'name' => $user->name,
            'profile_photo' => asset('uploads/profile_photo/' . $user->profile_photo),
            'role' => 'User', // If the role is available
            'messages' => $formattedMessages
        ]);
    }

    public function supportSendMessageReply(Request $request, $userId){
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:5000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            $photo_name = null;
            if ($request->file('photo')) {
                $manager = new ImageManager(new Driver());
                $photo_name = Auth::id()."-support_photo_".date('YmdHis').".".$request->file('photo')->getClientOriginalExtension();
                $image = $manager->read($request->file('photo'));
                $image->toJpeg(80)->save(base_path("public/uploads/support_photo/").$photo_name);
            }

            $support = Support::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $userId,
                'message' => $request->message,
                'photo' => $photo_name,
            ]);

            SupportEvent::dispatch($support);

            return response()->json([
                'status' => 200,
                'support' => $support,
            ]);
        }
    }
}
