<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\PostTask;
use App\Models\User;
use App\Models\ProofTask;
use App\Notifications\PostTaskCheckNotification;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\UserDetail;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Notifications\ReviewedTaskProofCheckNotification;

class TaskController extends Controller
{
    public function postedTaskListPending(Request $request)
    {
        if ($request->ajax()) {
            $query = PostTask::where('status', 'Pending');

            $query->select('post_tasks.*')->orderBy('created_at', 'desc');

            $pendingRequest = $query->get();

            return DataTables::of($pendingRequest)
                ->addIndexColumn()
                ->editColumn('user', function ($row) {
                    return $row->user->name;
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
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
        return view('backend.posted_task.pending');
    }

    public function pendingPostedTaskView(string $id)
    {
        $postTask = PostTask::where('id', $id)->first();
        return view('backend.posted_task.pending_show', compact('postTask'));
    }

    public function postedTaskListRejected(Request $request)
    {
        if ($request->ajax()) {
            $query = PostTask::where('status', 'Rejected');

            $query->select('post_tasks.*')->orderBy('created_at', 'desc');

            $taskList = $query->get();

            return DataTables::of($taskList)
                ->addIndexColumn()
                ->editColumn('user', function ($row) {
                    return $row->user->name;
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->editColumn('rejected_by', function ($row) {
                    return '
                        <span class="badge bg-info text-dark">' . $row->rejectedBy->name . '</span>
                        ';
                })
                ->editColumn('rejected_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->rejected_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                    ';
                return $btn;
                })
                ->rawColumns(['user', 'created_at', 'rejected_by', 'rejected_at', 'action'])
                ->make(true);
        }
        return view('backend.posted_task.rejected');
    }

    public function rejectedPostedTaskView(string $id)
    {
        $postTask = PostTask::where('id', $id)->first();
        return view('backend.posted_task.rejected_show', compact('postTask'));
    }

    public function postedTaskListRunning(Request $request)
    {
        if ($request->ajax()) {
            $query = PostTask::where('status', 'Running');

            $query->select('post_tasks.*')->orderBy('created_at', 'desc');

            $taskList = $query->get();

            return DataTables::of($taskList)
                ->addIndexColumn()
                ->editColumn('user', function ($row) {
                    return $row->user->name;
                })
                ->editColumn('proof_submitted', function ($row) {
                    $proofSubmitted = ProofTask::where('post_task_id', $row->id)->count();
                    $proofStyleWidth = $proofSubmitted != 0 ? round(($proofSubmitted / $row->worker_needed) * 100, 2) : 100;
                    $progressBarClass = $proofSubmitted == 0 ? 'primary' : 'success';
                    return '
                    <div class="progress position-relative">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-' . $progressBarClass . '" role="progressbar" style="width: ' . $proofStyleWidth . '%" aria-valuenow="' . $proofSubmitted . '" aria-valuemin="0" aria-valuemax="' . $row->worker_needed . '"></div>
                        <span class="position-absolute w-100 text-center">' . $proofSubmitted . '/' . $row->worker_needed . '</span>
                    </div>
                    ';
                })
                ->editColumn('proof_status', function ($row) {
                    $statuses = [
                        'Pending' => 'bg-warning',
                        'Approved' => 'bg-success',
                        'Rejected' => 'bg-danger',
                        'Reviewed' => 'bg-info'
                    ];
                    $proofStatus = '';
                    $proofCount = ProofTask::where('post_task_id', $row->id)->count();
                    if ($proofCount === 0) {
                        return '<span class="badge bg-secondary">Proof not submitted yet.</span>';
                    }
                    foreach ($statuses as $status => $class) {
                        $count = ProofTask::where('post_task_id', $row->id)->where('status', $status)->count();
                        if ($count > 0) {
                            $proofStatus .= "<span class=\"badge $class\"> $status: $count</span> ";
                        }
                    }
                    return $proofStatus;
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->editColumn('approved_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y h:i:s A', strtotime($row->approved_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                    <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs canceledBtn">Canceled</button>
                    <button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs pausedBtn">Paused</button>
                    ';
                return $btn;
                })
                ->rawColumns(['user', 'proof_submitted', 'proof_status', 'created_at', 'approved_at', 'action'])
                ->make(true);
        }
        return view('backend.posted_task.running');
    }

    public function runningPostedTaskView(string $id)
    {
        $postTask = PostTask::where('id', $id)->first();
        return view('backend.posted_task.running_show', compact('postTask'));
    }

    public function postedTaskListCanceled(Request $request)
    {
        if ($request->ajax()) {
            $query = PostTask::where('status', 'Canceled');

            $query->select('post_tasks.*')->orderBy('created_at', 'desc');

            $taskList = $query->get();

            return DataTables::of($taskList)
                ->addIndexColumn()
                ->editColumn('user', function ($row) {
                    return $row->user->name;
                })
                ->editColumn('proof_submitted', function ($row) {
                    $proofSubmitted = ProofTask::where('post_task_id', $row->id)->count();
                    $proofStyleWidth = $proofSubmitted != 0 ? round(($proofSubmitted / $row->worker_needed) * 100, 2) : 100;
                    $progressBarClass = $proofSubmitted == 0 ? 'primary' : 'success';
                    return '
                    <div class="progress position-relative">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-' . $progressBarClass . '" role="progressbar" style="width: ' . $proofStyleWidth . '%" aria-valuenow="' . $proofSubmitted . '" aria-valuemin="0" aria-valuemax="' . $row->worker_needed . '"></div>
                        <span class="position-absolute w-100 text-center">' . $proofSubmitted . '/' . $row->worker_needed . '</span>
                    </div>
                    ';
                })
                ->editColumn('proof_status', function ($row) {
                    $statuses = [
                        'Pending' => 'bg-warning',
                        'Approved' => 'bg-success',
                        'Rejected' => 'bg-danger',
                        'Reviewed' => 'bg-info'
                    ];
                    $proofStatus = '';
                    $proofCount = ProofTask::where('post_task_id', $row->id)->count();
                    if ($proofCount === 0) {
                        return '<span class="badge bg-secondary">Proof not submitted yet.</span>';
                    }
                    foreach ($statuses as $status => $class) {
                        $count = ProofTask::where('post_task_id', $row->id)->where('status', $status)->count();
                        if ($count > 0) {
                            $proofStatus .= "<span class=\"badge $class\"> $status: $count</span> ";
                        }
                    }
                    return $proofStatus;
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->editColumn('canceled_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->canceled_at)) . '</span>
                        ';
                })
                ->editColumn('canceled_by', function ($row) {
                    return '
                        <span class="badge bg-info text-dark">' . $row->canceledBy->name . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                    ';
                return $btn;
                })
                ->rawColumns(['user', 'proof_submitted', 'proof_status', 'created_at', 'canceled_at', 'canceled_by', 'action'])
                ->make(true);
        }
        return view('backend.posted_task.canceled');
    }

    public function canceledPostedTaskView(string $id)
    {
        $postTask = PostTask::where('id', $id)->first();
        return view('backend.posted_task.canceled_show', compact('postTask'));
    }

    public function postedTaskListPaused(Request $request)
    {
        if ($request->ajax()) {
            $query = PostTask::where('status', 'Paused');

            $query->select('post_tasks.*')->orderBy('paused_at', 'desc');

            $taskList = $query->get();

            return DataTables::of($taskList)
                ->addIndexColumn()
                ->editColumn('user', function ($row) {
                    return $row->user->name;
                })
                ->editColumn('proof_submitted', function ($row) {
                    $proofSubmitted = ProofTask::where('post_task_id', $row->id)->count();
                    $proofStyleWidth = $proofSubmitted != 0 ? round(($proofSubmitted / $row->worker_needed) * 100, 2) : 100;
                    $progressBarClass = $proofSubmitted == 0 ? 'primary' : 'success';
                    return '
                    <div class="progress position-relative">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-' . $progressBarClass . '" role="progressbar" style="width: ' . $proofStyleWidth . '%" aria-valuenow="' . $proofSubmitted . '" aria-valuemin="0" aria-valuemax="' . $row->worker_needed . '"></div>
                        <span class="position-absolute w-100 text-center">' . $proofSubmitted . '/' . $row->worker_needed . '</span>
                    </div>
                    ';
                })
                ->editColumn('proof_status', function ($row) {
                    $statuses = [
                        'Pending' => 'bg-warning',
                        'Approved' => 'bg-success',
                        'Rejected' => 'bg-danger',
                        'Reviewed' => 'bg-info'
                    ];
                    $proofStatus = '';
                    $proofCount = ProofTask::where('post_task_id', $row->id)->count();
                    if ($proofCount === 0) {
                        return '<span class="badge bg-secondary">Proof not submitted yet.</span>';
                    }
                    foreach ($statuses as $status => $class) {
                        $count = ProofTask::where('post_task_id', $row->id)->where('status', $status)->count();
                        if ($count > 0) {
                            $proofStatus .= "<span class=\"badge $class\"> $status: $count</span> ";
                        }
                    }
                    return $proofStatus;
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->editColumn('paused_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->paused_at)) . '</span>
                        ';
                })
                ->editColumn('paused_by', function ($row) {
                    return '
                        <span class="badge bg-info text-dark">' . $row->pausedBy->name . '</span>
                        ';
                })
                ->editColumn('action', function ($row) {
                    $btn = '';

                    if ($row->pausedBy->user_type == 'Backend') {
                        $btn .= '<button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs resumeBtn">Resume</button>';
                    }

                    $btn .= '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                    ';

                    return $btn;
                })
                ->rawColumns(['user', 'proof_submitted', 'proof_status', 'created_at', 'paused_at', 'paused_by', 'action'])
                ->make(true);
        }
        return view('backend.posted_task.paused');
    }

    public function pausedPostedTaskView(string $id)
    {
        $postTask = PostTask::where('id', $id)->first();
        return view('backend.posted_task.paused_show', compact('postTask'));
    }

    public function runningPostedTaskPausedResume($id)
    {
        $postTask = PostTask::findOrFail($id);
        $postTask->status = 'Running';
        $postTask->save();

        $user = User::findOrFail($postTask->user_id);
        $user->notify(new PostTaskCheckNotification($postTask));

        return response()->json(['success' => 'Status updated successfully.']);
    }

    public function postedTaskListCompleted(Request $request)
    {
        if ($request->ajax()) {
            $query = PostTask::where('status', 'Completed');

            $query->select('post_tasks.*')->orderBy('created_at', 'desc');

            $taskList = $query->get();

            return DataTables::of($taskList)
                ->addIndexColumn()
                ->editColumn('user', function ($row) {
                    return $row->user->name;
                })
                ->editColumn('worker_needed', function ($row) {
                    $proofSubmitted = ProofTask::where('post_task_id', $row->id)->count();
                    $proofStyleWidth = $proofSubmitted != 0 ? round(($proofSubmitted / $row->worker_needed) * 100, 2) : 100;
                    $progressBarClass = $proofSubmitted == 0 ? 'primary' : 'success';
                    return '
                    <div class="progress position-relative">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-' . $progressBarClass . '" role="progressbar" style="width: ' . $proofStyleWidth . '%" aria-valuenow="' . $proofSubmitted . '" aria-valuemin="0" aria-valuemax="' . $row->worker_needed . '"></div>
                        <span class="position-absolute w-100 text-center">' . $proofSubmitted . '/' . $row->worker_needed . '</span>
                    </div>
                    ';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                    ';
                return $btn;
                })
                ->rawColumns(['user', 'worker_needed', 'created_at', 'action'])
                ->make(true);
        }
        return view('backend.posted_task.completed');
    }

    public function completedPostedTaskView(string $id)
    {
        $postTask = PostTask::where('id', $id)->first();
        return view('backend.posted_task.completed_show', compact('postTask'));
    }

    public function postedTaskStatusUpdate(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'required_proof_answer' => 'required|string',
            'additional_note' => 'required|string',
            'status' => 'required',
            'rejection_reason' => 'required_if:status,Rejected',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        }

        $postTask = PostTask::findOrFail($id);

        // Store the previous status
        $previousStatus = $postTask->status;

        $user = User::findOrFail($postTask->user_id);
        if ($request->status == 'Rejected') {
            $user->update([
                'deposit_balance' => $user->deposit_balance + $postTask->total_charge,
            ]);
        }

        $postTask->update([
            'title' => $request->title,
            'description' => $request->description,
            'required_proof_answer' => $request->required_proof_answer,
            'additional_note' => $request->additional_note,
            'status' => $request->status,
            'rejection_reason' => $request->status == 'Rejected' ? $request->rejection_reason : NULL,
            'rejected_by' => $request->status == 'Rejected' ? auth()->user()->id : NULL,
            'rejected_at' => $request->status == 'Rejected' ? now() : NULL,
            'approved_by' => $request->status == 'Running' ? auth()->user()->id : NULL,
            'approved_at' => $request->status == 'Running' ? now() : NULL,
        ]);

        if ($previousStatus === 'Pending') {
            $postTask->update([
                'boosting_start_at' => $request->status == 'Running' && $postTask->boosting_time != NULL ? now() : NULL,
            ]);
            $user->notify(new PostTaskCheckNotification($postTask));
        }

        return response()->json([
            'status' => 200,
        ]);
    }

    public function runningPostedTaskCanceled(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 401,
                'error' => 'Please enter a valid reason.'
            ]);
        } else {
            $postTask = PostTask::findOrFail($id);
            $user = User::findOrFail($postTask->user_id);

            $proofTasks = ProofTask::where('post_task_id', $postTask->id)->count();

            $refundAmount = number_format(($postTask->charge / $postTask->worker_needed) * ($postTask->worker_needed - $proofTasks), 2, '.', '');
            $user->deposit_balance = $user->deposit_balance + $refundAmount;
            $user->save();

            $postTask->status = 'Canceled';
            $postTask->cancellation_reason = $request->message;
            $postTask->canceled_by = auth()->user()->id;
            $postTask->canceled_at = now();
            $postTask->save();

            $user->notify(new PostTaskCheckNotification($postTask));


            return response()->json([
                'status' => 200,
                'success' => 'Task canceled successfully.'
            ]);
        }
    }

    public function runningPostedTaskPaused(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 401,
                'error' => 'Please enter a valid reason.'
            ]);
        } else {
            $postTask = PostTask::findOrFail($id);

            $postTask->status = 'Paused';
            $postTask->pausing_reason = $request->message;
            $postTask->paused_by = auth()->user()->id;
            $postTask->paused_at = now();
            $postTask->save();

            $user = User::findOrFail($postTask->user_id);
            $user->notify(new PostTaskCheckNotification($postTask));

            return response()->json([
                'status' => 200,
                'success' => 'Task paused successfully.'
            ]);
        }
    }

    // Worked Task .............................................................................................

    public function workedTaskListAll(Request $request){
        if ($request->ajax()) {
            $taskIds = ProofTask::whereNot('status', 'Reviewed')->pluck('post_task_id')->toArray();
            $query = PostTask::whereIn('id', $taskIds);
            $query->select('post_tasks.*')->orderBy('created_at', 'desc');

            $taskList = $query->get();

            return DataTables::of($taskList)
                ->addIndexColumn()
                ->editColumn('proof_submitted', function ($row) {
                    $proofSubmitted = ProofTask::where('post_task_id', $row->id)->count();
                    $proofStyleWidth = $proofSubmitted != 0 ? round(($proofSubmitted / $row->worker_needed) * 100, 2) : 100;
                    $progressBarClass = $proofSubmitted == 0 ? 'primary' : 'success';
                    return '
                    <div class="progress position-relative">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-' . $progressBarClass . '" role="progressbar" style="width: ' . $proofStyleWidth . '%" aria-valuenow="' . $proofSubmitted . '" aria-valuemin="0" aria-valuemax="' . $row->worker_needed . '"></div>
                        <span class="position-absolute w-100 text-center">' . $proofSubmitted . '/' . $row->worker_needed . '</span>
                    </div>
                    ';
                })
                ->editColumn('proof_status', function ($row) {
                    $statuses = [
                        'Pending' => 'bg-warning',
                        'Approved' => 'bg-success',
                        'Rejected' => 'bg-danger',
                        'Reviewed' => 'bg-info'
                    ];
                    $proofStatus = '';
                    $proofCount = ProofTask::where('post_task_id', $row->id)->count();
                    if ($proofCount === 0) {
                        return '<span class="badge bg-secondary">Proof not submitted yet.</span>';
                    }
                    foreach ($statuses as $status => $class) {
                        $count = ProofTask::where('post_task_id', $row->id)->where('status', $status)->count();
                        if ($count > 0) {
                            $proofStatus .= "<span class=\"badge $class\"> $status: $count</span> ";
                        }
                    }
                    return $proofStatus;
                })
                ->editColumn('action', function ($row) {
                    $btn = '
                        <a href="' . route('backend.all.worked_task_view', encrypt($row->id)) . '" class="btn btn-info btn-xs">Check</a>
                    ';
                    return $btn;
                })
                ->rawColumns(['proof_submitted', 'proof_status', 'action'])
                ->make(true);
        }
        return view('backend.worked_task.all');
    }

    public function allWorkedTaskView(Request $request, string $id){

        if ($request->ajax()) {
            $query = ProofTask::where('post_task_id', decrypt($id))->whereNot('status', 'Reviewed');

            $query->select('proof_tasks.*');

            $proofTasks = $query->get();

            return DataTables::of($proofTasks)
                ->addIndexColumn()
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

        $postTask = PostTask::findOrFail(decrypt($id));
        $proofSubmitted = ProofTask::where('post_task_id', $postTask->id)->get();
        return view('backend.worked_task.all_list', compact('postTask', 'proofSubmitted'));
    }

    public function workedTaskListReviewed(Request $request){
        if ($request->ajax()) {
            $taskIds = ProofTask::where('status', 'Reviewed')->pluck('post_task_id')->toArray();
            $query = PostTask::whereIn('id', $taskIds);
            $query->select('post_tasks.*')->orderBy('created_at', 'desc');

            $taskList = $query->get();

            return DataTables::of($taskList)
                ->addIndexColumn()
                ->editColumn('proof_submitted', function ($row) {
                    $proofSubmitted = ProofTask::where('post_task_id', $row->id)->count();
                    $proofStyleWidth = $proofSubmitted != 0 ? round(($proofSubmitted / $row->worker_needed) * 100, 2) : 100;
                    $progressBarClass = $proofSubmitted == 0 ? 'primary' : 'success';
                    return '
                    <div class="progress position-relative">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-' . $progressBarClass . '" role="progressbar" style="width: ' . $proofStyleWidth . '%" aria-valuenow="' . $proofSubmitted . '" aria-valuemin="0" aria-valuemax="' . $row->worker_needed . '"></div>
                        <span class="position-absolute w-100 text-center">' . $proofSubmitted . '/' . $row->worker_needed . '</span>
                    </div>
                    ';
                })
                ->editColumn('reviewed_status', function ($row) {
                    $proofCount = ProofTask::where('post_task_id', $row->id)->where('status', 'Reviewed')->count();
                    return '<span class="badge bg-primary">Reviewed: '.$proofCount.'</span>';
                })
                ->editColumn('action', function ($row) {
                    $btn = '
                        <a href="' . route('backend.reviewed.worked_task_view', encrypt($row->id)) . '" class="btn btn-info btn-xs">Check</a>
                    ';
                    return $btn;
                })
                ->rawColumns(['proof_submitted', 'reviewed_status', 'action'])
                ->make(true);
        }
        return view('backend.worked_task.reviewed');
    }

    public function reviewedWorkedTaskView(Request $request, string $id){

        if ($request->ajax()) {
            $query = ProofTask::where('post_task_id', decrypt($id))->where('status', 'Reviewed');

            $query->select('proof_tasks.*');

            $proofTasks = $query->get();

            return DataTables::of($proofTasks)
                ->addIndexColumn()
                ->editColumn('user', function ($row) {
                    $userDetail = UserDetail::where('user_id', $row->user_id)->first();
                    $user = '
                        <span class="badge bg-dark">Id: ' . $row->user->id . '</span>
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

        $postTask = PostTask::findOrFail(decrypt($id));
        $proofSubmitted = ProofTask::where('post_task_id', $postTask->id)->get();
        return view('backend.worked_task.reviewed_list', compact('postTask', 'proofSubmitted'));
    }

    public function workedTaskCheck($id)
    {
        $proofTask = ProofTask::findOrFail($id);
        return view('backend.worked_task.check', compact('proofTask'));
    }

    public function workedTaskCheckUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'rejected_reason' => 'required_if:status,Rejected',
            'rejected_reason_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $proofTask = ProofTask::findOrFail($id);

            $postTask = PostTask::findOrFail($proofTask->post_task_id);

            $postTaskUser = User::findOrFail($postTask->user_id);
            $proofTaskUser = User::findOrFail($proofTask->user_id);

            if ($request->status == 'Rejected') {
                $postTaskUser->update([
                    'deposit_balance' => $postTaskUser->deposit_balance + $postTask->working_charge,
                ]);

                $rejected_reason_photo_name = null;
                if ($request->file('rejected_reason_photo')) {
                    $manager = new ImageManager(new Driver());
                    $rejected_reason_photo_name = $id."-rejected_reason_photo-".date('YmdHis').".".$request->file('rejected_reason_photo')->getClientOriginalExtension();
                    $image = $manager->read($request->file('rejected_reason_photo'));
                    $image->toJpeg(80)->save(base_path("public/uploads/task_proof_rejected_reason_photo/").$rejected_reason_photo_name);
                }

                $proofTask->rejected_reason = $request->rejected_reason;
                $proofTask->rejected_reason_photo = $rejected_reason_photo_name;
                $proofTask->rejected_at = now();
                $proofTask->rejected_by = auth()->user()->id;
            }else{
                $proofTaskUser->update([
                    'withdraw_balance' => $proofTaskUser->withdraw_balance + $postTask->working_charge,
                ]);

                $proofTask->approved_at = now();
                $proofTask->approved_by = auth()->user()->id;
            }

            $proofTask->status = $request->status;
            $proofTask->save();

            $postTaskUser->notify(new ReviewedTaskProofCheckNotification($proofTask));
            $proofTaskUser->notify(new ReviewedTaskProofCheckNotification($proofTask));

            return response()->json([
                'status' => 200,
            ]);
        }
    }
}
