<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('role.index') , only:['index']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('role.create') , only:['store']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('role.edit') , only:['edit', 'update']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('role.destroy'), only:['destroy']),
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
                ->addColumn('action', function ($row) {
                    $btn = '<span class="badge bg-dark">N/A</span>';

                    if ($row->name !== 'Super Admin') {
                        $editPermission = auth()->user()->can('role.edit');
                        $deletePermission = auth()->user()->can('role.destroy');

                        $editBtn = $editPermission
                            ? '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs editBtn" data-bs-toggle="modal" data-bs-target=".editModal">Edit</button>'
                            : '';
                        $deleteBtn = $deletePermission
                            ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>'
                            : '';

                        $btn = $editBtn . ' ' . $deleteBtn;
                    }

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.role.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            Role::create($request->all());

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function edit(string $id)
    {
        $role = Role::where('id', $id)->first();
        return response()->json($role);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $role = Role::findOrFail($id);
            $role->update($request->all());

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
    }
}
