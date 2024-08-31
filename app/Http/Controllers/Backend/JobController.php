<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\JobPost;
use App\Models\User;
use App\Models\JobProof;
use App\Notifications\JobPostNotification;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    public function jobListPending(Request $request)
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
        return view('backend.job_list.pending');
    }

    public function pendingJobView(string $id)
    {
        $jobPost = JobPost::where('id', $id)->first();
        return view('backend.job_list.pending_show', compact('jobPost'));
    }

    public function jobListRejected(Request $request)
    {
        if ($request->ajax()) {
            $query = JobPost::where('status', 'Rejected');

            $query->select('job_posts.*')->orderBy('created_at', 'desc');

            $jobList = $query->get();

            return DataTables::of($jobList)
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
        return view('backend.job_list.rejected');
    }

    public function rejectedJobView(string $id)
    {
        $jobPost = JobPost::where('id', $id)->first();
        return view('backend.job_list.rejected_show', compact('jobPost'));
    }

    public function jobListRunning(Request $request)
    {
        if ($request->ajax()) {
            $query = JobPost::where('status', 'Running');

            $query->select('job_posts.*')->orderBy('created_at', 'desc');

            $jobList = $query->get();

            return DataTables::of($jobList)
                ->addIndexColumn()
                ->editColumn('user', function ($row) {
                    return $row->user->name;
                })
                ->editColumn('need_worker', function ($row) {
                    $proofCount = JobProof::where('job_post_id', $row->id)->where('status', '!=', 'Rejected')->count();
                    return  '<span class="badge bg-success">' . $proofCount . ' / ' .$row->need_worker . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('F j, Y  H:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                    ';
                return $btn;
                })
                ->rawColumns(['user', 'need_worker', 'created_at', 'action'])
                ->make(true);
        }
        return view('backend.job_list.running');
    }

    public function runningJobView(string $id)
    {
        $jobPost = JobPost::where('id', $id)->first();
        return view('backend.job_list.running_show', compact('jobPost'));
    }

    public function jobListCanceled(Request $request)
    {
        if ($request->ajax()) {
            $query = JobPost::where('status', 'Canceled');

            $query->select('job_posts.*')->orderBy('created_at', 'desc');

            $jobList = $query->get();

            return DataTables::of($jobList)
                ->addIndexColumn()
                ->editColumn('user', function ($row) {
                    return $row->user->name;
                })
                ->editColumn('need_worker', function ($row) {
                    $proofCount = JobProof::where('job_post_id', $row->id)->where('status', '!=', 'Rejected')->count();
                    return  '<span class="badge bg-success">' . $proofCount . ' / ' .$row->need_worker . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('F j, Y  H:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                    ';
                return $btn;
                })
                ->rawColumns(['user', 'need_worker', 'created_at', 'action'])
                ->make(true);
        }
        return view('backend.job_list.canceled');
    }

    public function canceledJobView(string $id)
    {
        $jobPost = JobPost::where('id', $id)->first();
        return view('backend.job_list.canceled_show', compact('jobPost'));
    }

    public function jobListCompleted(Request $request)
    {
        if ($request->ajax()) {
            $query = JobPost::where('status', 'Completed');

            $query->select('job_posts.*')->orderBy('created_at', 'desc');

            $jobList = $query->get();

            return DataTables::of($jobList)
                ->addIndexColumn()
                ->editColumn('user', function ($row) {
                    return $row->user->name;
                })
                ->editColumn('need_worker', function ($row) {
                    $proofCount = JobProof::where('job_post_id', $row->id)->where('status', '!=', 'Rejected')->count();
                    return  '<span class="badge bg-success">' . $proofCount . ' / ' .$row->need_worker . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('F j, Y  H:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                    ';
                return $btn;
                })
                ->rawColumns(['user', 'need_worker', 'created_at', 'action'])
                ->make(true);
        }
        return view('backend.job_list.completed');
    }

    public function completedJobView(string $id)
    {
        $jobPost = JobPost::where('id', $id)->first();
        return view('backend.job_list.completed_show', compact('jobPost'));
    }

    public function jobStatusUpdate(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'required_proof' => 'required|string',
            'additional_note' => 'required|string',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        }

        $jobPost = JobPost::findOrFail($id);

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

        $user = User::findOrFail($jobPost->user_id);
        if ($request->status == 'Rejected' && $jobPost->rejected_at == NULL) {
            $user->update([
                'deposit_balance' => $user->deposit_balance + $jobPost->total_charge,
            ]);
        }

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



        $jobPost = JobPost::findOrFail($id);

        $user->notify(new JobPostNotification($jobPost));

        return response()->json([
            'status' => 200,
        ]);
    }
}
