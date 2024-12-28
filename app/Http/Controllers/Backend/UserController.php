<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\User;
use App\Models\UserStatus;
use App\Notifications\UserStatusNotification;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;


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

                    $viewBtn = '<a href="' . route('backend.user.show', $row->id) . '" class="btn btn-primary btn-xs">View</a>';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>'
                        : '';
                    $statusBtn = $statusPermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs statusBtn" data-bs-toggle="modal" data-bs-target=".statusModal">Status Details</button>'
                        : '';

                    $btn = $viewBtn . ' ' . $deleteBtn . ' ' . $statusBtn;
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

                    $viewBtn = '<a href="' . route('backend.user.show', $row->id) . '" class="btn btn-primary btn-xs">View</a>';
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

                    $viewBtn = '<a href="' . route('backend.user.show', $row->id) . '" class="btn btn-primary btn-xs">View</a>';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>'
                        : '';
                    $statusBtn = $statusPermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs statusBtn" data-bs-toggle="modal" data-bs-target=".statusModal">Status Details</button>'
                        : '';

                    $btn = $viewBtn . ' ' . $deleteBtn . ' ' . $statusBtn;
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
                    $statusPermission = auth()->user()->can('user.status');

                    $viewBtn = '<a href="' . route('backend.user.show', $row->id) . '" class="btn btn-primary btn-xs">View</a>';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>'
                        : '';
                    $statusBtn = $statusPermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs statusBtn" data-bs-toggle="modal" data-bs-target=".statusModal">Status Details</button>'
                        : '';

                    $btn = $viewBtn . ' ' . $deleteBtn . ' ' . $statusBtn;
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
                    $restorePermission = auth()->user()->can('user.restore');
                    $deletePermission = auth()->user()->can('user.delete');

                    $restoreBtn = $restorePermission
                        ? '<button type="button" data-id="'.$row->id.'" class="btn bg-success btn-xs restoreBtn">Restore</button>'
                        : '';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="'.$row->id.'" class="btn bg-danger btn-xs forceDeleteBtn">Delete</button>'
                        : '';

                    $btn = $restoreBtn . ' ' . $deleteBtn;
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
