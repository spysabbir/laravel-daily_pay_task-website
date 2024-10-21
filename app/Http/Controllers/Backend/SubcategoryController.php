<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Category;
use Illuminate\Validation\Rule;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = SubCategory::select('sub_categories.*');

            $query->orderBy('created_at', 'desc');

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $sub_categories = $query->get();

            return DataTables::of($sub_categories)
                ->addIndexColumn()
                ->addColumn('category_name', function ($row) {
                    return $row->category->name ?? 'N/A';
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

        return view('backend.sub_category.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => ['required', 'string', 'max:255',
                Rule::unique('sub_categories')->where(function ($query) use ($request) {
                    return $query->where('category_id', $request->category_id);
                }),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray(),
            ]);
        } else {
            SubCategory::create([
                'category_id' => $request->category_id,
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
        $sub_category = SubCategory::where('id', $id)->first();
        return response()->json($sub_category);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => ['required', 'string', 'max:255',
                Rule::unique('sub_categories')->where(function ($query) use ($request, $id) {
                    return $query->where('category_id', $request->category_id)->where('id', '!=', $id);
                }),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray(),
            ]);
        } else {
            SubCategory::where('id', $id)->update([
                'category_id' => $request->category_id,
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
        $sub_category = SubCategory::findOrFail($id);
        $sub_category->delete();
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = SubCategory::onlyTrashed();

            $trash_sub_categories = $query->orderBy('deleted_at', 'desc')->get();

            return DataTables::of($trash_sub_categories)
                ->addIndexColumn()
                ->addColumn('category_name', function ($row) {
                    return $row->category->name ?? 'N/A';
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

        return view('backend.sub_category.index');
    }

    public function restore(string $id)
    {
        SubCategory::onlyTrashed()->where('id', $id)->update([
            'deleted_by' => NULL
        ]);

        SubCategory::onlyTrashed()->where('id', $id)->restore();
    }

    public function delete(string $id)
    {
        $sub_category = SubCategory::onlyTrashed()->where('id', $id)->first();
        $sub_category->forceDelete();
    }

    public function status(string $id)
    {
        $sub_category = SubCategory::findOrFail($id);

        if ($sub_category->status == "Active") {
            $sub_category->status = "Inactive";
        } else {
            $sub_category->status = "Active";
        }

        $sub_category->updated_by = auth()->user()->id;
        $sub_category->save();
    }
}
