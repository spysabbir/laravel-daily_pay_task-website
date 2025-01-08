<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use App\Models\PostTask;
use App\Models\TaskPostCharge;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TaskPostChargeController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('task_post_charge.index') , only:['index']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('task_post_charge.create') , only:['store', 'getSubCategories', 'getChildCategories']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('task_post_charge.edit') , only:['edit', 'update']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('task_post_charge.destroy'), only:['destroy']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('task_post_charge.trash') , only:['trash']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('task_post_charge.restore') , only:['restore']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('task_post_charge.delete') , only:['delete']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('task_post_charge.status') , only:['status']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = TaskPostCharge::select('task_post_charges.*');

            $query->orderBy('created_at', 'desc');

            if ($request->category_id) {
                $query->where('category_id', $request->category_id);
            }
            if ($request->sub_category_id) {
                $query->where('sub_category_id', $request->sub_category_id);
            }
            if ($request->child_category_id) {
                $query->where('child_category_id', $request->child_category_id);
            }
            if ($request->status) {
                $query->where('status', $request->status);
            }

            $task_post_charges = $query->get();

            return DataTables::of($task_post_charges)
                ->addIndexColumn()
                ->addColumn('category_name', function ($row) {
                    return $row->category->name ?? 'N/A';
                })
                ->editColumn('sub_category_name', function ($row) {
                    return $row->subCategory->name ?? 'N/A';
                })
                ->editColumn('child_category_name', function ($row) {
                    return $row->childCategory->name ?? '<span class="text-danger">-</span>';
                })
                ->editColumn('status', function ($row) {
                    $canChangeStatus = auth()->user()->can('task_post_charge.status');

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
                    $editPermission = auth()->user()->can('task_post_charge.edit');
                    $deletePermission = auth()->user()->can('task_post_charge.destroy');

                    $editBtn = $editPermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs editBtn" data-bs-toggle="modal" data-bs-target=".editModal">Edit</button>'
                        : '';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>'
                        : '';

                    $btn = $editBtn . ' ' . $deleteBtn;
                    return $btn;
                })
                ->rawColumns(['category_name', 'sub_category_name', 'child_category_name', 'status', 'action'])
                ->make(true);
        }

        $categories = Category::where('status', 'Active')->get();
        $sub_categories = SubCategory::where('status', 'Active')->get();
        $child_categories = ChildCategory::where('status', 'Active')->get();

        return view('backend.task_post_charge.index', compact('categories', 'sub_categories', 'child_categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'min_charge' => 'required|numeric',
            'max_charge' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            // Check if the task post charge already exists
            $taskPostChargeExist = TaskPostCharge::where('category_id', $request->category_id)->where('sub_category_id', $request->sub_category_id)->where('child_category_id', $request->child_category_id)->exists();

            if ($taskPostChargeExist) {
                return response()->json([
                    'status' => 401,
                    'error' => 'This task post charge already exists.'
                ]);
            }

            TaskPostCharge::create([
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'child_category_id' => $request->child_category_id,
                'min_charge' => $request->min_charge,
                'max_charge' => $request->max_charge,
                'created_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function edit(string $id)
    {
        $task_post_charge = TaskPostCharge::where('id', $id)->first();
        return response()->json($task_post_charge);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'min_charge' => 'required|numeric',
            'max_charge' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            // Check if the task post charge already exists
            $taskPostChargeExist = TaskPostCharge::where('category_id', $request->category_id)->where('sub_category_id', $request->sub_category_id)->where('child_category_id', $request->child_category_id)->where('id', '!=', $id)->exists();

            if ($taskPostChargeExist) {
                return response()->json([
                    'status' => 401,
                    'error' => 'This task post charge already exists.'
                ]);
            }

            TaskPostCharge::where('id', $id)->update([
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'child_category_id' => $request->child_category_id,
                'min_charge' => $request->min_charge,
                'max_charge' => $request->max_charge,
                'updated_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function destroy(string $id)
    {
        $task_post_charge = TaskPostCharge::findOrFail($id);

        $postTaskExist = PostTask::where('category_id', $task_post_charge->category_id)->where('sub_category_id', $task_post_charge->sub_category_id)->where('child_category_id', $task_post_charge->child_category_id)->exists();
        if ($postTaskExist) {
            return response()->json([
                'status' => 400,
                'error' => 'This task post charge already used in post task. So, you can not delete this.'
            ]);
        }

        $task_post_charge->delete();
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = TaskPostCharge::onlyTrashed();

            $trash_task_post_charges = $query->orderBy('deleted_at', 'desc')->get();

            return DataTables::of($trash_task_post_charges)
                ->addIndexColumn()
                ->addColumn('category_name', function ($row) {
                    return $row->category->name ?? 'N/A';
                })
                ->editColumn('sub_category_name', function ($row) {
                    return $row->subCategory->name ?? 'N/A';
                })
                ->editColumn('child_category_name', function ($row) {
                    return $row->childCategory->name ?? '<span class="text-danger">-</span>';
                })
                ->addColumn('action', function ($row) {
                    $restorePermission = auth()->user()->can('task_post_charge.restore');
                    $deletePermission = auth()->user()->can('task_post_charge.delete');

                    $restoreBtn = $restorePermission
                        ? '<button type="button" data-id="'.$row->id.'" class="btn bg-success btn-xs restoreBtn">Restore</button>'
                        : '';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="'.$row->id.'" class="btn bg-danger btn-xs forceDeleteBtn">Delete</button>'
                        : '';

                    $btn = $restoreBtn . ' ' . $deleteBtn;
                    return $btn;
                })
                ->rawColumns(['category_name', 'sub_category_name', 'child_category_name', 'action'])
                ->make(true);
        }

        return view('backend.task_post_charge.index');
    }

    public function restore(string $id)
    {
        TaskPostCharge::onlyTrashed()->where('id', $id)->update([
            'deleted_by' => NULL
        ]);

        TaskPostCharge::onlyTrashed()->where('id', $id)->restore();
    }

    public function delete(string $id)
    {
        $task_post_charge = TaskPostCharge::onlyTrashed()->where('id', $id)->first();
        $task_post_charge->forceDelete();
    }

    public function status(string $id)
    {
        $task_post_charge = TaskPostCharge::findOrFail($id);

        if ($task_post_charge->status == "Active") {
            $task_post_charge->status = "Inactive";
        } else {
            $task_post_charge->status = "Active";
        }

        $task_post_charge->updated_by = auth()->user()->id;
        $task_post_charge->save();
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

    public function getChildCategories(Request $request)
    {
        $childCategories = ChildCategory::where('category_id', $request->category_id)->where('sub_category_id', $request->sub_category_id)->get();

        $html = '<option value="">-- Select Child Category --</option>';
        foreach ($childCategories as $childCategory) {
            $html .= '<option value="'.$childCategory->id.'">'.$childCategory->name.'</option>';
        }

        return response()->json(['html' => $html]);
    }
}
