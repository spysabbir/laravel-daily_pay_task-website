<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::where('user_type', 'Backend');

            if ($request->role) {
                $query->whereHas('roles', fn($q) => $q->where('roles.id', $request->role));
            }

            if ($request->status) {
                $query->where('users.status', $request->status);
            }

            $allEmployee = $query->orderBy('created_at', 'desc')->get();

            return DataTables::of($allEmployee)
                ->addIndexColumn()
                ->addColumn('roles', fn($row) => $row->roles->map(fn($role) => '<span class="badge bg-primary">' . $role->name . '</span>')->implode(' '))
                ->editColumn('status', fn($row) => '
                    <span class="badge bg-' . ($row->status == 'Active' ? 'success' : 'warning') . '">' . $row->status . '</span>
                    <button type="button" class="btn btn-' . ($row->status == 'Active' ? 'warning' : 'success') . ' btn-xs statusBtn">' . ($row->status == 'Active' ? 'Deactivate' : 'Activate') . '</button>
                ')
                ->addColumn('action', function ($row) {
                    $btns = '<button type="button" class="btn btn-primary btn-xs viewBtn">View</button>';
                    if (!$row->roles->contains('name', 'Super Admin')) {
                        $btns .= '<button type="button" class="btn btn-info btn-xs editBtn">Edit</button>';
                    }
                    $btns .= '<button type="button" class="btn btn-danger btn-xs deleteBtn">Delete</button>';
                    return $btns;
                })
                ->rawColumns(['roles', 'status', 'action'])
                ->make(true);
        }

        $roles = Role::all();
        return view('backend.employee.index', compact('roles'));
    }

    public function store (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required',
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
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
        $employee = User::where('id', $id)->first();
        foreach($employee->roles as $role) {
            $employee_role = $role;
        }
        return view('backend.employee.show', compact('employee', 'employee_role'));
    }

    public function edit(string $id)
    {
        $employee = User::where('id', $id)->first();
        foreach($employee->roles as $role) {
            $employee_role = $role;
        }
        return response()->json([
            'employee' => $employee,
            'role' => $employee_role,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required',
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:users,email,'.$id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $user = User::findOrFail($id);
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            if ($request->role) {
                $user->roles()->detach();
                $user->roles()->sync($request->role);
            }

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
                    $btn = '
                        <button type="button" data-id="'.$row->id.'" class="btn bg-success btn-xs restoreBtn">Restore</button>
                        <button type="button" data-id="'.$row->id.'" class="btn bg-danger btn-xs forceDeleteBtn">Delete</button>
                    ';
                    return $btn;
                })
                ->rawColumns(['roles', 'action'])
                ->make(true);
        }

        return view('backend.employee.index');
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
}
