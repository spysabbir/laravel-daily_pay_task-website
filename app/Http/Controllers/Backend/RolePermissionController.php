<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RolePermissionController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('role-permission.index') , only:['index', 'show']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('role-permission.edit') , only:['edit', 'update']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('role-permission.destroy'), only:['destroy']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Role::select('roles.*');

            $query->orderBy('created_at', 'desc');

            $roles = $query->get();

            return DataTables::of($roles)
                ->addIndexColumn()
                ->addColumn('permissions', function ($row) {
                    $permissions = $row->permissions;
                    $badgeTags = '';
                    foreach ($permissions as $permission) {
                        $badgeTags .= '<span class="badge bg-info mx-1">' . $permission->name . '</span>';
                    }
                    return $badgeTags;
                })
                ->addColumn('action', function ($row) {
                    $isSuperAdmin = $row->name == 'Super Admin';
                    $hasSuperAdminPermission = auth()->user()->hasRole('Super Admin');

                    if ($isSuperAdmin && !$hasSuperAdminPermission) {
                        return '<span class="badge bg-dark">N/A</span>';
                    }

                    $canAssign = auth()->user()->can('role-permission.edit');
                    $canDelete = auth()->user()->can('role-permission.destroy');

                    $viewBtn = '<a href="' . route('backend.role-permission.show', encrypt($row->id)) . '" class="btn btn-info btn-xs">View</a>';
                    $assigningBtn = $canAssign
                        ? '<a href="' . route('backend.role-permission.edit', encrypt($row->id)) . '" class="btn btn-success btn-xs">Assigning</a>'
                        : '';
                    $deleteBtn = $canDelete
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Remove All</button>'
                        : '';

                    return $viewBtn . ' ' . $assigningBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['permissions', 'action'])
                ->make(true);
        }

        return view('backend.role-permission.index');
    }

    public function show(string $id)
    {
        $id = decrypt($id);
        $role = Role::findOrFail($id);
        $permissions = $role->permissions;

        $groupedData = [];
        foreach ($permissions as $item) {
            $groupedData[$item['group_name']][] = $item['name'];
        }

        return view('backend.role-permission.show', compact('role', 'groupedData'));
    }

    public function edit(string $id)
    {
        $id = decrypt($id);
        $role = Role::findOrFail($id);
        $permission_groups = User::getPermissionsGroup();
        return view('backend.role-permission.edit', compact('role', 'permission_groups'));
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'permission_id' => 'array',
            'permission_id.*' => 'exists:permissions,id',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            $role = Role::findOrFail($id);

            $role->permissions()->sync($request->permission_id);

            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        $role->permissions()->detach();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'status' => 200,
        ]);
    }
}
