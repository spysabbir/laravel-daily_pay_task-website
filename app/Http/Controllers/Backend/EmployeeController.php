<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Carbon\Carbon;

class EmployeeController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('employee.index') , only:['index', 'show']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('employee.inactive') , only:['inactive', 'show']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('employee.create') , only:['store']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('employee.edit') , only:['edit', 'update']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('employee.destroy'), only:['destroy']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('employee.trash') , only:['trash']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('employee.restore') , only:['restore']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('employee.delete') , only:['delete']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('employee.status') , only:['status']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::where('user_type', 'Backend')->with('roles')->where('status', 'Active')->whereNot('id', 1);

            if ($request->role) {
                $query->whereHas('roles', fn($q) => $q->where('roles.id', $request->role));
            }

            if ($request->user_id) {
                $query->where('id',  $request->user_id);
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

            // Clone the query for counts
            $totalUsersCount = (clone $query)->count();

            $allEmployee = $query->orderBy('created_at', 'desc')->get();

            return DataTables::of($allEmployee)
                ->addIndexColumn()
                ->editColumn('phone', function ($row) {
                    return $row->phone ?? 'N/A';
                })
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
                ->addColumn('roles', fn($row) => $row->roles->map(fn($role) => '<span class="badge bg-primary">' . $role->name . '</span>')->implode(' '))
                ->addColumn('action', function ($row) {
                    $canChangeStatus = auth()->user()->can('employee.status');
                    $editPermission = auth()->user()->can('employee.edit');
                    $deletePermission = auth()->user()->can('employee.destroy');

                    $viewBtn = '<a href="' . route('backend.employee.show', encrypt($row->id)) . '" class="btn btn-primary btn-xs">View</a>';
                    $statusBtn = $canChangeStatus
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs statusBtn">Deactive</button>'
                        : '';
                    $editBtn = $editPermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs editBtn" data-bs-toggle="modal" data-bs-target=".editModal">Edit</button>'
                        : '';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>'
                        : '';

                    $btn = $viewBtn . ' ' . $statusBtn . ' ' . $editBtn . ' ' . $deleteBtn;
                    return $btn;
                })
                ->with([
                    'totalUsersCount' => $totalUsersCount,
                ])
                ->rawColumns(['phone', 'last_activity_at', 'created_at', 'roles', 'action'])
                ->make(true);
        }

        $roles = Role::all();
        return view('backend.employee.active', compact('roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|array',
            'role.*' => 'exists:roles,id',
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email', 'regex:/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]+$/'],
            'password' => [
                'required', 'string', 'min:8', 'max:20',
                'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'
            ],
        ],[
            'email.regex' => 'The email must follow the format "****@****.***".',
            'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => 'Backend',
                'status' => 'Active',
                'created_by' => auth()->user()->id,
            ]);

            $user->roles()->sync($request->role);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function show(string $id)
    {
        $id = decrypt($id);
        $employee = User::where('id', $id)->withTrashed()->first();
        return view('backend.employee.show', compact('employee'));
    }

    public function edit(string $id)
    {
        $employee = User::where('id', $id)->first();

        return response()->json([
            'employee' => $employee,
            'role' => $employee->roles->pluck('id')->toArray()
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|array',
            'role.*' => 'exists:roles,id',
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $id, 'regex:/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]+$/'],
            'password' => [
                'nullable', 'string', 'min:8', 'max:20',
                'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'
            ],
        ],[
            'email.regex' => 'The email must follow the format " ****@****.*** ".',
            'password.regex' => 'The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $employee = User::findOrFail($id);
            $employee->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password ? Hash::make($request->password) : $employee->password,
                'updated_by' => auth()->user()->id,
            ]);

            $employee->roles()->sync($request->role);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->updated_by = auth()->user()->id;
        $user->deleted_by = auth()->user()->id;
        $user->save();
        $user->delete();
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = User::where('user_type', 'Backend')->onlyTrashed();

            $trashEmployee = $query->orderBy('deleted_at', 'desc')->get();

            return DataTables::of($trashEmployee)
                ->addIndexColumn()
                ->addColumn('roles', function ($row) {
                    $roles = $row->roles;
                    $badgeTags = '';
                    foreach ($roles as $role) {
                        $badgeTags .= '<span class="badge bg-info mr-1">' . $role->name . '</span>';
                    }
                    return $badgeTags;
                })
                ->addColumn('action', function ($row) {
                    $restorePermission = auth()->user()->can('employee.restore');
                    $deletePermission = auth()->user()->can('employee.delete');

                    $viewBtn = '<a href="' . route('backend.employee.show', encrypt($row->id)) . '" class="btn btn-primary btn-xs" target="_blank">View</a>';
                    $restoreBtn = $restorePermission
                        ? '<button type="button" data-id="'.$row->id.'" class="btn bg-success btn-xs restoreBtn">Restore</button>'
                        : '';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="'.$row->id.'" class="btn bg-danger btn-xs forceDeleteBtn">Delete</button>'
                        : '';

                    $btn = $restoreBtn . ' ' . $deleteBtn . ' ' . $viewBtn;
                    return $btn;
                })
                ->rawColumns(['roles', 'action'])
                ->make(true);
        }

        return view('backend.employee.active');
    }

    public function restore(string $id)
    {
        User::onlyTrashed()->where('id', $id)->update([
            'deleted_by' => NULL
        ]);

        User::onlyTrashed()->where('id', $id)->restore();
    }

    public function delete(string $id)
    {
        $user = User::onlyTrashed()->where('id', $id)->first();
        $user->forceDelete();
    }

    public function status(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->status == "Active") {
            $user->status = "Inactive";
        } else {
            $user->status = "Active";
        }

        $user->updated_by = auth()->user()->id;
        $user->save();
    }

    public function inactive(Request $request)
    {
        if ($request->ajax()) {
            $query = User::where('user_type', 'Backend')->where('status', 'Inactive')->with('roles');

            if ($request->role) {
                $query->whereHas('roles', fn($q) => $q->where('roles.id', $request->role));
            }

            if ($request->user_id) {
                $query->where('id',  $request->user_id);
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

            // Clone the query for counts
            $totalUsersCount = (clone $query)->count();

            $allEmployee = $query->orderBy('created_at', 'desc')->get();

            return DataTables::of($allEmployee)
                ->addIndexColumn()
                ->editColumn('phone', function ($row) {
                    return $row->phone ?? 'N/A';
                })
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
                ->addColumn('roles', fn($row) => $row->roles->map(fn($role) => '<span class="badge bg-primary">' . $role->name . '</span>')->implode(' '))
                ->addColumn('action', function ($row) {
                    $canChangeStatus = auth()->user()->can('employee.status');
                    $deletePermission = auth()->user()->can('employee.destroy');

                    $viewBtn = '<a href="' . route('backend.employee.show', encrypt($row->id)) . '" class="btn btn-primary btn-xs">View</a>';
                    $statusBtn = $canChangeStatus
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-success btn-xs statusBtn">Active</button>'
                        : '';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>'
                        : '';

                    $btn = $viewBtn . ' ' . $statusBtn . ' ' . $deleteBtn;
                    return $btn;
                })
                ->with([
                    'totalUsersCount' => $totalUsersCount,
                ])
                ->rawColumns(['phone', 'last_activity_at', 'created_at', 'roles', 'action'])
                ->make(true);
        }

        $roles = Role::all();
        return view('backend.employee.inactive', compact('roles'));
    }
}
