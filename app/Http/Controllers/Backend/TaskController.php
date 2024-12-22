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
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TaskController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('posted_task_list.pending') , only:['postedTaskListPending', 'pendingPostedTaskView']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('posted_task_list.rejected') , only:['postedTaskListRejected', 'rejectedPostedTaskView']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('posted_task_list.running') , only:['postedTaskListRunning', 'runningPostedTaskView']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('posted_task_list.canceled') , only:['postedTaskListCanceled', 'canceledPostedTaskView']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('posted_task_list.paused'), only:['postedTaskListPaused', 'pausedPostedTaskView']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('posted_task_list.completed'), only:['postedTaskListCompleted', 'completedPostedTaskView']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('posted_task.update') , only:['postedTaskStatusUpdate']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('posted_task.canceled') , only:['runningPostedTaskCanceled']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('posted_task.paused.resume') , only:['runningPostedTaskPaused', 'runningPostedTaskPausedResume']),

            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('worked_task_list.all') , only:['workedTaskListAll', 'allWorkedTaskView']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('worked_task_list.reviewed') , only:['workedTaskListReviewed', 'reviewedWorkedTaskView']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('worked_task.check') , only:['workedTaskCheck', 'workedTaskCheckUpdate']),
        ];
    }

    // Posted Task .............................................................................................
    public function postedTaskListPending(Request $request)
    {
        if ($request->ajax()) {
            $query = PostTask::where('status', 'Pending');

            if ($request->posted_task_id) {
                $query->where('id', $request->posted_task_id);
            }

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

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
                    $viewBtn = '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">Check</button>';

                    return $viewBtn;
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

            if ($request->posted_task_id) {
                $query->where('id', $request->posted_task_id);
            }

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

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
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">View</button>
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

            if ($request->posted_task_id) {
                $query->where('id', $request->posted_task_id);
            }

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

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
                    $canCanceled = auth()->user()->can('posted_task.canceled');
                    $canPaused = auth()->user()->can('posted_task.paused.resume');

                    $viewBtn = '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">View</button>';
                    $canceledBtn = $canCanceled
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs canceledBtn">Canceled</button>'
                        : '';
                    $pausedBtn = $canPaused
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs pausedBtn">Paused</button>'
                        : '';

                    return $viewBtn . ' ' . $canceledBtn . ' ' . $pausedBtn;
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

            if ($request->posted_task_id) {
                $query->where('id', $request->posted_task_id);
            }

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

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
                    $bgColor = $row->canceledBy->user_type === 'Backend' ? 'bg-primary' : 'bg-info';
                    return '
                        <span class="badge ' . $bgColor . '">' . $row->canceledBy->name . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">View</button>
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

            if ($request->posted_task_id) {
                $query->where('id', $request->posted_task_id);
            }

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            $query->select('post_tasks.*')->orderBy('paused_at', 'desc');

            $taskList = $query->get();

            return DataTables::of($taskList)
                ->addIndexColumn()
                ->editColumn('user', function ($row) {
                    return $row->user->name;
                })
                ->editColumn('approved_at', function ($row) {
                    return date('d M, Y h:i A', strtotime($row->approved_at));
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
                ->editColumn('paused_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->paused_at)) . '</span>
                        ';
                })
                ->editColumn('paused_by', function ($row) {
                    $bgColor = $row->pausedBy->user_type === 'Backend' ? 'bg-primary' : 'bg-info';
                    return '
                        <span class="badge ' . $bgColor . '">' . $row->pausedBy->name . '</span>
                        ';
                })
                ->editColumn('action', function ($row) {
                    $canResume = auth()->user()->can('posted_task.paused.resume');

                    $pausedBtn = '';
                    $viewBtn = '';

                    if ($row->pausedBy->user_type === 'Backend' && $canResume) {
                        $pausedBtn = '<button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs resumeBtn">Resume</button>';
                    }

                    $viewBtn = '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">View</button>';

                    return $viewBtn . ' ' . $pausedBtn;
                })
                ->rawColumns(['user', 'proof_submitted', 'proof_status', 'approved_at', 'paused_at', 'paused_by', 'action'])
                ->make(true);
        }
        return view('backend.posted_task.paused');
    }

    public function pausedPostedTaskView(string $id)
    {
        $postTask = PostTask::where('id', $id)->first();
        return view('backend.posted_task.paused_show', compact('postTask'));
    }

    public function postedTaskListCompleted(Request $request)
    {
        if ($request->ajax()) {
            $query = PostTask::where('status', 'Completed');

            if ($request->posted_task_id) {
                $query->where('id', $request->posted_task_id);
            }

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

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
                ->editColumn('approved_at', function ($row) {
                    return date('d M, Y h:i A', strtotime($row->approved_at));
                })
                ->editColumn('completed_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->completed_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">View</button>
                    ';
                return $btn;
                })
                ->rawColumns(['user', 'worker_needed', 'approved_at', 'completed_at', 'action'])
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
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
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

        // Retrieve the PostTask model
        $postTask = PostTask::findOrFail($id);

        // Store the previous status
        $previousStatus = $postTask->status;

        $user = User::findOrFail($postTask->user_id);
        if ($request->status == 'Rejected') {
            $user->update([
                'deposit_balance' => $user->deposit_balance + $postTask->total_charge,
            ]);
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            if ($postTask->thumbnail && file_exists(public_path("uploads/task_thumbnail_photo/" . $postTask->thumbnail))) {
                unlink(public_path("uploads/task_thumbnail_photo/" . $postTask->thumbnail));
            }
            $thumbnail_photo_name = $request->user()->id . "-thumbnail-photo-" . now()->format('Ymdhis') . "." . $request->file('thumbnail')->getClientOriginalExtension();
            $request->file('thumbnail')->move(public_path("uploads/task_thumbnail_photo"), $thumbnail_photo_name);
        } else {
            $thumbnail_photo_name = $postTask->thumbnail;
        }

        // Update the PostTask
        $postTask->update([
            'title' => $request->title,
            'description' => $request->description,
            'required_proof_answer' => $request->required_proof_answer,
            'additional_note' => $request->additional_note,
            'thumbnail' => $thumbnail_photo_name,
            'status' => $request->status,
            'rejection_reason' => $request->status == 'Rejected' ? $request->rejection_reason : null,
            'rejected_by' => $request->status == 'Rejected' ? auth()->id() : null,
            'rejected_at' => $request->status == 'Rejected' ? now() : null,
            'approved_by' => $request->status == 'Running' ? auth()->id() : null,
            'approved_at' => $request->status == 'Running' ? now() : null,
        ]);

        // Handle boosting time and notification
        if ($previousStatus === 'Pending') {
            $postTask->update([
                'boosting_start_at' => $request->status == 'Running' && $postTask->boosting_time != 0 ? now() : null,
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

            if ($proofTasks === 0) {
                $user->deposit_balance = $user->deposit_balance + $postTask->total_cost;
                $user->save();
            }else{
                $refundAmount = number_format((($postTask->sub_cost + $postTask->site_charge) / $postTask->worker_needed) * ($postTask->worker_needed - $proofTasks), 2, '.', '');
                $user->deposit_balance = $user->deposit_balance + $refundAmount;
                $user->save();
            }

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

    public function runningPostedTaskPausedResume($id)
    {
        $postTask = PostTask::findOrFail($id);
        $postTask->status = 'Running';
        $postTask->save();

        $user = User::findOrFail($postTask->user_id);
        $user->notify(new PostTaskCheckNotification($postTask));

        return response()->json(['success' => 'Status updated successfully.']);
    }

    // Worked Task .............................................................................................

    public function workedTaskListPending(Request $request){
        if ($request->ajax()) {
            $taskIds = ProofTask::where('status', 'Pending')->pluck('post_task_id')->toArray();
            $query = PostTask::whereIn('id', $taskIds);

            if ($request->posted_task_id) {
                $query->where('id', $request->posted_task_id);
            }

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

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
        return view('backend.worked_task.pending');
    }

    public function pendingWorkedTaskView(Request $request, string $id){

        if ($request->ajax()) {
            $query = ProofTask::where('post_task_id', decrypt($id))->where('status', 'Pending');

            if ($request->proof_id) {
                $query->where('id', $request->proof_id);
            }

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            $query->select('proof_tasks.*');

            $proofTasks = $query->get();

            return DataTables::of($proofTasks)
                ->addIndexColumn()
                ->editColumn('id', function ($row) {
                    return '<span class="badge bg-primary">' . $row->id . '</span>';
                })
                ->editColumn('user', function ($row) {
                    $userDetail = UserDetail::where('user_id', $row->user_id)->first();
                    $user = '
                        <span class="badge bg-dark">Id: ' . $row->user->id . '</span>
                        <span class="badge bg-dark">Name: ' . $row->user->name . '</span>
                        <span class="badge bg-dark">Ip: ' . $userDetail->ip . '</span>
                    ';
                    return $user;
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'Pending') {
                        $status = '<span class="badge bg-info">' . $row->status . '</span>';
                    } else if ($row->status == 'Approved') {
                        $status = '<span class="badge bg-success">' . $row->status . '</span>';
                    } else if ($row->status == 'Rejected') {
                        $status = '<span class="badge bg-danger">' . $row->status . '</span>';
                    }else {
                        $status = '<span class="badge bg-warning">' . $row->status . '</span>';
                    }
                    return $status;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y h:i A');
                })
                ->editColumn('checked_at', function ($row) {
                    if ($row->approved_at) {
                        $checked_at = date('d M Y h:i A', strtotime($row->approved_at));
                    } else if ($row->rejected_at) {
                        $checked_at = date('d M Y h:i A', strtotime($row->rejected_at));
                    } else if ($row->reviewed_at) {
                        $checked_at = date('d M Y h:i A', strtotime($row->reviewed_at));
                    } else {
                        $checked_at = 'Waiting...';
                    }
                    return $checked_at;
                })
                ->editColumn('checked_by', function ($row) {
                    if ($row->approved_by) {
                        $checked_by = $row->approvedBy->user_type == 'Backend' ? 'Admin' : $row->approvedBy->name;
                        $checked_by = '<span class="badge bg-success">' . $checked_by . '</span>';
                    } else if ($row->rejected_by) {
                        $checked_by = $row->rejectedBy->user_type == 'Backend' ? 'Admin' : $row->rejectedBy->name;
                        $checked_by = '<span class="badge bg-primary">' . $checked_by . '</span>';
                    } else if ($row->reviewed_by) {
                        $checked_by = $row->user->name;
                        $checked_by = '<span class="badge bg-info">' . $checked_by . '</span>';
                    } else {
                        $checked_by = '<span class="badge bg-warning">Waiting...</span>';
                    }
                    return $checked_by;
                })
                ->addColumn('action', function ($row) {
                    $viewPermission = auth()->user()->can('worked_task.check');

                    $viewBtn = $viewPermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">View</button>'
                        : '';

                    return $viewBtn;
                })
                ->rawColumns(['id', 'user','status', 'created_at', 'checked_at', 'checked_by', 'action'])
                ->make(true);
        }

        $postTask = PostTask::findOrFail(decrypt($id));
        $proofSubmitted = ProofTask::where('post_task_id', $postTask->id)->get();
        return view('backend.worked_task.pending_list', compact('postTask', 'proofSubmitted'));
    }

    public function workedTaskListChecking(Request $request){
        if ($request->ajax()) {
            $taskIds = ProofTask::whereNotIn('status', ['Pending', 'Reviewed'])->pluck('post_task_id')->toArray();
            $query = PostTask::whereIn('id', $taskIds);

            if ($request->posted_task_id) {
                $query->where('id', $request->posted_task_id);
            }

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

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
        return view('backend.worked_task.checking');
    }

    public function checkingWorkedTaskView(Request $request, string $id){

        if ($request->ajax()) {
            $query = ProofTask::where('post_task_id', decrypt($id))->whereNotIn('status', ['Pending', 'Reviewed']);

            if ($request->proof_id) {
                $query->where('id', $request->proof_id);
            }

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            $query->select('proof_tasks.*');

            $proofTasks = $query->get();

            return DataTables::of($proofTasks)
                ->addIndexColumn()
                ->editColumn('id', function ($row) {
                    return '<span class="badge bg-primary">' . $row->id . '</span>';
                })
                ->editColumn('user', function ($row) {
                    $userDetail = UserDetail::where('user_id', $row->user_id)->first();
                    $user = '
                        <span class="badge bg-dark">Id: ' . $row->user->id . '</span>
                        <span class="badge bg-dark">Name: ' . $row->user->name . '</span>
                        <span class="badge bg-dark">Ip: ' . $userDetail->ip . '</span>
                    ';
                    return $user;
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'Pending') {
                        $status = '<span class="badge bg-info">' . $row->status . '</span>';
                    } else if ($row->status == 'Approved') {
                        $status = '<span class="badge bg-success">' . $row->status . '</span>';
                    } else if ($row->status == 'Rejected') {
                        $status = '<span class="badge bg-danger">' . $row->status . '</span>';
                    }else {
                        $status = '<span class="badge bg-warning">' . $row->status . '</span>';
                    }
                    return $status;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y h:i A');
                })
                ->editColumn('checked_at', function ($row) {
                    if ($row->approved_at) {
                        $checked_at = date('d M Y h:i A', strtotime($row->approved_at));
                    } else if ($row->rejected_at) {
                        $checked_at = date('d M Y h:i A', strtotime($row->rejected_at));
                    } else if ($row->reviewed_at) {
                        $checked_at = date('d M Y h:i A', strtotime($row->reviewed_at));
                    } else {
                        $checked_at = 'Waiting...';
                    }
                    return $checked_at;
                })
                ->editColumn('checked_by', function ($row) {
                    if ($row->approved_by) {
                        $checked_by = $row->approvedBy->user_type == 'Backend' ? 'Admin' : $row->approvedBy->name;
                        $checked_by = '<span class="badge bg-success">' . $checked_by . '</span>';
                    } else if ($row->rejected_by) {
                        $checked_by = $row->rejectedBy->user_type == 'Backend' ? 'Admin' : $row->rejectedBy->name;
                        $checked_by = '<span class="badge bg-primary">' . $checked_by . '</span>';
                    } else if ($row->reviewed_by) {
                        $checked_by = $row->user->name;
                        $checked_by = '<span class="badge bg-info">' . $checked_by . '</span>';
                    } else {
                        $checked_by = '<span class="badge bg-warning">Waiting...</span>';
                    }
                    return $checked_by;
                })
                ->addColumn('action', function ($row) {
                    $viewPermission = auth()->user()->can('worked_task.check');

                    $viewBtn = $viewPermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">View</button>'
                        : '';

                    return $viewBtn;
                })
                ->rawColumns(['id', 'user','status', 'created_at', 'checked_at', 'checked_by', 'action'])
                ->make(true);
        }

        $postTask = PostTask::findOrFail(decrypt($id));
        $proofSubmitted = ProofTask::where('post_task_id', $postTask->id)->get();
        return view('backend.worked_task.checking_list', compact('postTask', 'proofSubmitted'));
    }

    public function workedTaskListReviewed(Request $request){
        if ($request->ajax()) {
            $taskIds = ProofTask::where('status', 'Reviewed')->pluck('post_task_id')->toArray();
            $query = PostTask::whereIn('id', $taskIds);

            if ($request->posted_task_id) {
                $query->where('id', $request->posted_task_id);
            }

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

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

            if ($request->proof_id) {
                $query->where('id', $request->proof_id);
            }

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            $query->select('proof_tasks.*');

            $proofTasks = $query->get();

            return DataTables::of($proofTasks)
                ->addIndexColumn()
                ->editColumn('id', function ($row) {
                    return '<span class="badge bg-primary">' . $row->id . '</span>';
                })
                ->editColumn('user', function ($row) {
                    $userDetail = UserDetail::where('user_id', $row->user_id)->first();
                    $user = '
                        <span class="badge bg-dark">Id: ' . $row->user->id . '</span>
                        <span class="badge bg-dark">Name: ' . $row->user->name . '</span>
                        <span class="badge bg-dark">Ip: ' . $userDetail->ip . '</span>
                    ';
                    return $user;
                })
                ->editColumn('status', function ($row) {
                    $status = '<span class="badge bg-warning">' . $row->status . '</span>';
                    return $status;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y h:i A');
                })
                ->editColumn('reviewed_at', function ($row) {
                    $checked_at = date('d M Y h:i A', strtotime($row->reviewed_at));
                    return $checked_at;
                })
                ->addColumn('action', function ($row) {
                    $viewPermission = auth()->user()->can('worked_task.check');

                    $viewBtn = $viewPermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">Check</button>'
                        : '';

                    return $viewBtn;
                })
                ->rawColumns(['id', 'user','status', 'created_at', 'reviewed_at', 'action'])
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
                    'deposit_balance' => $postTaskUser->deposit_balance + $postTask->income_of_each_worker,
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
                    'withdraw_balance' => $proofTaskUser->withdraw_balance + $postTask->income_of_each_worker,
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
