<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ExpenseCategory;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ExpenseController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('expense.index') , only:['index', 'show']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('expense.create') , only:['store']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('expense.edit') , only:['edit', 'update']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('expense.destroy'), only:['destroy']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('expense.trash') , only:['trash']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('expense.restore') , only:['restore']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('expense.delete') , only:['delete']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('expense.status') , only:['status']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Expense::select('expenses.*');

            $query->orderBy('created_at', 'desc');

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->expense_category_id) {
                $query->where('expense_category_id', $request->expense_category_id);
            }

            if ($request->expense_date) {
                $query->where('expense_date', $request->expense_date);
            }

            $expenses = $query->get();

            return DataTables::of($expenses)
                ->addIndexColumn()
                ->editColumn('expense_category_name', function ($row) {
                    return $row->expenseCategory->name;
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="badge bg-dark">' . get_site_settings('site_currency_symbol') . ' ' . $row->amount . '</span>';
                })
                ->editColumn('expense_date', function ($row) {
                    return date('d M, Y', strtotime($row->expense_date));
                })
                ->editColumn('status', function ($row) {
                    $canChangeStatus = auth()->user()->can('expense.status');

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
                    $editPermission = auth()->user()->can('expense.edit');
                    $deletePermission = auth()->user()->can('expense.destroy');

                    $editBtn = $editPermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs editBtn">Edit</button>'
                        : '';
                    $viewBtn = '<button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs viewBtn">View</button>';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>'
                        : '';

                    $btn = $editBtn . ' ' . $viewBtn . ' ' . $deleteBtn;
                    return $btn;
                })
                ->rawColumns(['expense_category_name', 'amount', 'expense_date', 'status', 'action'])
                ->make(true);
        }

        $expense_categories = ExpenseCategory::where('status', 'Active')->get();
        return view('backend.expense.index' , compact('expense_categories'));
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'expense_category_id' => 'required|exists:expense_categories,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'expense_date' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray(),
            ]);
        }

        try {
            $input['expense_date'] = Carbon::createFromFormat('j F, Y', $input['expense_date'])->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'error' => ['expense_date' => ['Invalid date format. Please provide a valid date.']],
            ]);
        }

        Expense::create([
            'expense_category_id' => $input['expense_category_id'],
            'title' => $input['title'],
            'description' => $input['description'] ?? null,
            'amount' => $input['amount'],
            'expense_date' => $input['expense_date'],
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Expense created successfully!',
        ]);
    }


    public function show(string $id)
    {
        $expense = Expense::withTrashed()->where('id', $id)->first();
        return view('backend.expense.show', compact('expense'));
    }

    public function edit(string $id)
    {
        $expense = Expense::where('id', $id)->first();
        return response()->json($expense);
    }

    public function update(Request $request, string $id)
    {
        $input = $request->all();

        if (!empty($input['expense_date'])) {
            try {
                $formattedDate = Carbon::createFromFormat('j F, Y', $input['expense_date'])->format('Y-m-d');
                $input['expense_date'] = $formattedDate;
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 400,
                    'error' => ['expense_date' => ['Invalid date format. Please provide a valid date.']]
                ]);
            }
        }

        $validator = Validator::make($input, [
            'expense_category_id' => 'required|exists:expense_categories,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'expense_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            Expense::where('id', $id)->update([
                'expense_category_id' => $request->expense_category_id,
                'title' => $request->title,
                'description' => $request->description,
                'amount' => $request->amount,
                'expense_date' => $formattedDate,
                'updated_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function destroy(string $id)
    {
        $expense = Expense::findOrFail($id);
        $expense->deleted_by = auth()->user()->id;
        $expense->save();

        $expense->delete();
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = Expense::onlyTrashed();

            $trashExpenses = $query->orderBy('deleted_at', 'desc')->get();

            return DataTables::of($trashExpenses)
                ->addIndexColumn()
                ->editColumn('expense_category_name', function ($row) {
                    return $row->expenseCategory->name;
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="badge bg-dark">' . get_site_settings('site_currency_symbol') . ' ' . $row->amount . '</span>';
                })
                ->editColumn('expense_date', function ($row) {
                    return date('d M, Y', strtotime($row->expense_date));
                })
                ->addColumn('action', function ($row) {
                    $restorePermission = auth()->user()->can('expense.restore');
                    $deletePermission = auth()->user()->can('expense.delete');

                    $viewBtn = '<button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs viewBtn">View</button>';
                    $restoreBtn = $restorePermission
                        ? '<button type="button" data-id="'.$row->id.'" class="btn bg-success btn-xs restoreBtn">Restore</button>'
                        : '';
                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="'.$row->id.'" class="btn bg-danger btn-xs forceDeleteBtn">Delete</button>'
                        : '';

                    $btn = $viewBtn . ' ' . $restoreBtn . ' ' . $deleteBtn;
                    return $btn;
                })
                ->rawColumns(['expense_category_name', 'amount', 'expense_date', 'action'])
                ->make(true);
        }

        return view('backend.expense.index');
    }

    public function restore(string $id)
    {
        Expense::onlyTrashed()->where('id', $id)->update([
            'deleted_by' => NULL
        ]);

        Expense::onlyTrashed()->where('id', $id)->restore();
    }

    public function delete(string $id)
    {
        $expense = Expense::onlyTrashed()->where('id', $id)->first();
        $expense->forceDelete();
    }

    public function status(string $id)
    {
        $expense = Expense::findOrFail($id);

        if ($expense->status == "Active") {
            $expense->status = "Inactive";
        } else {
            $expense->status = "Active";
        }

        $expense->updated_by = auth()->user()->id;
        $expense->save();
    }
}
