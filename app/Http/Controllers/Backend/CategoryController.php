<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('category.index') , only:['index']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('category.create') , only:['store']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('category.edit') , only:['edit', 'update']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('category.destroy'), only:['destroy']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('category.trash') , only:['trash']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('category.restore') , only:['restore']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('category.delete') , only:['delete']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('category.status') , only:['status']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Category::select('categories.*');

            $query->orderBy('created_at', 'desc');

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $categories = $query->get();

            return DataTables::of($categories)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    if ($row->status == 'Active') {
                        $status = '
                        <span class="badge bg-success">' . $row->status . '</span>
                        <button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs statusBtn">Deactive</button>
                        ';
                    } else {
                        $status = '
                        <span class="badge text-white bg-warning">' . $row->status . '</span>
                        <button type="button" data-id="' . $row->id . '" class="btn btn-success btn-xs statusBtn">Active</button>
                        ';
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs editBtn" data-bs-toggle="modal" data-bs-target=".editModal">Edit</button>
                        <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>
                        ';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('backend.category.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            Category::create([
                'name' => $request->name,
                'created_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function edit(string $id)
    {
        $category = Category::where('id', $id)->first();
        return response()->json($category);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            Category::where('id', $id)->update([
                'name' => $request->name,
                'updated_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = Category::onlyTrashed();

            $trashCategories = $query->orderBy('deleted_at', 'desc')->get();

            return DataTables::of($trashCategories)
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

        return view('backend.category.index');
    }

    public function restore(string $id)
    {
        Category::onlyTrashed()->where('id', $id)->update([
            'deleted_by' => NULL
        ]);

        Category::onlyTrashed()->where('id', $id)->restore();
    }

    public function delete(string $id)
    {
        $category = Category::onlyTrashed()->where('id', $id)->first();
        $category->forceDelete();
    }

    public function status(string $id)
    {
        $category = Category::findOrFail($id);

        if ($category->status == "Active") {
            $category->status = "Inactive";
        } else {
            $category->status = "Active";
        }

        $category->updated_by = auth()->user()->id;
        $category->save();
    }
}
