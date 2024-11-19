<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use App\Models\PostTask;
use App\Models\ProofTask;
use App\Models\TaskPostCharge;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Rating;
use App\Models\Bonus;
use App\Models\Report;
use App\Notifications\RatingNotification;
use App\Notifications\BonusNotification;
use Carbon\Carbon;


class PostedTaskController extends Controller
{
    public function postTask()
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            $categories = Category::where('status', 'Active')->get();
            return view('frontend.post_task.create', compact('categories'));
        }
    }

    public function postTaskGetSubCategories(Request $request)
    {
        $categoryId = $request->category_id;
        $subCategories = SubCategory::where('category_id', $categoryId)->get();

        $response = [];
        if ($subCategories->isNotEmpty()) {
            $response['sub_categories'] = $subCategories;
        }

        return response()->json($response);
    }

    public function postTaskGetChildCategories(Request $request)
    {
        $categoryId = $request->category_id;
        $subCategoryId = $request->sub_category_id;
        $childCategories = ChildCategory::where('sub_category_id', $subCategoryId)->get();

        $response = [];
        if ($childCategories->isNotEmpty()) {
            $response['child_categories'] = $childCategories;
        }

        $response['task_post_charge'] = TaskPostCharge::where('category_id', $categoryId)
                                                ->where('sub_category_id', $subCategoryId)
                                                ->first();

        return response()->json($response);
    }

    public function postTaskGetTaskPostCharge(Request $request)
    {
        $categoryId = $request->category_id;
        $subCategoryId = $request->sub_category_id;
        $childCategoryId = $request->child_category_id;

        $charge = TaskPostCharge::where('category_id', $categoryId)
            ->where('sub_category_id', $subCategoryId)
            ->where('child_category_id', $childCategoryId)
            ->first();

        return response()->json($charge);
    }

    public function postTaskStore(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'required_proof_answer' => 'required|string',
            'additional_note' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'worker_needed' => 'required|numeric|min:1',
            'working_charge' => 'required|numeric|min:0',
            'required_proof_photo' => 'required|numeric|min:0',
            'boosting_time' => 'required|numeric|min:0',
            'work_duration' => 'required|numeric|min:3',
        ]);

        if($request->hasFile('thumbnail')){
            $manager = new ImageManager(new Driver());
            $thumbnail_photo_name = $request->user()->id."-thumbnail-photo". date('YmdHis') . "." . $request->file('thumbnail')->getClientOriginalExtension();
            $image = $manager->read($request->file('thumbnail'));
            $image->toJpeg(80)->save(base_path("public/uploads/task_thumbnail_photo/").$thumbnail_photo_name);
        }else{
            $thumbnail_photo_name = null;
        }

        $total_required_proof_photo_charge = get_default_settings('task_posting_additional_required_proof_photo_charge') * ($request->required_proof_photo > 1 ? $request->required_proof_photo - 1 : 0);
        $total_boosting_time_charge = get_default_settings('task_posting_boosting_time_charge') * ($request->boosting_time / 15);
        $total_work_duration_charge = get_default_settings('task_posting_additional_work_duration_charge') * ($request->work_duration - 3);

        $site_charge = number_format((($request->worker_needed * $request->working_charge) * get_default_settings('task_posting_charge_percentage')) / 100, 2, '.', '');
        $task_charge = number_format(($request->worker_needed * $request->working_charge) + $site_charge, 2, '.', '');
        $total_task_charge = number_format($task_charge + $total_required_proof_photo_charge + $total_boosting_time_charge + $total_work_duration_charge, 2, '.', '');

        $request->user()->update([
            'deposit_balance' => $request->user()->deposit_balance - $total_task_charge,
        ]);

        PostTask::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'child_category_id' => $request->child_category_id,
            'title' => $request->title,
            'description' => $request->description,
            'required_proof_answer' => $request->required_proof_answer,
            'required_proof_photo' => $request->required_proof_photo,
            'required_proof_photo_charge' => $total_required_proof_photo_charge,
            'additional_note' => $request->additional_note,
            'thumbnail' => $thumbnail_photo_name,
            'worker_needed' => $request->worker_needed,
            'working_charge' => $request->working_charge,
            'site_charge' => $site_charge,
            'charge' => $task_charge,
            'boosting_time' => $request->boosting_time,
            'total_boosting_time' => $request->boosting_time,
            'boosting_time_charge' => $total_boosting_time_charge,
            'work_duration' => $request->work_duration,
            'work_duration_charge' => $total_work_duration_charge,
            'total_charge' => $total_task_charge,
            'status' => 'Pending',
        ]);

        $notification = array(
            'message' => 'Task post submitted successfully.',
            'alert-type' => 'success'
        );

        return to_route('posted_task.list.pending')->with($notification);
    }

    public function postTaskEdit($id)
    {
        $id = decrypt($id);
        $categories = Category::where('status', 'Active')->get();
        $postTask = PostTask::findOrFail($id);
        return view('frontend.post_task.edit', compact('categories', 'postTask'));
    }

    public function postTaskUpdate(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'required_proof_answer' => 'required|string',
            'additional_note' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'worker_needed' => 'required|numeric|min:1',
            'working_charge' => 'required|numeric|min:0',
            'required_proof_photo' => 'required|numeric|min:0',
            'boosting_time' => 'required|numeric|min:0',
            'work_duration' => 'required|numeric|min:3',
        ]);

        $postTask = PostTask::findOrFail($id);

        if($request->hasFile('thumbnail')){
            $manager = new ImageManager(new Driver());
            $thumbnail_photo_name = $request->user()->id."-thumbnail-photo". date('YmdHis') . "." . $request->file('thumbnail')->getClientOriginalExtension();
            $image = $manager->read($request->file('thumbnail'));
            $image->toJpeg(80)->save(base_path("public/uploads/task_thumbnail_photo/").$thumbnail_photo_name);
        }else{
            $thumbnail_photo_name = $postTask->thumbnail;
        }

        $total_required_proof_photo_charge = get_default_settings('task_posting_additional_required_proof_photo_charge') * ($request->required_proof_photo > 1 ? $request->required_proof_photo - 1 : 0);
        $total_boosting_time_charge = get_default_settings('task_posting_boosting_time_charge') * ($request->boosting_time / 15);
        $total_work_duration_charge = get_default_settings('task_posting_additional_work_duration_charge') * ($request->work_duration - 3);

        $site_charge = number_format((($request->worker_needed * $request->working_charge) * get_default_settings('task_posting_charge_percentage')) / 100, 2, '.', '');
        $task_charge = number_format(($request->worker_needed * $request->working_charge) + $site_charge, 2, '.', '');
        $total_task_charge = number_format($task_charge + $total_required_proof_photo_charge + $total_boosting_time_charge + $total_work_duration_charge, 2, '.', '');

        $request->user()->update([
            'deposit_balance' => $request->user()->deposit_balance - $total_task_charge,
        ]);

        $postTask->update([
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'child_category_id' => $request->child_category_id,
            'title' => $request->title,
            'description' => $request->description,
            'required_proof_answer' => $request->required_proof_answer,
            'required_proof_photo' => $request->required_proof_photo,
            'required_proof_photo_charge' => $total_required_proof_photo_charge,
            'additional_note' => $request->additional_note,
            'thumbnail' => $thumbnail_photo_name,
            'worker_needed' => $request->worker_needed,
            'working_charge' => $request->working_charge,
            'site_charge' => $site_charge,
            'charge' => $task_charge,
            'boosting_time' => $request->boosting_time,
            'total_boosting_time' => $request->boosting_time,
            'boosting_time_charge' => $total_boosting_time_charge,
            'work_duration' => $request->work_duration,
            'work_duration_charge' => $total_work_duration_charge,
            'total_charge' => $total_task_charge,
            'status' => 'Pending',
        ]);

        $notification = array(
            'message' => 'Task post updated successfully.',
            'alert-type' => 'success'
        );

        return to_route('posted_task.list.pending')->with($notification);
    }

    public function postedTaskListPending(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $query = PostTask::where('user_id', Auth::id())->where('status', 'Pending');

                $query->select('post_tasks.*')->orderBy('created_at', 'desc');

                $taskListPending = $query->get();

                return DataTables::of($taskListPending)
                    ->addIndexColumn()
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i:s A');
                    })
                    ->addColumn('action', function ($row) {
                        $actionBtn = '
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs canceledBtn">Canceled</button>
                        ';
                        return $actionBtn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('frontend.posted_task.pending');
        }
    }

    public function postedTaskView($id)
    {
        $postTask = PostTask::findOrFail($id);
        $proofSubmitted = ProofTask::where('post_task_id', $postTask->id)->get();
        $pendingProof = ProofTask::where('post_task_id', $postTask->id)->where('status', 'Pending')->count();
        $approvedProof = ProofTask::where('post_task_id', $postTask->id)->where('status', 'Approved')->count();
        $finallyRejectedProof = ProofTask::where('post_task_id', $postTask->id)->where('status', 'Rejected')
                        ->where(function ($query) {
                            $query->where(function ($query) {
                                    $query->whereNull('reviewed_at')
                                        ->where('rejected_at', '<=', now()->subHours(72));
                                })
                                ->orWhereNotNull('reviewed_at');
                        })->count();

        $waitingRejectedProof = ProofTask::where('post_task_id', $postTask->id)
                        ->where(function ($query) {
                            $query->where('status', 'Reviewed')
                                ->orWhere(function ($query) {
                                    $query->where('status', 'Rejected')
                                            ->whereNull('reviewed_at')
                                            ->where('rejected_at', '>', now()->subHours(72));
                                });
                        })->count();
        return view('frontend.posted_task.view', compact('postTask', 'proofSubmitted', 'pendingProof', 'approvedProof', 'finallyRejectedProof', 'waitingRejectedProof'));
    }

    public function postedTaskListRejected(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $query = PostTask::where('user_id', Auth::id())->where('status', 'Rejected');

                $query->select('post_tasks.*')->orderBy('rejected_at', 'desc');

                $taskListRejected = $query->get();

                return DataTables::of($taskListRejected)
                    ->addIndexColumn()
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->editColumn('rejected_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->rejected_at));
                    })
                    ->addColumn('action', function ($row) {
                        $actionBtn = '
                            <a href="' . route('post_task.edit', encrypt($row->id)) . '" class="btn btn-primary btn-xs">Edit</a>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs canceledBtn">Canceled</button>
                        ';
                        return $actionBtn;
                    })
                    ->rawColumns(['created_at', 'action'])
                    ->make(true);
            }
            return view('frontend.posted_task.rejected');
        }
    }

    public function runningPostedTaskCanceled(Request $request, $id)
    {
        $postTask = PostTask::findOrFail($id);

        $proofTasks = ProofTask::where('post_task_id', $id)->whereIn('status', ['Pending', 'Reviewed'])->count();

        if ($proofTasks > 0) {
            return response()->json([
                'status' => 400,
                'error' => 'You can not cancel this task. Because some workers are already submitted proof. If you want to cancel this task, please reject or approve all proof first.'
            ]);
        }

        if ($request->has('check') && $request->check == true) {
            return response()->json([
                'status' => 200,
                'message' => 'Task found. Proceed with cancellation.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 401,
                'error' => 'Please enter a valid reason.'
            ]);
        } else {
            $user = User::findOrFail($postTask->user_id);
            $proofTasks = ProofTask::where('post_task_id', $postTask->id)->count();
            if ($proofTasks == 0) {
                $user->deposit_balance = $user->deposit_balance + $postTask->total_charge;
                $user->save();
            }else if ($postTask->status == 'Running' || $postTask->status == 'Paused') {

                $refundAmount = number_format(($postTask->charge / $postTask->worker_needed) * ($postTask->worker_needed - $proofTasks), 2, '.', '');

                $user->deposit_balance = $user->deposit_balance + $refundAmount;
                $user->save();
            }

            $postTask->status = 'Canceled';
            $postTask->cancellation_reason = $request->message;
            $postTask->canceled_by = auth()->user()->id;
            $postTask->canceled_at = now();
            $postTask->save();

            return response()->json([
                'status' => 200,
                'deposit_balance' => number_format($user->deposit_balance, 2, '.', ''),
                'success' => 'Task canceled successfully.'
            ]);
        }
    }

    public function postedTaskListRunning(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $query = PostTask::where('user_id', Auth::id())->where('status', 'Running');

                $query->select('post_tasks.*')->orderBy('approved_at', 'desc');

                $taskListRunning = $query->get();

                return DataTables::of($taskListRunning)
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
                    ->editColumn('total_charge', function ($row) {
                        $total_charge = '
                            <span class="badge bg-primary">' . get_site_settings('site_currency_symbol'). ' ' . $row->total_charge . '</span>
                        ';
                        return $total_charge;
                    })
                    ->editColumn('charge_status', function ($row) {
                        $pendingProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Pending')->count();
                        $approvedProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Approved')->count();
                        $finallyRejectedProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Rejected')
                            ->where(function ($query) {
                                $query->whereNull('reviewed_at')->where('rejected_at', '<=', now()->subHours(72))
                                    ->orWhereNotNull('reviewed_at');
                            })->count();
                        $waitingRejectedProof = ProofTask::where('post_task_id', $row->id)
                            ->where(function ($query) {
                                $query->where('status', 'Reviewed')
                                    ->orWhere('status', 'Rejected')->whereNull('reviewed_at')
                                    ->where('rejected_at', '>', now()->subHours(72));
                            })->count();

                        $proofStatus = '';
                        $currency = get_site_settings('site_currency_symbol');
                        $rate = $row->charge / $row->worker_needed;

                        if ($pendingProof > 0) {
                            $proofStatus .= '<span class="badge bg-primary">Pending: ' . $currency . ' ' . number_format($rate * $pendingProof, 2) . '</span> ';
                        }
                        if ($approvedProof > 0) {
                            $proofStatus .= '<span class="badge bg-success">Expenses: ' . $currency . ' ' . number_format($rate * $approvedProof, 2) . '</span> ';
                        }
                        if ($finallyRejectedProof > 0) {
                            $proofStatus .= '<span class="badge bg-danger">Refund: ' . $currency . ' ' . number_format($rate * $finallyRejectedProof, 2) . '</span> ';
                        }
                        if ($waitingRejectedProof > 0) {
                            $proofStatus .= '<span class="badge bg-warning">Hold: ' . $currency . ' ' . number_format($rate * $waitingRejectedProof, 2) . '</span> ';
                        }

                        return $proofStatus ?: '<span class="badge bg-secondary">Proof not submitted yet.</span>';
                    })
                    ->editColumn('approved_at', function ($row) {
                        return date('d M, Y h:i A', strtotime($row->approved_at));
                    })
                    ->editColumn('total_boosting_time', function ($row) {
                        $boosting_start_at = Carbon::parse($row->boosting_start_at);
                        $boostingEndTime = $boosting_start_at->addMinutes($row->boosting_time);

                        if ($row->total_boosting_time == null) {
                            return '<span class="badge bg-secondary">Not boosting</span>';
                        } else if ($boostingEndTime->isFuture()) {
                            return '<span class="countdown badge bg-success" data-end-time="' . $boostingEndTime->toIso8601String() .'"></span>';
                        } else {
                            if ($row->total_boosting_time < 60) {
                                return '
                                <span class="badge bg-danger">' . $row->total_boosting_time . ' Minute' . ($row->total_boosting_time > 1 ? 's' : '') . ' | Expired</span>
                                ';
                            } else {
                                $hours = round($row->total_boosting_time / 60, 1);
                                return '
                                <span class="badge bg-danger">' . $hours . ' Hour' . ($hours > 1 ? 's' : '') . ' | Expired</span>
                                ';
                            }
                        }
                    })
                    ->editColumn('action', function ($row) {
                        $btn = '
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs editBtn" data-bs-toggle="modal" data-bs-target=".editModal">Update</button>
                            <a href="' . route('proof_task.list', encrypt($row->id)) . '" class="btn btn-info btn-xs">Check</a>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs pausedBtn">Paused</button>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs canceledBtn">Canceled</button>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                        ';
                        return $btn;
                    })
                    ->rawColumns(['proof_submitted', 'proof_status', 'total_charge', 'charge_status', 'approved_at', 'total_boosting_time', 'action'])
                    ->make(true);
            }
            return view('frontend.posted_task.running');
        }
    }

    public function runningPostedTaskEdit(string $id)
    {
        $postTask = PostTask::where('id', $id)->first();
        return response()->json($postTask);
    }

    public function runningPostedTaskUpdate(Request $request, string $id)
    {
        $postTask = PostTask::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'worker_needed' => 'nullable|numeric|min:0',
            'boosting_time' => 'nullable|numeric|min:0',
            'work_duration' => 'nullable|numeric|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $site_charge = number_format((($request->worker_needed * $postTask->working_charge) * get_default_settings('task_posting_charge_percentage')) / 100, 2, '.', '');

            $worker_needed_charge = number_format((($request->worker_needed * $postTask->working_charge) + $site_charge), 2, '.', '');

            $boosting_time_charge = number_format(get_default_settings('task_posting_boosting_time_charge') * ($request->boosting_time / 15), 2, '.', '');

            $work_duration_charge = number_format((($request->work_duration > $postTask->work_duration ? $request->work_duration - $postTask->work_duration : 0) * get_default_settings('task_posting_additional_work_duration_charge')), 2, '.', '');

            $total_charge = number_format( $worker_needed_charge + $boosting_time_charge + $work_duration_charge, 2, '.', '');

            if ($request->user()->deposit_balance < $total_charge) {
                return response()->json([
                    'status' => 401,
                    'error' => 'Insufficient balance. Please deposit first. Your current balance is ' . $request->user()->deposit_balance . ' ' . get_site_settings('site_currency_symbol') . '.'
                ]);
            } else {
                $request->user()->update([
                    'deposit_balance' => $request->user()->deposit_balance - $total_charge,
                ]);

                PostTask::where('id', $id)->update([
                    'worker_needed' => $postTask->worker_needed + $request->worker_needed,
                    'site_charge' => $postTask->site_charge + $site_charge,
                    'charge' => $postTask->charge + $worker_needed_charge,
                    'boosting_time' => $request->boosting_time > 0 ? $request->boosting_time : $postTask->boosting_time,
                    'total_boosting_time' => $postTask->total_boosting_time + $request->boosting_time,
                    'boosting_time_charge' => $postTask->boosting_time_charge + $boosting_time_charge,
                    'boosting_start_at' => $request->boosting_time > 0 ? now() : $postTask->boosting_start_at,
                    'work_duration' => $request->work_duration,
                    'total_charge' => $postTask->total_charge + $total_charge,
                ]);

                return response()->json([
                    'status' => 200,
                    'deposit_balance' => number_format($request->user()->deposit_balance, 2, '.', ''),
                ]);
            }
        }
    }

    public function proofTaskList(Request $request, $id)
    {
        if ($request->ajax()) {
            $query = ProofTask::where('post_task_id', decrypt($id));

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $query->select('proof_tasks.*');

            $proofTasks = $query->get();

            return DataTables::of($proofTasks)
                ->addColumn('checkbox', function ($row) {
                    $checkPending = $row->status != 'Pending' ? 'disabled' : '';
                    $checkbox = '
                        <input type="checkbox" class="form-check-input checkbox" value="' . $row->id . '" ' . $checkPending . '>
                    ';
                    return $checkbox;
                })
                ->editColumn('user', function ($row) {
                    $user = '
                        <span class="badge bg-dark">Id: ' . $row->user->id . '</span>
                        <span class="badge bg-dark">Name: ' . $row->user->name . '</span>
                        <span class="badge bg-dark">Ip: ' . $row->user->userDetail->ip . '</span>
                    ';
                    return $user;
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'Pending') {
                        $status = '<span class="badge bg-warning">' . $row->status . '</span>';
                    } else if ($row->status == 'Approved') {
                        $status = '<span class="badge bg-success">' . $row->status . '</span>';
                    } else if ($row->status == 'Rejected') {
                        $status = '<span class="badge bg-danger">' . $row->status . '</span>';
                    }else {
                        $status = '<span class="badge bg-info">' . $row->status . '</span>';
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
                        $checked_at = 'N/A';
                    }
                    return $checked_at;
                })
                ->addColumn('action', function ($row) {
                    if ($row->status == 'Rejected') {
                        $actionBtn = '
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">Check</button>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs reportProofTaskBtn" data-bs-toggle="modal" data-bs-target=".reportProofTaskModal">Report</button>';
                    } else {
                        $actionBtn = '
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">Check</button>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['checkbox', 'user', 'status', 'created_at', 'checked_at', 'action'])
                ->make(true);
        }

        $postTask = PostTask::findOrFail(decrypt($id));
        $proofSubmitted = ProofTask::where('post_task_id', $postTask->id)->get();
        $pendingProof = ProofTask::where('post_task_id', $postTask->id)->where('status', 'Pending')->count();
        $approvedProof = ProofTask::where('post_task_id', $postTask->id)->where('status', 'Approved')->count();
        $finallyRejectedProof = ProofTask::where('post_task_id', $postTask->id)->where('status', 'Rejected')
                        ->where(function ($query) {
                            $query->where(function ($query) {
                                    $query->whereNull('reviewed_at')
                                        ->where('rejected_at', '<=', now()->subHours(72));
                                })
                                ->orWhereNotNull('reviewed_at');
                        })->count();

        $waitingRejectedProof = ProofTask::where('post_task_id', $postTask->id)
                        ->where(function ($query) {
                            $query->where('status', 'Reviewed')
                                ->orWhere(function ($query) {
                                    $query->where('status', 'Rejected')
                                            ->whereNull('reviewed_at')
                                            ->where('rejected_at', '>', now()->subHours(72));
                                });
                        })->count();
        return view('frontend.posted_task.all_proof_list', compact('postTask', 'proofSubmitted', 'pendingProof', 'approvedProof', 'finallyRejectedProof', 'waitingRejectedProof'));
    }

    public function proofTaskReport($id)
    {
        $proofTask = ProofTask::findOrFail($id);

        $reportStatus = Report::where('user_id', $proofTask->user_id)
                        ->where('post_task_id', $proofTask->post_task_id)
                        ->where('proof_task_id', $proofTask->id)
                        ->first();

        if ($reportStatus) {
            $formattedReportStatus = [
                'status' => $reportStatus->status,
                'reason' => $reportStatus->reason,
                'created_at' => $reportStatus->created_at->format('d M Y h:i A'),
                'photo' => $reportStatus->photo,
            ];
        } else {
            $formattedReportStatus = null; // Handle case when no report is found
        }

        return response()->json([
            'status' => 200,
            'reportStatus' => $formattedReportStatus,
            'proofTask' => $proofTask,
        ]);
    }

    public function proofTaskApprovedAll($id)
    {
        $proofTasks = ProofTask::where('post_task_id', $id)->where('status', 'Pending')->get();

        $postTask = PostTask::findOrFail($id);

        foreach ($proofTasks->pluck('user_id') as $user_id) {
            $user = User::findOrFail($user_id);
            $user->withdraw_balance = $user->withdraw_balance + $postTask->working_charge;
            $user->save();
        }

        foreach ($proofTasks as $proofTask) {
            $proofTask->status = 'Approved';
            $proofTask->approved_at = now();
            $proofTask->approved_by = auth()->user()->id;
            $proofTask->save();
        }

        return response()->json(['success' => 'Status updated successfully.']);
    }

    public function proofTaskSelectedItemApproved(Request $request)
    {
        $proofTasks = ProofTask::whereIn('id', $request->id)->get();

        $postTask = PostTask::findOrFail($proofTasks->first()->post_task_id);

        foreach ($proofTasks as $proofTask) {
            $user = User::findOrFail($proofTask->user_id);
            $user->withdraw_balance = $user->withdraw_balance + $postTask->working_charge;
            $user->save();
        }

        foreach ($proofTasks as $proofTask) {
            $proofTask->status = 'Approved';
            $proofTask->approved_at = now();
            $proofTask->approved_by = auth()->user()->id;
            $proofTask->save();
        }

        return response()->json(['success' => 'Status updated successfully.']);
    }

    public function proofTaskSelectedItemRejected(Request $request)
    {
        $proofTasks = ProofTask::whereIn('id', $request->id)->get();

        foreach ($proofTasks as $proofTask) {
            $proofTask->status = 'Rejected';
            $proofTask->rejected_reason = $request->message;
            $proofTask->rejected_at = now();
            $proofTask->rejected_by = auth()->user()->id;
            $proofTask->save();
        }

        return response()->json(['success' => 'Status updated successfully.']);
    }

    public function proofTaskCheck($id)
    {
        $proofTask = ProofTask::findOrFail($id);
        return view('frontend.posted_task.proof_check', compact('proofTask'));
    }

    public function proofTaskAllPendingCheck($id)
    {
        $proofTaskListPending = ProofTask::where('post_task_id', $id)->where('status', 'Pending')->with('user', 'user_detail')->get();

        return response()->json([
            'status' => 200,
            'proofTaskListPending' => $proofTaskListPending,
        ]);
    }

    public function proofTaskCheckUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Approved,Rejected',
            'bonus' => 'nullable|numeric|min:0|max:' . get_default_settings('task_proof_max_bonus_amount'),
            'rating' => 'nullable|numeric|min:0|max:5',
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
            $user = User::findOrFail($proofTask->user_id);

            if ($request->status == 'Approved') {
                $user->withdraw_balance = $user->withdraw_balance + $postTask->working_charge + $request->bonus;
                $user->save();

                if ($request->rating) {
                    Rating::create([
                        'user_id' => $proofTask->user_id,
                        'rated_by' => auth()->user()->id,
                        'post_task_id' => $postTask->id,
                        'rating' => $request->rating,
                    ]);
                    $rating = Rating::where('user_id', $proofTask->user_id)->where('post_task_id', $postTask->id)->first();
                    $user->notify(new RatingNotification($rating));
                }

                if ($request->bonus) {
                    if ($request->bonus <= auth()->user()->deposit_balance) {
                        auth()->user()->update([
                            'deposit_balance' => auth()->user()->deposit_balance - $request->bonus
                        ]);
                    }else if ($request->bonus <= auth()->user()->withdraw_balance) {
                        auth()->user()->update([
                            'withdraw_balance' => auth()->user()->withdraw_balance - $request->bonus
                        ]);
                    }else{
                        return response()->json([
                            'status' => 401,
                            'error' => 'Insufficient balance. Please deposit first. Your current deposit balance is ' . get_site_settings('site_currency_symbol') . ' ' . auth()->user()->deposit_balance . ' and withdraw balance is ' . get_site_settings('site_currency_symbol') . ' ' . auth()->user()->withdraw_balance . '.'
                        ]);
                    }

                    Bonus::create([
                        'user_id' => $proofTask->user_id,
                        'bonus_by' => auth()->user()->id,
                        'type' => 'Proof Task Approved Bonus',
                        'post_task_id' => $postTask->id,
                        'amount' => $request->bonus,
                    ]);
                    $bonus = Bonus::where('user_id', $proofTask->user_id)->where('post_task_id', $postTask->id)->first();
                    $user->notify(new BonusNotification($bonus));
                }
            }

            $rejected_reason_photo_name = null;
            if ($request->file('rejected_reason_photo')) {
                $manager = new ImageManager(new Driver());
                $rejected_reason_photo_name = $id."-rejected_reason_photo-".date('YmdHis').".".$request->file('rejected_reason_photo')->getClientOriginalExtension();
                $image = $manager->read($request->file('rejected_reason_photo'));
                $image->toJpeg(80)->save(base_path("public/uploads/task_proof_rejected_reason_photo/").$rejected_reason_photo_name);
            }

            $proofTask->status = $request->status;
            $proofTask->rejected_reason = $request->rejected_reason ?? NULL;
            $proofTask->rejected_reason_photo = $rejected_reason_photo_name;
            $proofTask->rejected_at = $request->status == 'Rejected' ? now() : NULL;
            $proofTask->rejected_by = $request->status == 'Rejected' ? auth()->user()->id : NULL;
            $proofTask->approved_at = $request->status == 'Approved' ? now() : NULL;
            $proofTask->approved_by = $request->status == 'Approved' ? auth()->user()->id : NULL;
            $proofTask->save();

            return response()->json([
                'status' => 200,
                'deposit_balance' => number_format(auth()->user()->deposit_balance, 2, '.', ''),
                'withdraw_balance' => number_format(auth()->user()->withdraw_balance, 2, '.', ''),
            ]);
        }
    }

    public function runningPostedTaskPausedResume($id)
    {
        $postTask = PostTask::findOrFail($id);

        if ($postTask->status == 'Paused') {
            $postTask->status = 'Running';
        } else if ($postTask->status == 'Running') {
            $postTask->paused_at = now();
            $postTask->paused_by = auth()->user()->id;
            $postTask->status = 'Paused';
        }

        $postTask->save();

        return response()->json(['success' => 'Status updated successfully.']);
    }

    public function postedTaskListCanceled(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $query = PostTask::where('user_id', Auth::id())->where('status', 'Canceled');

                $query->select('post_tasks.*')->orderBy('canceled_at', 'desc');

                $taskListCanceled = $query->get();

                return DataTables::of($taskListCanceled)
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
                    ->editColumn('total_charge', function ($row) {
                        $total_charge = '
                            <span class="badge bg-primary">' . get_site_settings('site_currency_symbol'). ' ' . $row->total_charge . '</span>
                        ';
                        return $total_charge;
                    })
                    ->editColumn('charge_status', function ($row) {
                        $pendingProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Pending')->count();
                        $approvedProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Approved')->count();
                        $finallyRejectedProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Rejected')
                            ->where(function ($query) {
                                $query->whereNull('reviewed_at')->where('rejected_at', '<=', now()->subHours(72))
                                    ->orWhereNotNull('reviewed_at');
                            })->count();
                        $waitingRejectedProof = ProofTask::where('post_task_id', $row->id)
                            ->where(function ($query) {
                                $query->where('status', 'Reviewed')
                                    ->orWhere('status', 'Rejected')->whereNull('reviewed_at')
                                    ->where('rejected_at', '>', now()->subHours(72));
                            })->count();

                        $proofStatus = '';
                        $currency = get_site_settings('site_currency_symbol');
                        $rate = $row->charge / $row->worker_needed;

                        if ($pendingProof > 0) {
                            $proofStatus .= '<span class="badge bg-primary">Pending: ' . $currency . ' ' . number_format($rate * $pendingProof, 2) . '</span> ';
                        }
                        if ($approvedProof > 0) {
                            $proofStatus .= '<span class="badge bg-success">Expenses: ' . $currency . ' ' . number_format($rate * $approvedProof, 2) . '</span> ';
                        }
                        if ($finallyRejectedProof > 0) {
                            $proofStatus .= '<span class="badge bg-danger">Refund: ' . $currency . ' ' . number_format($rate * $finallyRejectedProof, 2) . '</span> ';
                        }
                        if ($waitingRejectedProof > 0) {
                            $proofStatus .= '<span class="badge bg-warning">Hold: ' . $currency . ' ' . number_format($rate * $waitingRejectedProof, 2) . '</span> ';
                        }

                        return $proofStatus ?: '<span class="badge bg-secondary">Proof not submitted yet.</span>';
                    })
                    ->editColumn('cancellation_reason', function ($row) {
                        if ($row->cancellation_reason == 'Work duration exceeded') {
                            return '<span class="badge bg-danger">' . $row->cancellation_reason . '</span>';
                        } else {
                            return '<span class="badge bg-warning">' . $row->cancellation_reason . '</span>';
                        }
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->editColumn('canceled_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->canceled_at));
                    })
                    ->editColumn('canceled_by', function ($row) {
                        if ($row->canceledBy->user_type =='Backend') {
                            return '<span class="badge bg-primary">Admin</span>';
                        } else {
                            return '<span class="badge bg-info">'. $row->canceledBy->name .'</span>';
                        }
                    })
                    ->editColumn('action', function ($row) {
                        $btn = '
                            <a href="' . route('proof_task.list', encrypt($row->id)) . '" class="btn btn-success btn-xs">Check</a>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                        ';
                        return $btn;
                    })
                    ->rawColumns(['proof_submitted', 'proof_status', 'total_charge', 'charge_status', 'cancellation_reason', 'canceled_by', 'action'])
                    ->make(true);
            }
            return view('frontend.posted_task.canceled');
        }
    }

    public function postedTaskListPaused(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $query = PostTask::where('user_id', Auth::id())->where('status', 'Paused');

                $query->select('post_tasks.*')->orderBy('paused_at', 'desc');

                $taskListPaused = $query->get();

                return DataTables::of($taskListPaused)
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
                    ->editColumn('total_charge', function ($row) {
                        $total_charge = '
                            <span class="badge bg-primary">' . get_site_settings('site_currency_symbol'). ' ' . $row->total_charge . '</span>
                        ';
                        return $total_charge;
                    })
                    ->editColumn('charge_status', function ($row) {
                        $pendingProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Pending')->count();
                        $approvedProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Approved')->count();
                        $finallyRejectedProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Rejected')
                            ->where(function ($query) {
                                $query->whereNull('reviewed_at')->where('rejected_at', '<=', now()->subHours(72))
                                    ->orWhereNotNull('reviewed_at');
                            })->count();
                        $waitingRejectedProof = ProofTask::where('post_task_id', $row->id)
                            ->where(function ($query) {
                                $query->where('status', 'Reviewed')
                                    ->orWhere('status', 'Rejected')->whereNull('reviewed_at')
                                    ->where('rejected_at', '>', now()->subHours(72));
                            })->count();

                        $proofStatus = '';
                        $currency = get_site_settings('site_currency_symbol');
                        $rate = $row->charge / $row->worker_needed;

                        if ($pendingProof > 0) {
                            $proofStatus .= '<span class="badge bg-primary">Pending: ' . $currency . ' ' . number_format($rate * $pendingProof, 2) . '</span> ';
                        }
                        if ($approvedProof > 0) {
                            $proofStatus .= '<span class="badge bg-success">Expenses: ' . $currency . ' ' . number_format($rate * $approvedProof, 2) . '</span> ';
                        }
                        if ($finallyRejectedProof > 0) {
                            $proofStatus .= '<span class="badge bg-danger">Refund: ' . $currency . ' ' . number_format($rate * $finallyRejectedProof, 2) . '</span> ';
                        }
                        if ($waitingRejectedProof > 0) {
                            $proofStatus .= '<span class="badge bg-warning">Hold: ' . $currency . ' ' . number_format($rate * $waitingRejectedProof, 2) . '</span> ';
                        }

                        return $proofStatus ?: '<span class="badge bg-secondary">Proof not submitted yet.</span>';
                    })
                    ->editColumn('pausing_reason', function ($row) {
                        if ($row->pausing_reason == null) {
                            return '<span class="badge bg-info">N/A</span>';
                        } else {
                            return '<span class="badge bg-warning">' . $row->pausing_reason . '</span>';
                        }
                    })
                    ->editColumn('paused_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->paused_at));
                    })
                    ->editColumn('paused_by', function ($row) {
                        if ($row->pausedBy->user_type =='Backend') {
                            return '<span class="badge bg-primary">Admin</span>';
                        } else {
                            return '<span class="badge bg-info">'. $row->pausedBy->name .'</span>';
                        }
                    })
                    ->editColumn('action', function ($row) {
                        $btn = '';

                        if ($row->pausedBy->user_type !== 'Backend') {
                            $btn .= '<button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs resumeBtn">Resume</button>';
                        }

                        $btn .= '
                            <a href="' . route('proof_task.list', encrypt($row->id)) . '" class="btn btn-success btn-xs">Check</a>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs canceledBtn">Canceled</button>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                        ';

                        return $btn;
                    })
                    ->rawColumns(['proof_submitted', 'proof_status', 'total_charge', 'charge_status', 'pausing_reason', 'paused_by', 'action'])
                    ->make(true);
            }
            return view('frontend.posted_task.paused');
        }
    }

    public function postedTaskListCompleted(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $query = PostTask::where('user_id', Auth::id())->where('status', 'Completed');

                $query->select('post_tasks.*')->orderBy('completed_at', 'desc');

                $taskListCompleted = $query->get();

                return DataTables::of($taskListCompleted)
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
                    ->editColumn('total_charge', function ($row) {
                        $total_charge = '
                            <span class="badge bg-primary">' . get_site_settings('site_currency_symbol'). ' ' . $row->total_charge . '</span>
                        ';
                        return $total_charge;
                    })
                    ->editColumn('charge_status', function ($row) {
                        $pendingProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Pending')->count();
                        $approvedProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Approved')->count();
                        $finallyRejectedProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Rejected')
                            ->where(function ($query) {
                                $query->whereNull('reviewed_at')->where('rejected_at', '<=', now()->subHours(72))
                                    ->orWhereNotNull('reviewed_at');
                            })->count();
                        $waitingRejectedProof = ProofTask::where('post_task_id', $row->id)
                            ->where(function ($query) {
                                $query->where('status', 'Reviewed')
                                    ->orWhere('status', 'Rejected')->whereNull('reviewed_at')
                                    ->where('rejected_at', '>', now()->subHours(72));
                            })->count();

                        $proofStatus = '';
                        $currency = get_site_settings('site_currency_symbol');
                        $rate = $row->charge / $row->worker_needed;

                        if ($pendingProof > 0) {
                            $proofStatus .= '<span class="badge bg-primary">Pending: ' . $currency . ' ' . number_format($rate * $pendingProof, 2) . '</span> ';
                        }
                        if ($approvedProof > 0) {
                            $proofStatus .= '<span class="badge bg-success">Expenses: ' . $currency . ' ' . number_format($rate * $approvedProof, 2) . '</span> ';
                        }
                        if ($finallyRejectedProof > 0) {
                            $proofStatus .= '<span class="badge bg-danger">Refund: ' . $currency . ' ' . number_format($rate * $finallyRejectedProof, 2) . '</span> ';
                        }
                        if ($waitingRejectedProof > 0) {
                            $proofStatus .= '<span class="badge bg-warning">Hold: ' . $currency . ' ' . number_format($rate * $waitingRejectedProof, 2) . '</span> ';
                        }

                        return $proofStatus ?: '<span class="badge bg-secondary">Proof not submitted yet.</span>';
                    })
                    ->editColumn('approved_at', function ($row) {
                        return '<span class="badge bg-dark">' . date('d M Y h:i A', strtotime($row->approved_at)) . '</span>';
                    })
                    ->editColumn('completed_at', function ($row) {
                        return '<span class="badge bg-dark">' . date('d M Y h:i A', strtotime($row->completed_at)) . '</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $status = '
                            <a href="' . route('proof_task.list', encrypt($row->id)) . '" class="btn btn-info btn-xs">Check</a>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                        ';
                        return $status;
                    })
                    ->rawColumns(['proof_submitted', 'proof_status', 'total_charge', 'charge_status', 'approved_at', 'completed_at', 'action'])
                    ->make(true);
            }
            return view('frontend.posted_task.completed');
        }
    }
}
