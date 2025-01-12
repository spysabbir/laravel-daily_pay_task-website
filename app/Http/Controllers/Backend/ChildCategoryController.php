<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ChildCategory;
use App\Models\SubCategory;
use App\Models\TaskPostCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ChildCategoryController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('child_category.index') , only:['index']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('child_category.create') , only:['store', 'getSubCategories']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('child_category.edit') , only:['edit', 'update']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('child_category.destroy'), only:['destroy']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('child_category.trash') , only:['trash']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('child_category.restore') , only:['restore']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('child_category.delete') , only:['delete']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('child_category.status') , only:['status']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ChildCategory::select('child_categories.*');

            $query->orderBy('created_at', 'desc');

            if ($request->category_id) {
                $query->where('category_id', $request->category_id);
            }
            if ($request->sub_category_id) {
                $query->where('sub_category_id', $request->sub_category_id);
            }
            if ($request->status) {
                $query->where('status', $request->status);
            }

            $child_categories = $query->get();

            return DataTables::of($child_categories)
                ->addIndexColumn()
                ->addColumn('category_name', function ($row) {
                    return $row->category->name ?? 'N/A';
                })
                ->editColumn('sub_category_name', function ($row) {
                    return $row->subCategory->name ?? 'N/A';
                })
                ->editColumn('status', function ($row) {
                    $canChangeStatus = auth()->user()->can('child_category.status');

                    $badgeClass = $row->status == 'Active' ? 'bg-success' : 'bg-warning text-white';
                    $badgeText = $row->status == 'Active' ? 'Active' : 'Deactive';

                    $button = '';
                    if ($canChangeStatus) {
                        $button = $row->status == 'Active'
                            ? '<button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs statusBtn">Deactive</button>'
                            : '<button type="button" data-id="' . $row->id . '" class="btn btn-success btn-xs statusBtn">Active</button>';
                    }

                    return '
                        <span class="badge ' . $badgeClass . '">' . $badgeText . '</span>
                        ' . $button . '
                    ';
                })
                ->addColumn('action', function ($row) {
                    $editPermission = auth()->user()->can('child_category.edit');
                    $deletePermission = auth()->user()->can('child_category.destroy');

                    $editBtn = $editPermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs editBtn">Edit</button>'
                        : '';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>'
                        : '';

                    $btn = $editBtn . ' ' . $deleteBtn;
                    return $btn;
                })
                ->rawColumns(['category_name', 'sub_category_name', 'status', 'action'])
                ->make(true);
        }

        $categories = Category::where('status', 'Active')->get();
        $sub_categories = SubCategory::where('status', 'Active')->get();

        return view('backend.child_category.index', compact('categories', 'sub_categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'name' => ['required', 'string', 'max:255',
                Rule::unique('child_categories')->where(function ($query) use ($request) {
                    return $query->where('category_id', $request->category_id)->where('sub_category_id', $request->sub_category_id);
                }),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray(),
            ]);
        } else {
            ChildCategory::create([
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
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
        $child_category = ChildCategory::where('id', $id)->first();
        return response()->json($child_category);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'name' => ['required', 'string', 'max:255',
                Rule::unique('child_categories')->where(function ($query) use ($request, $id) {
                    return $query->where('category_id', $request->category_id)->where('sub_category_id', $request->sub_category_id)->where('id', '!=', $id);
                }),
            ],
        ]);

        // If validation fails, return a JSON response with errors
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray(),
            ]);
        } else {
            // Update the existing child category
            ChildCategory::where('id', $id)->update([
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'name' => $request->name,
                'updated_by' => auth()->user()->id,
            ]);

            // Return a success response
            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function destroy(string $id)
    {
        $taskPostChargeExist = TaskPostCharge::where('child_category_id', $id)->exists();
        if ($taskPostChargeExist) {
            return response()->json([
                'status' => 400,
                'error' => 'This child category has task post charge. Please delete task post charge first.',
            ]);
        }
        $child_category = ChildCategory::findOrFail($id);
        $child_category->delete();
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = ChildCategory::onlyTrashed();

            $trash_child_categories = $query->orderBy('deleted_at', 'desc')->get();

            return DataTables::of($trash_child_categories)
                ->addIndexColumn()
                ->addColumn('category_name', function ($row) {
                    return $row->category->name ?? 'N/A';
                })
                ->editColumn('sub_category_name', function ($row) {
                    return $row->subCategory->name ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $restorePermission = auth()->user()->can('child_category.restore');
                    $deletePermission = auth()->user()->can('child_category.delete');

                    $restoreBtn = $restorePermission
                        ? '<button type="button" data-id="'.$row->id.'" class="btn bg-success btn-xs restoreBtn">Restore</button>'
                        : '';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="'.$row->id.'" class="btn bg-danger btn-xs forceDeleteBtn">Delete</button>'
                        : '';

                    $btn = $restoreBtn . ' ' . $deleteBtn;
                    return $btn;
                })
                ->rawColumns(['category_name', 'sub_category_name', 'action'])
                ->make(true);
        }

        return view('backend.child_category.index');
    }

    public function restore(string $id)
    {
        ChildCategory::onlyTrashed()->where('id', $id)->update([
            'deleted_by' => NULL
        ]);

        ChildCategory::onlyTrashed()->where('id', $id)->restore();
    }

    public function delete(string $id)
    {
        $child_category = ChildCategory::onlyTrashed()->where('id', $id)->first();
        $child_category->forceDelete();
    }

    public function status(string $id)
    {
        $child_category = ChildCategory::findOrFail($id);

        if ($child_category->status == "Active") {
            $child_category->status = "Inactive";
        } else {
            $child_category->status = "Active";
        }

        $child_category->updated_by = auth()->user()->id;
        $child_category->save();
    }

    public function getSubCategories(Request $request)
    {
        $subCategories = SubCategory::where('category_id', $request->category_id)->get();

        $html = '<option value="">-- Select Sub Category --</option>';
        foreach ($subCategories as $subCategory) {
            $html .= '<option value="'.$subCategory->id.'">'.$subCategory->name.'</option>';
        }

        return response()->json(['html' => $html]);
    }
}
