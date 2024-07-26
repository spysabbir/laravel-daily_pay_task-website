<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\JobPostCharge;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class JobPostChargeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = JobPostCharge::select('job_post_charges.*');

            $query->orderBy('created_at', 'desc');

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $job_post_charges = $query->get();

            return DataTables::of($job_post_charges)
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

        return view('backend.job_post_charge.index', compact('categories', 'sub_categories', 'child_categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'working_min_charge' => 'required|numeric',
            'working_max_charge' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            JobPostCharge::create([
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'child_category_id' => $request->child_category_id,
                'working_min_charge' => $request->working_min_charge,
                'working_max_charge' => $request->working_max_charge,
                'created_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function edit(string $id)
    {
        $job_post_charge = JobPostCharge::where('id', $id)->first();
        return response()->json($job_post_charge);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'working_min_charge' => 'required|numeric',
            'working_max_charge' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            JobPostCharge::where('id', $id)->update([
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'child_category_id' => $request->child_category_id,
                'working_min_charge' => $request->working_min_charge,
                'working_max_charge' => $request->working_max_charge,
                'updated_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function destroy(string $id)
    {
        $job_post_charge = JobPostCharge::findOrFail($id);
        $job_post_charge->delete();
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = JobPostCharge::onlyTrashed();

            $trash_job_post_charges = $query->orderBy('deleted_at', 'desc')->get();

            return DataTables::of($trash_job_post_charges)
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

        return view('backend.job_post_charge.index');
    }

    public function restore(string $id)
    {
        JobPostCharge::onlyTrashed()->where('id', $id)->update([
            'deleted_by' => NULL
        ]);

        JobPostCharge::onlyTrashed()->where('id', $id)->restore();
    }

    public function delete(string $id)
    {
        $job_post_charge = JobPostCharge::onlyTrashed()->where('id', $id)->first();
        $job_post_charge->forceDelete();
    }

    public function status(string $id)
    {
        $job_post_charge = JobPostCharge::findOrFail($id);

        if ($job_post_charge->status == "Active") {
            $job_post_charge->status = "Inactive";
        } else {
            $job_post_charge->status = "Active";
        }

        $job_post_charge->updated_by = auth()->user()->id;
        $job_post_charge->save();
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
