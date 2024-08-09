<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\JobPost;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class JobPostController extends Controller
{
    public function jobPostRequest(Request $request)
    {
        if ($request->ajax()) {
            $query = JobPost::where('status', 'Pending');

            $query->select('job_posts.*')->orderBy('created_at', 'desc');

            $pendingRequest = $query->get();

            return DataTables::of($pendingRequest)
                ->addIndexColumn()
                ->editColumn('user', function ($row) {
                    return $row->user->name;
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('F j, Y  H:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">Check</button>
                    ';
                return $btn;
                })
                ->rawColumns(['user', 'created_at', 'action'])
                ->make(true);
        }
        return view('backend.job_post.index');
    }

    public function jobPostRequestShow(string $id)
    {
        $jobPost = JobPost::where('id', $id)->first();
        return view('backend.job_post.show', compact('jobPost'));
    }

    public function jobPostRequestUpdate(Request $request, string $id)
    {
        // First, validate the request data
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'required_proof' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        }

        $jobPost = JobPost::findOrFail($id);

        // Check if the status is 'Rejected' and validate accordingly
        if ($request->status == 'Rejected') {
            $validator = Validator::make($request->all(), [
                'rejection_reason' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'error' => $validator->errors()->toArray()
                ]);
            }
        }

        // Update the job post
        $jobPost->update([
            'title' => $request->title,
            'description' => $request->description,
            'required_proof' => $request->required_proof,
            'status' => $request->status,
            'rejection_reason' => $request->status == 'Rejected' ? $request->rejection_reason : NULL,
            'rejected_by' => $request->status == 'Rejected' ? auth()->user()->id : NULL,
            'rejected_at' => $request->status == 'Rejected' ? now() : NULL,
            'approved_by' => $request->status == 'Running' ? auth()->user()->id : NULL,
            'approved_at' => $request->status == 'Running' ? now() : NULL,
        ]);

        return response()->json([
            'status' => 200,
        ]);
    }

}
