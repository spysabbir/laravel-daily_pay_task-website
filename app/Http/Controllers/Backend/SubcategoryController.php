<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class SubcategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Subcategory::select('subcategories.*');

            $query->orderBy('created_at', 'desc');

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $subcategories = $query->get();

            return DataTables::of($subcategories)
                ->addIndexColumn()
                ->addColumn('category_name', function ($row) {
                    return $row->category->name;
                })
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
                ->rawColumns(['category_name', 'status', 'action'])
                ->make(true);
        }

        $categories = Category::where('status', 'Active')->get();

        return view('backend.subcategory.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:subcategories',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            Subcategory::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'created_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function edit(string $id)
    {
        $subcategory = Subcategory::where('id', $id)->first();
        return response()->json($subcategory);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:subcategories,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            Subcategory::where('id', $id)->update([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'updated_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function destroy(string $id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $subcategory->delete();
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = Subcategory::onlyTrashed();

            $trashSubcategories = $query->orderBy('deleted_at', 'desc')->get();

            return DataTables::of($trashSubcategories)
                ->addIndexColumn()
                ->addColumn('category_name', function ($row) {
                    return $row->category->name;
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                        <button type="button" data-id="'.$row->id.'" class="btn bg-success btn-xs restoreBtn">Restore</button>
                        <button type="button" data-id="'.$row->id.'" class="btn bg-danger btn-xs forceDeleteBtn">Delete</button>
                    ';
                    return $btn;
                })
                ->rawColumns(['category_name', 'action'])
                ->make(true);
        }

        return view('backend.subcategory.index');
    }

    public function restore(string $id)
    {
        Subcategory::onlyTrashed()->where('id', $id)->update([
            'deleted_by' => NULL
        ]);

        Subcategory::onlyTrashed()->where('id', $id)->restore();
    }

    public function delete(string $id)
    {
        $subcategory = Subcategory::onlyTrashed()->where('id', $id)->first();
        $subcategory->forceDelete();
    }

    public function status(string $id)
    {
        $subcategory = Subcategory::findOrFail($id);

        if ($subcategory->status == "Active") {
            $subcategory->status = "Inactive";
        } else {
            $subcategory->status = "Active";
        }

        $subcategory->updated_by = auth()->user()->id;
        $subcategory->save();
    }
}
