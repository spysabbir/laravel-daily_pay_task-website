<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ExpenseCategoryController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('expense_category.index') , only:['index']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('expense_category.create') , only:['store']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('expense_category.edit') , only:['edit', 'update']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('expense_category.destroy'), only:['destroy']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('expense_category.trash') , only:['trash']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('expense_category.restore') , only:['restore']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('expense_category.delete') , only:['delete']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('expense_category.status') , only:['status']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ExpenseCategory::select('expense_categories.*');

            $query->orderBy('created_at', 'desc');

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $expense_categories = $query->get();

            return DataTables::of($expense_categories)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $canChangeStatus = auth()->user()->can('expense_category.status');

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
                    $editPermission = auth()->user()->can('expense_category.edit');
                    $deletePermission = auth()->user()->can('expense_category.destroy');

                    $editBtn = $editPermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs editBtn">Edit</button>'
                        : '';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>'
                        : '';

                    $btn = $editBtn . ' ' . $deleteBtn;
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('backend.expense_category.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:expense_categories,name',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            ExpenseCategory::create([
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
        $expense_category = ExpenseCategory::where('id', $id)->first();
        return response()->json($expense_category);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:expense_categories,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            ExpenseCategory::where('id', $id)->update([
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
        $expenseExist = Expense::where('expense_category_id', $id)->exists();
        if ($expenseExist) {
            return response()->json([
                'status' => 400,
                'error' => 'This category is already used in expense. So, it can not be deleted.'
            ]);
        }
        $expense_category = ExpenseCategory::findOrFail($id);
        $expense_category->delete();
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = ExpenseCategory::onlyTrashed();

            $trashExpenseCategories = $query->orderBy('deleted_at', 'desc')->get();

            return DataTables::of($trashExpenseCategories)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $restorePermission = auth()->user()->can('expense_category.restore');
                    $deletePermission = auth()->user()->can('expense_category.delete');

                    $restoreBtn = $restorePermission
                        ? '<button type="button" data-id="'.$row->id.'" class="btn bg-success btn-xs restoreBtn">Restore</button>'
                        : '';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="'.$row->id.'" class="btn bg-danger btn-xs forceDeleteBtn">Delete</button>'
                        : '';

                    $btn = $restoreBtn . ' ' . $deleteBtn;
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.expense_category.index');
    }

    public function restore(string $id)
    {
        ExpenseCategory::onlyTrashed()->where('id', $id)->update([
            'deleted_by' => NULL
        ]);

        ExpenseCategory::onlyTrashed()->where('id', $id)->restore();
    }

    public function delete(string $id)
    {
        $expense_category = ExpenseCategory::onlyTrashed()->where('id', $id)->first();
        $expense_category->forceDelete();
    }

    public function status(string $id)
    {
        $expense_category = ExpenseCategory::findOrFail($id);

        if ($expense_category->status == "Active") {
            $expense_category->status = "Inactive";
        } else {
            $expense_category->status = "Active";
        }

        $expense_category->updated_by = auth()->user()->id;
        $expense_category->save();
    }
}
