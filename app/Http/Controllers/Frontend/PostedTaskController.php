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
use Illuminate\Support\Str;

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
            $TaskPostChargeCategoryIds = TaskPostCharge::where('status', 'Active')->select('category_id')->groupBy('category_id')->pluck('category_id')->toArray();
            $categories = Category::where('status', 'Active')->whereIn('id', $TaskPostChargeCategoryIds)->get();
            return view('frontend.post_task.create', compact('categories'));
        }
    }

    public function postTaskGetSubCategories(Request $request)
    {
        $categoryId = $request->category_id;
        $TaskPostChargeSubCategoryIds = TaskPostCharge::where('status', 'Active')->select('sub_category_id')->groupBy('sub_category_id')->pluck('sub_category_id')->toArray();
        $subCategories = SubCategory::where('status', 'Active')->where('category_id', $categoryId)->whereIn('id', $TaskPostChargeSubCategoryIds)->get();

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
        $TaskPostChargeChildCategoryIds = TaskPostCharge::where('status', 'Active')->select('child_category_id')->groupBy('child_category_id')->pluck('child_category_id')->toArray();
        $childCategories = ChildCategory::where('status', 'Active')->where('sub_category_id', $subCategoryId)->whereIn('id', $TaskPostChargeChildCategoryIds)->get();

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
            'required_proof_photo' => 'nullable|numeric|min:0|max:10',
            'additional_note' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'worker_needed' => 'required|numeric|min:1',
            'income_of_each_worker' => 'required|numeric|min:0',
            'boosting_time' => 'required|numeric|min:0',
            'work_duration' => 'required|numeric|min:3',
        ]);

        if($request->hasFile('thumbnail')){
            $manager = new ImageManager(new Driver());
            $thumbnail_photo_name = $request->user()->id."-thumbnail-photo-". date('YmdhisA') . "." . $request->file('thumbnail')->getClientOriginalExtension();
            $image = $manager->read($request->file('thumbnail'));
            $image->toJpeg(80)->save(base_path("public/uploads/task_thumbnail_photo/").$thumbnail_photo_name);
        }else{
            $thumbnail_photo_name = null;
        }

        $total_required_proof_photo_charge = get_default_settings('task_posting_additional_required_proof_photo_charge') * ($request->required_proof_photo > 1 ? $request->required_proof_photo - 1 : 0);
        $total_boosting_time_charge = get_default_settings('task_posting_boosting_time_charge') * ($request->boosting_time / 15);
        $total_work_duration_charge = get_default_settings('task_posting_additional_work_duration_charge') * ($request->work_duration - 3);

        $task_cost = number_format(($request->worker_needed * $request->income_of_each_worker), 2, '.', '');
        $site_charge = number_format(($task_cost * get_default_settings('task_posting_charge_percentage')) / 100, 2, '.', '');
        $total_task_cost = number_format($task_cost + $site_charge + $total_required_proof_photo_charge + $total_boosting_time_charge + $total_work_duration_charge, 2, '.', '');

        $request->user()->update([
            'deposit_balance' => $request->user()->deposit_balance - $total_task_cost,
        ]);

        PostTask::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'child_category_id' => $request->child_category_id,
            'title' => $request->title,
            'description' => $request->description,
            'required_proof_answer' => $request->required_proof_answer,
            'required_proof_photo' => $request->required_proof_photo ?? 0,
            'required_proof_photo_charge' => $total_required_proof_photo_charge,
            'additional_note' => $request->additional_note,
            'thumbnail' => $thumbnail_photo_name,
            'worker_needed' => $request->worker_needed,
            'income_of_each_worker' => $request->income_of_each_worker,
            'sub_cost' => $task_cost,
            'site_charge' => $site_charge,
            'boosting_time' => $request->boosting_time,
            'total_boosting_time' => $request->boosting_time,
            'boosting_time_charge' => $total_boosting_time_charge,
            'work_duration' => $request->work_duration,
            'work_duration_charge' => $total_work_duration_charge,
            'total_cost' => $total_task_cost,
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
        $TaskPostChargeCategoryIds = TaskPostCharge::where('status', 'Active')->select('category_id')->groupBy('category_id')->pluck('category_id')->toArray();
        $categories = Category::where('status', 'Active')->whereIn('id', $TaskPostChargeCategoryIds)->get();
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
            'required_proof_photo' => 'nullable|numeric|min:0|max:10',
            'additional_note' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'worker_needed' => 'required|numeric|min:1',
            'income_of_each_worker' => 'required|numeric|min:0',
            'boosting_time' => 'required|numeric|min:0',
            'work_duration' => 'required|numeric|min:3',
        ]);

        $postTask = PostTask::findOrFail($id);

        if($request->hasFile('thumbnail')){
            // Delete old thumbnail photo
            if ($postTask->thumbnail) {
                if (file_exists(base_path("public/uploads/task_thumbnail_photo/").$postTask->thumbnail)) {
                    unlink(base_path("public/uploads/task_thumbnail_photo/").$postTask->thumbnail);
                }
            }
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

        $task_cost = number_format(($request->worker_needed * $request->income_of_each_worker), 2, '.', '');
        $site_charge = number_format(($task_cost * get_default_settings('task_posting_charge_percentage')) / 100, 2, '.', '');
        $total_task_cost = number_format($task_cost + $site_charge + $total_required_proof_photo_charge + $total_boosting_time_charge + $total_work_duration_charge, 2, '.', '');

        $request->user()->update([
            'deposit_balance' => $request->user()->deposit_balance - $total_task_cost,
        ]);

        $postTask->update([
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'child_category_id' => $request->child_category_id,
            'title' => $request->title,
            'description' => $request->description,
            'required_proof_answer' => $request->required_proof_answer,
            'required_proof_photo' => $request->required_proof_photo ?? 0,
            'required_proof_photo_charge' => $total_required_proof_photo_charge,
            'additional_note' => $request->additional_note,
            'thumbnail' => $thumbnail_photo_name,
            'worker_needed' => $request->worker_needed,
            'income_of_each_worker' => $request->income_of_each_worker,
            'sub_cost' => $task_cost,
            'site_charge' => $site_charge,
            'boosting_time' => $request->boosting_time,
            'total_boosting_time' => $request->boosting_time,
            'boosting_time_charge' => $total_boosting_time_charge,
            'work_duration' => $request->work_duration,
            'work_duration_charge' => $total_work_duration_charge,
            'total_cost' => $total_task_cost,
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

                // Total filtered count
                $totalTasksCount = $query->count();

                $taskListPending = $query->get();

                return DataTables::of($taskListPending)
                    ->addIndexColumn()
                    ->editColumn('total_cost', function ($row) {
                        $total_cost = '
                            <span class="badge bg-primary">' . get_site_settings('site_currency_symbol'). ' ' . $row->total_cost . '</span>
                        ';
                        return $total_cost;
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i:s A');
                    })
                    ->addColumn('action', function ($row) {
                        $actionBtn = '
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">View</button>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs canceledBtn">Canceled</button>
                        ';
                        return $actionBtn;
                    })
                    ->with(['totalTasksCount' => $totalTasksCount])
                    ->rawColumns(['total_cost', 'action'])
                    ->make(true);
            }
            return view('frontend.posted_task.pending');
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

                // Total filtered count
                $totalTasksCount = $query->count();

                $taskListRunning = $query->get();

                return DataTables::of($taskListRunning)
                    ->addIndexColumn()
                    ->editColumn('income_of_each_worker', function ($row) {
                        return get_site_settings('site_currency_symbol') . ' ' . $row->income_of_each_worker;
                    })
                    ->editColumn('total_boosting_time', function ($row) {
                        $boosting_start_at = Carbon::parse($row->boosting_start_at);
                        $boostingEndTime = $boosting_start_at->addMinutes((int) $row->boosting_time);

                        if ($row->total_boosting_time == 0) {
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
                    ->editColumn('work_duration', function ($row) {
                        $approvedDate = Carbon::parse($row->approved_at);
                        $endDate = $approvedDate->addDays((int) $row->work_duration);
                        return '<span class="badge bg-primary">' . $endDate->format('d M, Y h:i:s A') . '</span>';
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
                            'Pending' => 'bg-primary',
                            'Approved' => 'bg-success',
                            'Rejected' => 'bg-danger',
                            'Reviewed' => 'bg-warning'
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
                    ->editColumn('total_cost', function ($row) {
                        $total_cost = '
                            <span class="badge bg-primary">' . get_site_settings('site_currency_symbol'). ' ' . $row->total_cost . '</span>
                        ';
                        return $total_cost;
                    })
                    ->editColumn('charge_status', function ($row) {
                        $proofSubmitted = ProofTask::where('post_task_id', $row->id)->count();
                        $pendingProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Pending')->count();
                        $approvedProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Approved')->count();
                        $refundProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Rejected')
                            ->where(function ($query) {
                                $query->whereNull('reviewed_at')->where('rejected_at', '<=', now()->subHours(get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time')))
                                    ->orWhereNotNull('reviewed_at');
                            })->count();
                        $holdProof = ProofTask::where('post_task_id', $row->id)
                            ->where(function ($query) {
                                $query->where('status', 'Reviewed')
                                    ->orWhere('status', 'Rejected')->whereNull('reviewed_at')
                                    ->where('rejected_at', '>', now()->subHours(get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time')));
                            })->count();

                        $proofStatus = '';
                        $currency = get_site_settings('site_currency_symbol');
                        $rate = ($row->sub_cost + $row->site_charge) / $row->worker_needed;

                        if ($proofSubmitted > 0) {
                            $proofStatus .= '<span class="badge bg-dark">Waiting: ' . $currency . ' ' . number_format($rate * ($row->worker_needed - $proofSubmitted), 2) . '</span> ';
                        }
                        if ($pendingProof > 0) {
                            $proofStatus .= '<span class="badge bg-primary">Pending: ' . $currency . ' ' . number_format($rate * $pendingProof, 2) . '</span> ';
                        }
                        if ($approvedProof > 0 || $proofSubmitted > 0) {
                            $approvedCharge = ($row->income_of_each_worker * $approvedProof);
                            $proofStatus .= '<span class="badge bg-success">Worker Payment: ' . $currency . ' ' . number_format($approvedCharge, 2) . '</span> <span class="badge bg-success">Site Payment: ' . $currency . ' ' . number_format(((($rate * $approvedProof) - $approvedCharge) + $row->required_proof_photo_charge + $row->boosting_time_charge +  $row->work_duration_charge), 2) . '</span> ';
                        }
                        if ($refundProof > 0) {
                            $proofStatus .= '<span class="badge bg-danger">Refund: ' . $currency . ' ' . number_format($rate * $refundProof, 2) . '</span> ';
                        }
                        if ($holdProof > 0) {
                            $proofStatus .= '<span class="badge bg-warning">Hold: ' . $currency . ' ' . number_format($rate * $holdProof, 2) . '</span> ';
                        }
                        return $proofStatus ?: '<span class="badge bg-dark">Waiting: ' . number_format( $row->total_cost, 2) . '</span>';
                    })
                    ->editColumn('approved_at', function ($row) {
                        return date('d M, Y h:i A', strtotime($row->approved_at));
                    })
                    ->editColumn('action', function ($row) {
                        $btn = '
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs editBtn">Update</button>
                            <a href="' . route('proof_task.list.clear.filters', encrypt($row->id)) . '" class="btn btn-info btn-xs">Check</a>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs pausedBtn">Paused</button>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs canceledBtn">Canceled</button>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">View</button>
                        ';
                        return $btn;
                    })
                    ->with(['totalTasksCount' => $totalTasksCount])
                    ->rawColumns(['income_of_each_worker', 'total_boosting_time', 'work_duration', 'proof_submitted', 'proof_status', 'total_cost', 'charge_status', 'approved_at', 'action'])
                    ->make(true);
            }
            return view('frontend.posted_task.running');
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

                // Total filtered count
                $totalTasksCount = $query->count();

                $taskListPaused = $query->get();

                return DataTables::of($taskListPaused)
                    ->addIndexColumn()
                    ->editColumn('approved_at', function ($row) {
                        return date('d M, Y h:i A', strtotime($row->approved_at));
                    })
                    ->editColumn('boosting_time', function ($row) {
                        if ($row->boosting_time == 0) {
                            return '<span class="badge bg-secondary">Not boosting</span>';
                        } else {
                            if ($row->boosting_time < 60) {
                                return '
                                    <span class="badge bg-info">' . $row->boosting_time . ' Minute' . ($row->boosting_time > 1 ? 's' : '') . ' | Waiting</span>
                                ';
                            } else {
                                $hours = round($row->boosting_time / 60, 1);
                                return '
                                    <span class="badge bg-info">' . $hours . ' Hour' . ($hours > 1 ? 's' : '') . ' | Waiting</span>
                                ';
                            }
                        }
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
                    ->editColumn('work_duration', function ($row) {
                        $approvedDate = Carbon::parse($row->approved_at);
                        $endDate = $approvedDate->addDays((int) $row->work_duration);
                        return '<span class="badge bg-primary">' . $endDate->format('d M, Y h:i:s A') . '</span>';
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
                    ->editColumn('total_cost', function ($row) {
                        $total_cost = '
                            <span class="badge bg-primary">' . get_site_settings('site_currency_symbol'). ' ' . $row->total_cost . '</span>
                        ';
                        return $total_cost;
                    })
                    ->editColumn('charge_status', function ($row) {
                        $proofSubmitted = ProofTask::where('post_task_id', $row->id)->count();
                        $pendingProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Pending')->count();
                        $approvedProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Approved')->count();
                        $finallyRejectedProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Rejected')
                            ->where(function ($query) {
                                $query->whereNull('reviewed_at')->where('rejected_at', '<=', now()->subHours(get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time')))
                                    ->orWhereNotNull('reviewed_at');
                            })->count();
                        $waitingRejectedProof = ProofTask::where('post_task_id', $row->id)
                            ->where(function ($query) {
                                $query->where('status', 'Reviewed')
                                    ->orWhere('status', 'Rejected')->whereNull('reviewed_at')
                                    ->where('rejected_at', '>', now()->subHours(get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time')));
                            })->count();

                        $proofStatus = '';
                        $currency = get_site_settings('site_currency_symbol');
                        $rate = $row->sub_cost / $row->worker_needed;

                        if ($proofSubmitted > 0) {
                            $proofStatus .= '<span class="badge bg-dark">Waiting: ' . $currency . ' ' . number_format($rate * ($row->worker_needed - $proofSubmitted), 2) . '</span> ';
                        }
                        if ($pendingProof > 0) {
                            $proofStatus .= '<span class="badge bg-primary">Pending: ' . $currency . ' ' . number_format($rate * $pendingProof, 2) . '</span> ';
                        }
                        if ($approvedProof > 0 || $proofSubmitted > 0) {
                            $approvedCharge = ($row->income_of_each_worker * $approvedProof);
                            $proofStatus .= '<span class="badge bg-success">Expenses: ' . $currency . ' ' . number_format($approvedCharge + (($rate * $approvedProof) - $approvedCharge + $row->required_proof_photo_charge + $row->boosting_time_charge +  $row->work_duration_charge), 2) . '</span> ';
                        }
                        if ($finallyRejectedProof > 0) {
                            $proofStatus .= '<span class="badge bg-danger">Refund: ' . $currency . ' ' . number_format($rate * $finallyRejectedProof, 2) . '</span> ';
                        }
                        if ($waitingRejectedProof > 0) {
                            $proofStatus .= '<span class="badge bg-warning">Hold: ' . $currency . ' ' . number_format($rate * $waitingRejectedProof, 2) . '</span> ';
                        }

                        return $proofStatus ?: '<span class="badge bg-dark">Waiting: ' . number_format( $row->total_cost, 2) . '</span>';
                    })
                    ->editColumn('pausing_reason', function ($row) {
                        if ($row->pausing_reason == null) {
                            return '<span class="badge bg-info">N/A</span>';
                        } else {
                            $pausing_reason = Str::limit($row->pausing_reason,40, '...'); // Limit to 50 characters with "..."
                            return '<span class="badge bg-warning">' . e($pausing_reason) . '</span>';
                        }
                    })
                    ->addColumn('pausing_reason_full', function ($row) {
                        $pausing_reason = $row->pausing_reason ? nl2br(e($row->pausing_reason)) : 'N/A'; // Convert newlines to <br> and escape HTML
                        return '<span class="badge bg-info my-2">Reason: </span><br>' . $pausing_reason;
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
                            <a href="' . route('proof_task.list.clear.filters', encrypt($row->id)) . '" class="btn btn-success btn-xs">Check</a>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs canceledBtn">Canceled</button>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">View</button>
                        ';

                        return $btn;
                    })
                    ->with(['totalTasksCount' => $totalTasksCount])
                    ->rawColumns(['approved_at', 'boosting_time', 'proof_submitted', 'work_duration', 'proof_status', 'total_cost', 'charge_status', 'pausing_reason', 'pausing_reason_full', 'paused_by', 'action'])
                    ->make(true);
            }
            return view('frontend.posted_task.paused');
        }
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

                // Total filtered count
                $totalTasksCount = $query->count();

                $taskListRejected = $query->get();

                return DataTables::of($taskListRejected)
                    ->addIndexColumn()
                    ->editColumn('total_cost', function ($row) {
                        $total_cost = '
                            <span class="badge bg-primary">' . get_site_settings('site_currency_symbol'). ' ' . $row->total_cost . '</span>
                        ';
                        return $total_cost;
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->editColumn('rejected_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->rejected_at));
                    })
                    ->editColumn('rejection_reason', function ($row) {
                        $rejection_reason = Str::limit($row->rejection_reason,40, '...'); // Limit to 50 characters with "..."
                        return e($rejection_reason);
                    })
                    ->addColumn('rejection_reason_full', function ($row) {
                        $rejection_reason = nl2br(e($row->rejection_reason)); // Convert newlines to <br> and escape HTML
                        return '<span class="badge bg-info my-2">Reason: </span><br>' . $rejection_reason;
                    })
                    ->addColumn('action', function ($row) {
                        $actionBtn = '
                            <a href="' . route('post_task.edit', encrypt($row->id)) . '" class="btn btn-primary btn-xs">Edit</a>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs canceledBtn">Canceled</button>
                        ';
                        return $actionBtn;
                    })
                    ->with(['totalTasksCount' => $totalTasksCount])
                    ->rawColumns(['total_cost', 'created_at', 'rejection_reason', 'rejection_reason_full', 'action'])
                    ->make(true);
            }
            return view('frontend.posted_task.rejected');
        }
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
                $query = PostTask::where('user_id', Auth::id())
                    ->where('status', 'Canceled')
                    ->whereBetween('canceled_at', [now()->subDays(7), now()]);

                $query->select('post_tasks.*')->orderBy('canceled_at', 'desc');

                // Total filtered count
                $totalTasksCount = $query->count();

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
                    ->editColumn('total_cost', function ($row) {
                        $total_cost = '
                            <span class="badge bg-primary">' . get_site_settings('site_currency_symbol'). ' ' . $row->total_cost . '</span>
                        ';
                        return $total_cost;
                    })
                    ->editColumn('charge_status', function ($row) {
                        $proofSubmitted = ProofTask::where('post_task_id', $row->id)->count();
                        $pendingProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Pending')->count();
                        $approvedProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Approved')->count();
                        $refundProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Rejected')
                            ->where(function ($query) {
                                $query->whereNull('reviewed_at')->where('rejected_at', '<=', now()->subHours(get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time')))
                                    ->orWhereNotNull('reviewed_at');
                            })->count();
                        $holdProof = ProofTask::where('post_task_id', $row->id)
                            ->where(function ($query) {
                                $query->where('status', 'Reviewed')
                                    ->orWhere('status', 'Rejected')->whereNull('reviewed_at')
                                    ->where('rejected_at', '>', now()->subHours(get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time')));
                            })->count();

                        $proofStatus = '';
                        $currency = get_site_settings('site_currency_symbol');
                        $rate = ($row->sub_cost + $row->site_charge) / $row->worker_needed;

                        if ($proofSubmitted > 0) {
                            $proofStatus .= '<span class="badge bg-danger">Canceled Refund: ' . $currency . ' ' . number_format($rate * ($row->worker_needed - $proofSubmitted), 2) . '</span> ';
                        }
                        if ($pendingProof > 0) {
                            $proofStatus .= '<span class="badge bg-primary">Pending: ' . $currency . ' ' . number_format($rate * $pendingProof, 2) . '</span> ';
                        }
                        if ($approvedProof > 0 || $proofSubmitted > 0) {
                            $approvedCharge = ($row->income_of_each_worker * $approvedProof);
                            $proofStatus .= '<span class="badge bg-success">Worker Payment: ' . $currency . ' ' . number_format($approvedCharge, 2) . '</span> <span class="badge bg-success">Site Payment: ' . $currency . ' ' . number_format(((($rate * $approvedProof) - $approvedCharge) + $row->required_proof_photo_charge + $row->boosting_time_charge +  $row->work_duration_charge), 2) . '</span> ';
                        }
                        if ($refundProof > 0) {
                            $proofStatus .= '<span class="badge bg-danger">Refund: ' . $currency . ' ' . number_format($rate * $refundProof, 2) . '</span> ';
                        }
                        if ($holdProof > 0) {
                            $proofStatus .= '<span class="badge bg-warning">Hold: ' . $currency . ' ' . number_format($rate * $holdProof, 2) . '</span> ';
                        }
                        return $proofStatus ?: '<span class="badge bg-danger">Canceled Refund: ' . $currency . ' ' . number_format( ($row->total_cost), 2) . '</span>';
                    })
                    ->editColumn('cancellation_reason', function ($row) {
                        if ($row->cancellation_reason == 'Work duration exceeded') {
                            return '<span class="badge bg-danger">' . $row->cancellation_reason . '</span>';
                        } else {
                            $cancellation_reason = Str::limit($row->cancellation_reason,40, '...'); // Limit to 50 characters with "..."
                            return '<span class="badge bg-warning">' . e($cancellation_reason) . '</span>';
                        }
                    })
                    ->addColumn('cancellation_reason_full', function ($row) {
                        $cancellation_reason = nl2br(e($row->cancellation_reason)); // Convert newlines to <br> and escape HTML
                        return '<span class="badge bg-info my-2">Reason: </span><br>' . $cancellation_reason;
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
                            <a href="' . route('proof_task.list.clear.filters', encrypt($row->id)) . '" class="btn btn-success btn-xs">Check</a>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">View</button>
                        ';
                        return $btn;
                    })
                    ->with(['totalTasksCount' => $totalTasksCount])
                    ->rawColumns(['proof_submitted', 'proof_status', 'total_cost', 'charge_status', 'cancellation_reason', 'cancellation_reason_full', 'canceled_by', 'action'])
                    ->make(true);
            }
            return view('frontend.posted_task.canceled');
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
                $query = PostTask::where('user_id', Auth::id())
                    ->where('status', 'Completed')
                    ->whereBetween('completed_at', [now()->subDays(7), now()]);

                $query->select('post_tasks.*')->orderBy('completed_at', 'desc');

                // Total filtered count
                $totalTasksCount = $query->count();

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
                    ->editColumn('total_cost', function ($row) {
                        $total_cost = '
                            <span class="badge bg-primary">' . get_site_settings('site_currency_symbol'). ' ' . $row->total_cost . '</span>
                        ';
                        return $total_cost;
                    })
                    ->editColumn('charge_status', function ($row) {
                        $proofSubmitted = ProofTask::where('post_task_id', $row->id)->count();
                        $pendingProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Pending')->count();
                        $approvedProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Approved')->count();
                        $finallyRejectedProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Rejected')
                            ->where(function ($query) {
                                $query->whereNull('reviewed_at')->where('rejected_at', '<=', now()->subHours(get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time')))
                                    ->orWhereNotNull('reviewed_at');
                            })->count();
                        $waitingRejectedProof = ProofTask::where('post_task_id', $row->id)
                            ->where(function ($query) {
                                $query->where('status', 'Reviewed')
                                    ->orWhere('status', 'Rejected')->whereNull('reviewed_at')
                                    ->where('rejected_at', '>', now()->subHours(get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time')));
                            })->count();

                        $proofStatus = '';
                        $currency = get_site_settings('site_currency_symbol');
                        $rate = $row->sub_cost / $row->worker_needed;

                        if ($proofSubmitted > 0) {
                            $proofStatus .= '<span class="badge bg-dark">Waiting: ' . $currency . ' ' . number_format($rate * ($row->worker_needed - $proofSubmitted), 2) . '</span> ';
                        }
                        if ($pendingProof > 0) {
                            $proofStatus .= '<span class="badge bg-primary">Pending: ' . $currency . ' ' . number_format($rate * $pendingProof, 2) . '</span> ';
                        }
                        if ($approvedProof > 0 || $proofSubmitted > 0) {
                            $approvedCharge = ($row->income_of_each_worker * $approvedProof);
                            $proofStatus .= '<span class="badge bg-success">Expenses: ' . $currency . ' ' . number_format($approvedCharge + (($rate * $approvedProof) - $approvedCharge + $row->required_proof_photo_charge + $row->boosting_time_charge +  $row->work_duration_charge), 2) . '</span> ';
                        }
                        if ($finallyRejectedProof > 0) {
                            $proofStatus .= '<span class="badge bg-danger">Refund: ' . $currency . ' ' . number_format($rate * $finallyRejectedProof, 2) . '</span> ';
                        }
                        if ($waitingRejectedProof > 0) {
                            $proofStatus .= '<span class="badge bg-warning">Hold: ' . $currency . ' ' . number_format($rate * $waitingRejectedProof, 2) . '</span> ';
                        }

                        return $proofStatus ?: '<span class="badge bg-dark">Waiting: ' . number_format( $row->total_cost, 2) . '</span>';
                    })
                    ->editColumn('approved_at', function ($row) {
                        return '<span class="badge bg-dark">' . date('d M Y h:i A', strtotime($row->approved_at)) . '</span>';
                    })
                    ->editColumn('completed_at', function ($row) {
                        return '<span class="badge bg-dark">' . date('d M Y h:i A', strtotime($row->completed_at)) . '</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $status = '
                            <a href="' . route('proof_task.list.clear.filters', encrypt($row->id)) . '" class="btn btn-info btn-xs">Check</a>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">View</button>
                        ';
                        return $status;
                    })
                    ->with(['totalTasksCount' => $totalTasksCount])
                    ->rawColumns(['proof_submitted', 'proof_status', 'total_cost', 'charge_status', 'approved_at', 'completed_at', 'action'])
                    ->make(true);
            }
            return view('frontend.posted_task.completed');
        }
    }

    public function postedTaskView($id)
    {
        $postTask = PostTask::findOrFail($id);
        $proofSubmitted = ProofTask::where('post_task_id', $postTask->id)->get();
        $pendingProof = ProofTask::where('post_task_id', $postTask->id)->where('status', 'Pending')->count();
        $approvedProof = ProofTask::where('post_task_id', $postTask->id)->where('status', 'Approved')->count();
        $refundProof = ProofTask::where('post_task_id', $postTask->id)->where('status', 'Rejected')
                        ->where(function ($query) {
                            $query->where(function ($query) {
                                    $query->whereNull('reviewed_at')
                                        ->where('rejected_at', '<=', now()->subHours(get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time')));
                                })
                                ->orWhereNotNull('reviewed_at');
                        })->count();

        $holdProof = ProofTask::where('post_task_id', $postTask->id)
                        ->where(function ($query) {
                            $query->where('status', 'Reviewed')
                                ->orWhere(function ($query) {
                                    $query->where('status', 'Rejected')
                                            ->whereNull('reviewed_at')
                                            ->where('rejected_at', '>', now()->subHours(get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time')));
                                });
                        })->count();
        return view('frontend.posted_task.view', compact('postTask', 'proofSubmitted', 'pendingProof', 'approvedProof', 'refundProof', 'holdProof'));
    }

    public function postedTaskEdit(string $id)
    {
        $postTask = PostTask::where('id', $id)->first();

        if ($postTask) {
            $startTime = $postTask->approved_at;
            $workDurationDays = $postTask->work_duration;

            $endTime = Carbon::parse($startTime)->addDays( (int) $workDurationDays);

            $now = now();
            $remainingTimeMinutes = $endTime->greaterThan($now)
                ? $now->diffInMinutes($endTime, false)
                : 0;

            return response()->json([
                'postTask' => $postTask,
                'remainingTimeMinutes' => $remainingTimeMinutes,
            ]);
        }
    }

    public function postedTaskUpdate(Request $request, string $id)
    {
        $postTask = PostTask::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'worker_needed' => 'nullable|numeric|min:0',
            'boosting_time' => 'nullable|numeric|min:0',
            'work_duration' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $task_cost = number_format(($request->worker_needed * $postTask->income_of_each_worker), 2, '.', '');

            $site_charge = number_format(($task_cost * get_default_settings('task_posting_charge_percentage')) / 100, 2, '.', '');

            $boosting_time_charge = number_format(get_default_settings('task_posting_boosting_time_charge') * ($request->boosting_time / 15), 2, '.', '');

            $work_duration_charge = number_format(($request->work_duration * get_default_settings('task_posting_additional_work_duration_charge')), 2, '.', '');

            $total_cost = number_format($task_cost + $site_charge + $boosting_time_charge + $work_duration_charge, 2, '.', '');

            if ($request->user()->deposit_balance < $total_cost) {
                return response()->json([
                    'status' => 401,
                    'error' => 'Insufficient balance. Please deposit first. Your current balance is ' . $request->user()->deposit_balance . ' ' . get_site_settings('site_currency_symbol') . '.'
                ]);
            } else {
                $request->user()->update([
                    'deposit_balance' => $request->user()->deposit_balance - $total_cost,
                ]);

                PostTask::where('id', $id)->update([
                    'worker_needed' => $postTask->worker_needed + $request->worker_needed,
                    'sub_cost' => $postTask->sub_cost + $task_cost,
                    'site_charge' => $postTask->site_charge + $site_charge,
                    'boosting_time' => $request->boosting_time > 0 ? $request->boosting_time : $postTask->boosting_time,
                    'total_boosting_time' => $postTask->total_boosting_time + $request->boosting_time,
                    'boosting_time_charge' => $postTask->boosting_time_charge + $boosting_time_charge,
                    'boosting_start_at' => $request->boosting_time > 0 ? now() : $postTask->boosting_start_at,
                    'work_duration' => $postTask->work_duration + $request->work_duration,
                    'work_duration_charge' => $postTask->work_duration_charge + $work_duration_charge,
                    'total_cost' => $postTask->total_cost + $total_cost,
                ]);

                return response()->json([
                    'status' => 200,
                    'deposit_balance' => number_format($request->user()->deposit_balance, 2, '.', ''),
                ]);
            }
        }
    }

    public function postedTaskCanceled(Request $request, $id)
    {
        $postTask = PostTask::findOrFail($id);
        $user = User::findOrFail($postTask->user_id);

        if ($postTask->status == 'Canceled') {
            $message = $postTask->canceled_by != auth()->user()->id
                ? 'This task is already canceled by the system. You cannot cancel it again. Please check your canceled posted task list.'
                : 'This task is already canceled by you. You cannot cancel it again. Please check your canceled posted task list.';
            return response()->json([
                'status' => 400,
                'deposit_balance' => number_format($user->deposit_balance, 2, '.', ''),
                'error' => $message
            ]);
        }

        if ($postTask->status == 'Completed') {
            return response()->json([
                'status' => 400,
                'deposit_balance' => number_format($user->deposit_balance, 2, '.', ''),
                'error' => 'This task is already completed. You cannot cancel it. Please check your completed posted task list.'
            ]);
        }

        $proofTasksCount = ProofTask::where('post_task_id', $id)->whereIn('status', ['Pending'])->count();
        if ($proofTasksCount > 0) {
            return response()->json([
                'status' => 400,
                'deposit_balance' => number_format($user->deposit_balance, 2, '.', ''),
                'error' => 'You cannot cancel this task because workers have already submitted proof. Please reject or approve all proofs first.'
            ]);
        }

        if ($request->has('check') && $request->check == true) {
            return response()->json([
                'status' => 200,
                'message' => 'Task found. Proceed with cancellation.'
            ]);
        }

        $request->validate([
            'message' => 'required|string',
        ], [
            'message.required' => 'Please enter a valid reason.'
        ]);

        $refundAmount = 0;
        if ($proofTasksCount == 0 && $postTask->status != 'Rejected') {
            $refundAmount = $postTask->total_cost;
        } elseif (in_array($postTask->status, ['Running', 'Paused'])) {
            $refundAmount = number_format(
                ($postTask->sub_cost / $postTask->worker_needed) * ($postTask->worker_needed - $proofTasksCount),
                2,
                '.',
                ''
            );
        }

        if ($refundAmount > 0) {
            $user->deposit_balance += $refundAmount;
            $user->save();
        }

        $postTask->update([
            'status' => 'Canceled',
            'cancellation_reason' => $request->message,
            'canceled_by' => auth()->user()->id,
            'canceled_at' => now(),
        ]);

        return response()->json([
            'status' => 200,
            'deposit_balance' => number_format($user->deposit_balance, 2, '.', ''),
            'success' => 'Task canceled successfully.'
        ]);
    }

    public function postedTaskPaused($id)
    {
        $postTask = PostTask::findOrFail($id);

        $boostingStartAtDiffInMinutes = Carbon::parse($postTask->boosting_start_at)->diffInMinutes(Carbon::now());
        $boostingStartAtDiffRounded = $postTask->boosting_time - round($boostingStartAtDiffInMinutes);

        if ($postTask->status != 'Running') {
            return response()->json([
                'status' => 400,
                'error' => 'Currently, this posted task is ' . $postTask->status . '. So, no need to paused it. Please check your posted ' . $postTask->status . ' task list.'
            ]);
        }

        if ($boostingStartAtDiffRounded > 0) {
            $postTask->boosting_start_at = null;
            $postTask->boosting_time = $boostingStartAtDiffRounded;
        }
        $postTask->paused_at = now();
        $postTask->paused_by = auth()->user()->id;
        $postTask->status = 'Paused';

        $postTask->save();

        return response()->json([
            'status' => 200,
        ]);
    }

    public function postedTaskResume($id)
    {
        $postTask = PostTask::findOrFail($id);

        $boostingStartAtDiffInMinutes = Carbon::parse($postTask->boosting_start_at)->diffInMinutes(Carbon::now());
        $boostingStartAtDiffRounded = $postTask->boosting_time - round($boostingStartAtDiffInMinutes);

        if ($postTask->status != 'Paused') {
            return response()->json([
                'status' => 400,
                'error' => 'Currently, this posted task is ' . $postTask->status . '. So, no need to resume it. Please check your posted ' . $postTask->status . ' task list.'
            ]);
        }

        $postTask->boosting_start_at = $boostingStartAtDiffRounded > 0 ? now() : null;
        $postTask->status = 'Running';
        $postTask->save();

        return response()->json([
            'status' => 200,
        ]);
    }

    // Method to handle find tasks page and filter tasks
    public function proofTaskListClearFilters($id)
    {
        $id = decrypt($id);
        session()->put('clear_filters', true);
        return redirect()->route('proof_task.list', encrypt($id));
    }

    public function proofTaskList(Request $request, $id)
    {
        if ($request->ajax()) {
            $query = ProofTask::where('post_task_id', decrypt($id));

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $query->select('proof_tasks.*');

            $pendingProofTasksCount = (clone $query)->where('status', 'Pending')->count();
            $approvedProofTasksCount = (clone $query)->where('status', 'Approved')->count();
            $rejectedProofTasksCount = (clone $query)->where('status', 'Rejected')->count();
            $reviewedProofTasksCount = (clone $query)->where('status', 'Reviewed')->count();

            $proofTasks = $query->get();

            return DataTables::of($proofTasks)
                ->addColumn('checkbox', function ($row) {
                    $checkPending = $row->status != 'Pending' ? 'disabled' : '';
                    return '<input type="checkbox" class="form-check-input checkbox" value="' . $row->id . '" ' . $checkPending . '>';
                })

                ->editColumn('id', function ($row) {
                    return '<span class="badge bg-primary">' . $row->id . '</span>';
                })
                ->editColumn('user', function ($row) {
                    return '
                        <span class="badge bg-dark">Name: ' . $row->user->name . '</span>
                        <span class="badge bg-dark">Ip: ' . $row->user_ip . '</span>
                    ';
                })
                ->editColumn('proof_answer', function ($row) {
                    return '<span class="badge bg-info mx-2">Answer: </span>' . e(Str::limit($row->proof_answer, 40, '...'));
                })
                ->addColumn('proof_answer_full', function ($row) {
                    return '<span class="badge bg-info my-2">Answer: </span><br>' . nl2br(e($row->proof_answer));
                })
                ->editColumn('status', function ($row) {
                    $statusClasses = [
                        'Pending' => 'info',
                        'Approved' => 'success',
                        'Rejected' => 'danger',
                        'Reviewed' => 'warning',
                    ];
                    $statusClass = $statusClasses[$row->status] ?? 'secondary';
                    return '<span class="badge bg-' . $statusClass . '">' . $row->status . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y h:i A');
                })
                ->editColumn('checking_at', function ($row) {
                    if ($row->status == 'Pending') {
                        $submitDate = Carbon::parse($row->created_at);
                        $endDate = $submitDate->addHours((int) get_default_settings('posted_task_proof_submit_auto_approved_time'));
                        if ($endDate < now()) {
                            return '<span class="badge bg-info">Please contact support</span>';
                        }
                        return '<span class="badge bg-primary">Deadline: ' . $endDate->format('d M Y h:i A') . '</span>';
                    }
                    if ($row->approved_at) {
                        return '<span class="badge bg-success">' . date('d M Y h:i A', strtotime($row->approved_at)) . '</span>';
                    } elseif ($row->rejected_at) {
                        return '<span class="badge bg-danger">' . date('d M Y h:i A', strtotime($row->rejected_at)) . '</span>';
                    } elseif ($row->reviewed_at) {
                        return '<span class="badge bg-warning">' . date('d M Y h:i A', strtotime($row->reviewed_at)) . '</span>';
                    } else {
                        return '<span class="badge bg-info">Please contact support</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $btnName = $row->status == 'Pending' ? 'Check' : 'View';
                    if ($row->status == 'Rejected') {
                        return '
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">' . $btnName . '</button>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs reportProofTaskBtn">Report</button>
                        ';
                    } else {
                        return '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">' . $btnName . '</button>';
                    }
                })
                ->with([
                    'pendingProofTasksCount' => $pendingProofTasksCount,
                    'approvedProofTasksCount' => $approvedProofTasksCount,
                    'rejectedProofTasksCount' => $rejectedProofTasksCount,
                    'reviewedProofTasksCount' => $reviewedProofTasksCount,
                ])
                ->rawColumns(['checkbox', 'id', 'user', 'proof_answer', 'proof_answer_full', 'status', 'created_at', 'checking_at', 'action'])
                ->make(true);
        }

        $postTask = PostTask::findOrFail(decrypt($id));
        $proofSubmitted = ProofTask::where('post_task_id', $postTask->id)->get();
        $pendingProof = ProofTask::where('post_task_id', $postTask->id)->where('status', 'Pending')->count();
        $approvedProof = ProofTask::where('post_task_id', $postTask->id)->where('status', 'Approved')->count();
        $refundProof = ProofTask::where('post_task_id', $postTask->id)->where('status', 'Rejected')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereNull('reviewed_at')
                        ->where('rejected_at', '<=', now()->subHours(get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time')));
                })
                ->orWhereNotNull('reviewed_at');
            })->count();
        $holdProof = ProofTask::where('post_task_id', $postTask->id)
            ->where(function ($query) {
                $query->where('status', 'Reviewed')
                    ->orWhere(function ($query) {
                        $query->where('status', 'Rejected')
                            ->whereNull('reviewed_at')
                            ->where('rejected_at', '>', now()->subHours(get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time')));
                    });
            })->count();

        if (session()->has('clear_filters')) {
            session()->forget('clear_filters');
            $clearFilters = true;
        } else {
            $clearFilters = false;
        }

        return view('frontend.posted_task.all_proof_list', compact('postTask', 'proofSubmitted', 'pendingProof', 'approvedProof', 'refundProof', 'holdProof', 'clearFilters'));
    }

    public function proofTaskApprovedAll($id)
    {
        $proofTasks = ProofTask::where('post_task_id', $id)->where('status', 'Pending')->get();

        $postTask = PostTask::findOrFail($id);

        foreach ($proofTasks->pluck('user_id') as $user_id) {
            $user = User::findOrFail($user_id);
            $user->withdraw_balance = $user->withdraw_balance + $postTask->income_of_each_worker;
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
            $user->withdraw_balance = $user->withdraw_balance + $postTask->income_of_each_worker;
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
        $rating = Rating::where('user_id', $proofTask->user_id)->where('post_task_id', $proofTask->post_task_id)->first();
        $bonus = Bonus::where('user_id', $proofTask->user_id)->where('post_task_id', $proofTask->post_task_id)->first();
        return view('frontend.posted_task.proof_check', compact('proofTask', 'rating', 'bonus'));
    }

    public function proofTaskAllPendingCheck($id)
    {
        $proofTaskListPending = ProofTask::where('post_task_id', $id)->where('status', 'Pending')->with('user')->get();

        $responseData = $proofTaskListPending->map(function ($task) {
            $task->formatted_created_at = Carbon::parse($task->created_at)->format('d M, Y h:i A');
            return $task;
        });

        return response()->json([
            'status' => 200,
            'proofTaskListPending' => $responseData,
        ]);
    }

    public function proofTaskCheckUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Approved,Rejected',
            'bonus' => 'nullable|numeric|min:0|max:' . get_default_settings('posted_task_proof_submit_user_max_bonus_amount'),
            'rating' => 'nullable|numeric|min:0|max:5',
            'rejected_reason' => 'required_if:status,Rejected',
            'rejected_reason_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either Approved or Rejected.',
            'rejected_reason.required_if' => 'Rejected reason is required when status is Rejected.',
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

            $autoApprovalTime = get_default_settings('posted_task_proof_submit_auto_approved_time');
            if ($proofTask->created_at < now()->subHours($autoApprovalTime)) {
                return response()->json([
                    'status' => 401,
                    'error' => 'This task proof checking time has expired and has been automatically approved by the system.',
                ]);
            }

            if ($proofTask->status != 'Pending') {
                return response()->json([
                    'status' => 401,
                    'error' => "This proof task is already {$proofTask->status}. You cannot resubmit it.",
                ]);
            }

            if ($request->status === 'Approved') {
                $totalIncome = $postTask->income_of_each_worker + ($request->bonus ?? 0);

                if ($request->bonus) {
                    $authUser = auth()->user();
                    if ($request->bonus <= $authUser->deposit_balance) {
                        $authUser->deposit_balance -= $request->bonus;
                    } elseif ($request->bonus <= $authUser->withdraw_balance) {
                        $authUser->withdraw_balance -= $request->bonus;
                    } else {
                        return response()->json([
                            'status' => 402,
                            'error' => 'Insufficient balance. Please deposit funds. Your current deposit balance is ' .
                                get_site_settings('site_currency_symbol') . ' ' . number_format($authUser->deposit_balance, 2) .
                                ' and withdraw balance is ' . get_site_settings('site_currency_symbol') . ' ' .
                                number_format($authUser->withdraw_balance, 2) . '.',
                        ]);
                    }
                    $authUser->save();

                    $userBonus = Bonus::create([
                        'user_id' => $proofTask->user_id,
                        'bonus_by' => $authUser->id,
                        'type' => 'Proof Task Approved Bonus',
                        'post_task_id' => $postTask->id,
                        'amount' => $request->bonus,
                    ]);

                    $user->notify(new BonusNotification($userBonus));
                }

                $user->withdraw_balance += $totalIncome;
                $user->save();

                if ($request->rating) {
                    $rating = Rating::create([
                        'user_id' => $proofTask->user_id,
                        'rated_by' => auth()->user()->id,
                        'post_task_id' => $postTask->id,
                        'rating' => $request->rating,
                    ]);
                    $user->notify(new RatingNotification($rating));
                }
            }

            $rejected_reason_photo_name = null;
            if ($request->file('rejected_reason_photo')) {
                $manager = new ImageManager(new Driver());
                $rejected_reason_photo_name = $id."-rejected_reason_photo-".date('YmdHis').".".$request->file('rejected_reason_photo')->getClientOriginalExtension();
                $image = $manager->read($request->file('rejected_reason_photo'));
                $image->toJpeg(80)->save(base_path("public/uploads/task_proof_rejected_reason_photo/").$rejected_reason_photo_name);
            }

            $proofTask->update([
                'status' => $request->status,
                'rejected_reason' => $request->rejected_reason ?? null,
                'rejected_reason_photo' => $rejected_reason_photo_name,
                'rejected_at' => $request->status === 'Rejected' ? now() : null,
                'rejected_by' => $request->status === 'Rejected' ? auth()->user()->id : null,
                'approved_at' => $request->status === 'Approved' ? now() : null,
                'approved_by' => $request->status === 'Approved' ? auth()->user()->id : null,
            ]);

            return response()->json([
                'status' => 200,
                'deposit_balance' => number_format(auth()->user()->deposit_balance, 2, '.', ''),
                'withdraw_balance' => number_format(auth()->user()->withdraw_balance, 2, '.', ''),
            ]);
        }
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
                'id' => $reportStatus->id,
                'status' => $reportStatus->status,
                'reason' => $reportStatus->reason,
                'created_at' => $reportStatus->created_at->format('d M, Y h:i A'),
                'photo' => $reportStatus->photo,
            ];
        } else {
            $formattedReportStatus = null;
        }

        return response()->json([
            'status' => 200,
            'reportStatus' => $formattedReportStatus,
            'proofTask' => $proofTask,
        ]);
    }
}
