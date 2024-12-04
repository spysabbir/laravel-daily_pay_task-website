<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PermissionController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('permission.index') , only:['index']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('permission.create') , only:['store']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('permission.edit') , only:['edit', 'update']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('permission.destroy'), only:['destroy']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Permission::select('permissions.*');

            $query->orderBy('created_at', 'desc');

            if ($request->group_name) {
                $query->where('group_name', $request->group_name);
            }

            $permissions = $query->get();

            return DataTables::of($permissions)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    if ($row->created_at != null) {
                        $editPermission = auth()->user()->can('permission.edit');
                        $deletePermission = auth()->user()->can('permission.destroy');

                        $editBtn = $editPermission
                            ? '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs editBtn" data-bs-toggle="modal" data-bs-target=".editModal">Edit</button>'
                            : '';
                        $deleteBtn = $deletePermission
                            ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>'
                            : '';

                        $btn = $editBtn . ' ' . $deleteBtn;
                    } else {
                        $btn = '<span class="badge bg-danger">N/A</span>';
                    }
                    
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // Need to return only group_name
        $permissions = Permission::select('group_name')->groupBy('group_name')->get();
        return view('backend.permission.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_name' => 'required|string|max:255',
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            Permission::create($request->all());

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function edit(string $id)
    {
        $permission = Permission::where('id', $id)->first();
        return response()->json($permission);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'group_name' => 'required|string|max:255',
            'name' => 'required|string|max:255|unique:permissions,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $permission = Permission::findOrFail($id);
            $permission->update($request->all());

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
    }
}
