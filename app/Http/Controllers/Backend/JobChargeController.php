<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ChildCategory;
use App\Models\JobCharge;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class JobChargeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = JobCharge::select('job_charges.*');

            $query->orderBy('created_at', 'desc');

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $job_charges = $query->get();

            return DataTables::of($job_charges)
                ->addIndexColumn()
                ->addColumn('category_name', function ($row) {
                    return $row->category->name ?? 'N/A';
                })
                ->editColumn('sub_category_name', function ($row) {
                    return $row->subCategory->name ?? 'N/A';
                })
                ->editColumn('child_category_name', function ($row) {
                    return $row->childCategory->name ?? 'N/A';
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
                ->rawColumns(['category_name', 'sub_category_name', 'child_category_name', 'status', 'action'])
                ->make(true);
        }

        $categories = Category::where('status', 'Active')->get();
        $sub_categories = SubCategory::where('status', 'Active')->get();
        $child_categories = ChildCategory::where('status', 'Active')->get();

        return view('backend.job_charge.index', compact('categories', 'sub_categories', 'child_categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'working_min_charges' => 'required|numeric',
            'working_max_charges' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            JobCharge::create([
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'child_category_id' => $request->child_category_id,
                'working_min_charges' => $request->working_min_charges,
                'working_max_charges' => $request->working_max_charges,
                'created_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function edit(string $id)
    {
        $job_charge = JobCharge::where('id', $id)->first();
        return response()->json($job_charge);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'working_min_charges' => 'required|numeric',
            'working_max_charges' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            JobCharge::where('id', $id)->update([
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'child_category_id' => $request->child_category_id,
                'working_min_charges' => $request->working_min_charges,
                'working_max_charges' => $request->working_max_charges,
                'updated_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function destroy(string $id)
    {
        $job_charge = JobCharge::findOrFail($id);
        $job_charge->delete();
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = JobCharge::onlyTrashed();

            $trash_job_charges = $query->orderBy('deleted_at', 'desc')->get();

            return DataTables::of($trash_job_charges)
                ->addIndexColumn()
                ->addColumn('category_name', function ($row) {
                    return $row->category->name ?? 'N/A';
                })
                ->editColumn('sub_category_name', function ($row) {
                    return $row->subCategory->name ?? 'N/A';
                })
                ->editColumn('child_category_name', function ($row) {
                    return $row->childCategory->name ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                        <button type="button" data-id="'.$row->id.'" class="btn bg-success btn-xs restoreBtn">Restore</button>
                        <button type="button" data-id="'.$row->id.'" class="btn bg-danger btn-xs forceDeleteBtn">Delete</button>
                    ';
                    return $btn;
                })
                ->rawColumns(['category_name', 'sub_category_name', 'child_category_name', 'action'])
                ->make(true);
        }

        return view('backend.job_charge.index');
    }

    public function restore(string $id)
    {
        JobCharge::onlyTrashed()->where('id', $id)->update([
            'deleted_by' => NULL
        ]);

        JobCharge::onlyTrashed()->where('id', $id)->restore();
    }

    public function delete(string $id)
    {
        $job_charge = JobCharge::onlyTrashed()->where('id', $id)->first();
        $job_charge->forceDelete();
    }

    public function status(string $id)
    {
        $job_charge = JobCharge::findOrFail($id);

        if ($job_charge->status == "Active") {
            $job_charge->status = "Inactive";
        } else {
            $job_charge->status = "Active";
        }

        $job_charge->updated_by = auth()->user()->id;
        $job_charge->save();
    }
}
