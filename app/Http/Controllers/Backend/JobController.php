<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\JobPost;
use App\Models\User;
use App\Models\JobProof;
use App\Notifications\JobPostCheckNotification;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\UserDetail;

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
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
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
        if ($request->status == 'Rejected') {
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

        $user->notify(new JobPostCheckNotification($jobPost));

        return response()->json([
            'status' => 200,
        ]);
    }

    public function jobProofPending(Request $request){
        if ($request->ajax()) {
            $query = JobProof::where('status', 'Pending');

            $query->select('job_posts.*')->orderBy('created_at', 'desc');

            $jobListRunning = $query->get();

            return DataTables::of($jobListRunning)
                ->addIndexColumn()
                ->editColumn('proof_submitted', function ($row) {
                    $proofSubmitted = JobProof::where('job_post_id', $row->id)->count();
                    return  '<span class="badge bg-dark">' . $proofSubmitted . ' / ' . $row->need_worker . '</span>';
                })
                ->editColumn('proof_status', function ($row) {
                    $statuses = [
                        'Pending' => 'bg-warning',
                        'Approved' => 'bg-success',
                        'Rejected' => 'bg-danger',
                        'Reviewed' => 'bg-info'
                    ];
                    $proofStatus = '';
                    foreach ($statuses as $status => $class) {
                        $count = JobProof::where('job_post_id', $row->id)->where('status', $status)->count();
                        if ($count > 0) {
                            $proofStatus .= "<span class=\"badge $class\"> $status: $count</span> ";
                        }
                    }
                    return $proofStatus;
                })
                ->editColumn('approved_at', function ($row) {
                    return  date('d M Y h:i A', strtotime($row->approved_at));
                })
                ->editColumn('action', function ($row) {
                    $btn = '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs editBtn" data-bs-toggle="modal" data-bs-target=".editModal">Edit</button>
                        <a href="' . route('running_job.show', encrypt($row->id)) . '" class="btn btn-info btn-xs">Check</a>
                        <button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs pausedBtn">Paused</button>
                        <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs canceledBtn">Canceled</button>
                    ';
                    return $btn;
                })
                ->rawColumns(['proof_submitted', 'proof_status', 'approved_at', 'action'])
                ->make(true);
        }
        return view('backend.job_proof.pending');
    }

    public function jobProofReviewed(Request $request){
        if ($request->ajax()) {
            $jobIds = JobProof::where('status', 'Reviewed')->pluck('job_post_id')->toArray();
            $query = JobPost::whereIn('id', $jobIds);
            $query->select('job_posts.*')->orderBy('created_at', 'desc');

            $jobList = $query->get();

            return DataTables::of($jobList)
                ->addIndexColumn()
                ->editColumn('action', function ($row) {
                    $btn = '
                        <a href="' . route('backend.job_proof.reviewed.list', encrypt($row->id)) . '" class="btn btn-info btn-xs">Check</a>
                    ';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.job_proof.reviewed');
    }

    public function jobProofReviewedList(Request $request, string $id){

        if ($request->ajax()) {
            $query = JobProof::where('job_post_id', decrypt($id))->where('status', 'Reviewed');

            $query->select('job_proofs.*');

            $JobProofs = $query->get();

            return DataTables::of($JobProofs)
                ->editColumn('user', function ($row) {
                    $userDetail = UserDetail::where('user_id', $row->user_id)->first();
                    $user = '
                        <span class="badge bg-dark">Name: ' . $row->user->name . '</span>
                        <span class="badge bg-dark">Ip: ' . $userDetail->ip . '</span>
                    ';
                    return $user;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y h:i A');
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">Check</button>';
                    return $actionBtn;
                })
                ->rawColumns(['user', 'created_at', 'action'])
                ->make(true);
        }

        $jobPost = JobPost::findOrFail(decrypt($id));

        return view('backend.job_proof.reviewed_list', compact('jobPost'));
    }

    public function jobProofReviewedCheck($id)
    {
        $jobProof = JobProof::findOrFail($id);
        return view('backend.job_proof.reviewed_check', compact('jobProof'));
    }

    public function jobProofReviewedCheckUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            if ($request->status == 'Rejected') {
                $validator = Validator::make($request->all(), [
                    'rejected_reason' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 400,
                        'error' => $validator->errors()->toArray()
                    ]);
                }
            }

            $jobProof = JobProof::findOrFail($id);

            $jobPost = JobPost::findOrFail($jobProof->job_post_id);

            $user = User::findOrFail($jobProof->user_id);
            $user->withdraw_balance = $user->withdraw_balance + $jobPost->worker_charge + $request->bonus;
            $user->save();

            $jobProof->status = $request->status;
            $jobProof->rejected_reason = $request->rejected_reason;
            $jobProof->rejected_at = $request->status == 'Rejected' ? now() : NULL;
            $jobProof->rejected_by = $request->status == 'Rejected' ? auth()->user()->id : NULL;
            $jobProof->approved_at = $request->status == 'Approved' ? now() : NULL;
            $jobProof->approved_by = $request->status == 'Approved' ? auth()->user()->id : NULL;
            $jobProof->save();

            return response()->json([
                'status' => 200,
            ]);
        }
    }
}
