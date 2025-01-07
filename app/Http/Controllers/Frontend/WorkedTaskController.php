<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PostTask;
use App\Models\ProofTask;
use App\Models\Block;
use App\Models\Report;
use App\Models\NotInterestedTask;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WorkedTaskController extends Controller
{
    // Method to handle find tasks page and filter tasks
    public function findTasksClearFilters()
    {
        // Set session to clear filters on the next request
        session()->put('clear_filters', true);
        return redirect()->route('find_tasks');
    }

    public function findTasks(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $proofTaskIds = ProofTask::where('user_id', Auth::id())->pluck('post_task_id')->toArray();
                $blockedUserIds = Block::where('blocked_by', Auth::id())->pluck('user_id')->toArray();
                $notInterestedTaskIds = NotInterestedTask::where('user_id', Auth::id())->pluck('post_task_id')->toArray();
                $query = PostTask::where('status', 'Running')
                    ->whereNot('user_id', Auth::id())
                    ->whereNotIn('id', $proofTaskIds)
                    ->whereNotIn('user_id', $blockedUserIds)
                    ->whereNotIn('id', $notInterestedTaskIds);

                if ($request->category_id) {
                    $query->where('post_tasks.category_id', $request->category_id);
                }
                if ($request->sort_by) {
                    if ($request->sort_by == 'low_to_high') {
                        $query->orderBy('income_of_each_worker', 'asc');
                    } else if ($request->sort_by == 'high_to_low') {
                        $query->orderBy('income_of_each_worker', 'desc');
                    } else if ($request->sort_by == 'latest') {
                        $query->orderBy('approved_at', 'desc');
                    } else if ($request->sort_by == 'oldest') {
                        $query->orderBy('approved_at', 'asc');
                    }
                }

                $findTasks = $query->orderByRaw("
                    CASE
                        WHEN NOW() <= DATE_ADD(post_tasks.boosting_start_at, INTERVAL post_tasks.boosting_time MINUTE)
                        THEN 0
                        ELSE 1
                    END
                ");

                // Total filtered count
                $totalTasksCount = $findTasks->count();

                // $findTasks = $query->orderBy('approved_at', 'desc')->get();
                $findTasks = $findTasks->orderBy('approved_at', 'desc')->get();

                return DataTables::of($findTasks)
                    ->addIndexColumn()
                    ->editColumn('category_name', function ($row) {
                        return '<span class="badge bg-primary">'.$row->category->name.'</span>';
                    })
                    ->editColumn('title', function ($row) {
                        return '
                            <a href="'.route('find_task.details', encrypt($row->id)).'" title="'.$row->title.'" class="text-success">
                                '.$row->title.'
                            </a>
                        ';
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
                    ->editColumn('income_of_each_worker', function ($row) {
                        return '<span class="badge bg-success">'.get_site_settings('site_currency_symbol') . ' ' . $row->income_of_each_worker.'</span>';
                    })
                    ->editColumn('approved_at', function ($row) {
                        return '<span class="badge bg-dark">'.date('d M Y h:i A', strtotime($row->approved_at)).'</span>';
                    })
                    ->editColumn('work_duration_deadline', function ($row) {
                        $approvedDate = Carbon::parse($row->approved_at);
                        $endDate = $approvedDate->addDays((int) $row->work_duration);
                        return '<span class="badge bg-warning text-dark">' . $endDate->format('d M, Y h:i:s A') . '</span>';
                    })
                    ->editColumn('action', function ($row) {
                        $action = '
                        <a href="'.route('find_task.details', encrypt($row->id)).'" target="_blank" title="View" class="btn btn-info btn-sm">
                            View
                        </a>
                        ';
                        return $action;
                    })
                    ->with(['totalTasksCount' => $totalTasksCount])
                    ->rawColumns(['category_name', 'title', 'worker_needed', 'income_of_each_worker', 'approved_at', 'work_duration_deadline', 'action'])
                    ->make(true);
            }

            // Clear filters if requested
            if (session()->has('clear_filters')) {
                session()->forget('clear_filters');
                $clearFilters = true;
            } else {
                $clearFilters = false;
            }

            // Return categories for filter
            $categories = PostTask::where('status', 'Running')->groupBy('category_id')->select('category_id')->with('category')->get();
            return view('frontend.find_tasks.index', compact('categories', 'clearFilters'));
        }
    }

    public function findTaskDetails($id)
    {
        $id = decrypt($id);
        $taskDetails = PostTask::findOrFail($id);
        $taskProofExists = ProofTask::where('post_task_id', $id)->where('user_id', Auth::id())->exists();
        $taskProof = ProofTask::where('post_task_id', $id)->where('user_id', Auth::id())->first();
        $proofCount = ProofTask::where('post_task_id', $id)->count();
        $blocked = Block::where('user_id', $taskDetails->user_id)->where('blocked_by', Auth::id())->exists();
        $reportPostTask = Report::where('post_task_id', $id)->where('reported_by', Auth::id())->first();
        $reportUserCount = Report::where('user_id', $taskDetails->user_id)->where('reported_by', Auth::id())->where('type', 'User')->count();
        $reviewDetails = Rating::where('user_id', $taskDetails->user_id)->get();
        $totalPostedTask = PostTask::where('user_id', $taskDetails->user_id)->count();
        $totalWorkedTask = ProofTask::where('user_id', $taskDetails->user_id)->count();

        $postedTaskIds = PostTask::where('user_id', $taskDetails->user_id)->pluck('id')->toArray();
        $totalPostedTaskProofCount = ProofTask::whereIn('post_task_id', $postedTaskIds)->count();
        $totalPostedTaskProofApprovedCount = ProofTask::whereIn('post_task_id', $postedTaskIds)->where('status', 'Approved')->count();
        $totalPostedTaskProofApproved = $totalPostedTaskProofCount > 0 ? round(($totalPostedTaskProofApprovedCount / $totalPostedTaskProofCount) * 100, 2) : 0;
        $totalPostedTaskProofRejectedCount = ProofTask::whereIn('post_task_id', $postedTaskIds)->where('status', 'Rejected')->count();
        $totalPostedTaskProofRejected = $totalPostedTaskProofCount > 0 ? round(($totalPostedTaskProofRejectedCount / $totalPostedTaskProofCount) * 100, 2) : 0;

        $totalWorkedTaskProofSubmitCount = ProofTask::where('user_id', $taskDetails->user_id)->count();
        $totalWorkedTaskProofApprovedCount = ProofTask::where('user_id', $taskDetails->user_id)->where('status', 'Approved')->count();
        $totalWorkedTaskProofApproved = $totalWorkedTaskProofSubmitCount > 0 ? round(($totalWorkedTaskProofApprovedCount / $totalWorkedTaskProofSubmitCount) * 100, 2) : 0;
        $totalWorkedTaskProofRejectedCount = ProofTask::where('user_id', $taskDetails->user_id)->where('status', 'Rejected')->count();
        $totalWorkedTaskProofRejected = $totalWorkedTaskProofSubmitCount > 0 ? round(($totalWorkedTaskProofRejectedCount / $totalWorkedTaskProofSubmitCount) * 100, 2) : 0;

        return view('frontend.find_tasks.view', compact('taskDetails', 'taskProofExists', 'taskProof', 'proofCount', 'blocked', 'reportPostTask', 'reportUserCount', 'reviewDetails', 'totalPostedTask', 'totalWorkedTask', 'totalPostedTaskProofCount', 'totalPostedTaskProofApproved', 'totalPostedTaskProofRejected', 'totalWorkedTaskProofApproved', 'totalWorkedTaskProofRejected'));
    }

    public function findTaskNotInterested($id)
    {
        $id = decrypt($id);

        NotInterestedTask::create([
            'post_task_id' => $id,
            'user_id' => Auth::id(),
        ]);

        $notification = array(
            'message' => 'This task is not interested in submitting proof.',
            'alert-type' => 'success'
        );

        return redirect()->route('find_tasks.clear.filters')->with($notification);
    }

    public function findTaskProofSubmitValidCheck($id)
    {
        $id = decrypt($id);
        $postTask =  PostTask::findOrFail($id);
        $ProofTask = ProofTask::where('post_task_id', $id)->count();

        $userExists = ProofTask::where('post_task_id', $id)->where('user_id', Auth::id())->exists();
        if ($userExists) {
            return response()->json([
                'canSubmit' => false,
                'message' => 'Sorry, you have already submitted proof for this task.'
            ]);
        }

        if ($postTask->status != 'Running') {
            return response()->json([
                'canSubmit' => false,
                'message' => 'Sorry, Currently, this task is ' . $postTask->status . ' now. So you can not submit proof for this task.'
            ]);
        }

        if ($ProofTask >= $postTask->worker_needed) {
            return response()->json([
                'canSubmit' => false,
                'message' => 'Sorry, the required number of work have already submitted proof for this task.'
            ]);
        }

        $approvedDate = Carbon::parse($postTask->approved_at);
        $endDate = $approvedDate->addDays((int) $postTask->work_duration);
        if ($endDate < now()) {
            return response()->json([
                'canSubmit' => false,
                'message' => 'Sorry, the deadline for submitting proof for this task has expired.'
            ]);
        }

        return response()->json(['canSubmit' => true]);
    }

    public function findTaskProofSubmit(Request $request, $id)
    {
        $id = decrypt($id);
        $taskDetails = PostTask::findOrFail($id);

        $rules = [
            'proof_answer' => 'required|string|max:5000',
            'proof_photos' => $taskDetails->required_proof_photo > 0
                ? 'required|array|min:' . $taskDetails->required_proof_photo
                : 'nullable|array', // Set to nullable if no photos are required
            'proof_photos.*' => 'image|mimes:jpg,jpeg,png|max:2048', // Each photo must be an image if provided
        ];

        $messages = [
            'proof_answer.required' => 'The proof answer is required.',
            'proof_answer.string' => 'The proof answer must be a string.',
            'proof_answer.max' => 'The proof answer may not be greater than 5000 characters.',
            'proof_photos.required' => 'You must upload all required proof photos.',
            'proof_photos.array' => 'The proof photos must be an array.',
            'proof_photos.min' => 'You must upload at least ' . $taskDetails->required_proof_photo . ' proof photos.',
            'proof_photos.*.image' => 'Each proof photo must be an image.',
            'proof_photos.*.mimes' => 'Each proof photo must be a file of type: jpg, jpeg, png.',
            'proof_photos.*.max' => 'Each proof photo may not be greater than 2MB.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $proofCount = ProofTask::where('post_task_id', $id)->count();

        $userExists = ProofTask::where('post_task_id', $id)->where('user_id', Auth::id())->exists();
        if ($userExists) {
            $notification = array(
                'message' => 'Sorry, you have already submitted proof for this task.',
                'alert-type' => 'error'
            );

            return back()->with($notification)->withInput();
        }
        
        if ($taskDetails->status != 'Running') {
            $notification = array(
                'message' => 'Sorry, this task is ' . $taskDetails->status . ' now. You can not submit proof for this task.',
                'alert-type' => 'error'
            );

            return back()->with($notification)->withInput();
        }

        if ($proofCount >= $taskDetails->worker_needed) {
            $notification = array(
                'message' => 'Sorry, the required number of work have already submitted proof for this task.',
                'alert-type' => 'error'
            );

            return back()->with($notification)->withInput();
        }

        $approvedDate = Carbon::parse($taskDetails->approved_at);
        $endDate = $approvedDate->addDays((int) $taskDetails->work_duration);

        if ($endDate < now()) {
            $notification = array(
                'message' => 'Sorry, the deadline for submitting proof for this task has expired.',
                'alert-type' => 'error'
            );

            return back()->with($notification)->withInput();
        }

        $proof_photos = [];
        $manager = new ImageManager(new Driver());
        if ($request->hasFile('proof_photos')) {
            foreach ($request->file('proof_photos') as $key => $photo) {
                $proof_photo_name = $id . "-" . $request->user()->id . "-proof_photo-".($key+1).".". $photo->getClientOriginalExtension();
                $image = $manager->read($photo);
                $image->toJpeg(80)->save(base_path("public/uploads/task_proof_photo/").$proof_photo_name);
                $proof_photos[] = $proof_photo_name;
            }
        }

        $userIp = $request->ip();

        ProofTask::create([
            'post_task_id' => $id,
            'user_id' => $request->user()->id,
            'user_ip' => $userIp,
            'proof_answer' => $request->proof_answer,
            'proof_photos' => json_encode($proof_photos),
            'status' => 'Pending',
        ]);

        $notification = array(
            'message' => 'Task proof submitted successfully.',
            'alert-type' => 'success'
        );

        return redirect()->route('find_tasks')->with($notification);
    }

    public function workedTaskListPending(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $proofTasks = ProofTask::where('user_id', Auth::id())->where('status', 'Pending');

                $query = $proofTasks->select('proof_tasks.*')->with('postTask');

                if ($request->filter_date){
                    $query->whereDate('proof_tasks.created_at', $request->filter_date);
                }

                $query->whereDate('proof_tasks.created_at', '>', now()->subDays(7));

                // Total filtered count
                $totalProofsCount = $query->count();

                $taskList = $query->get();

                return DataTables::of($taskList)
                    ->addIndexColumn()
                    ->editColumn('proof_id', function ($row) {
                        return '<span class="badge bg-primary">'.$row->postTask->id.'-'.$row->id.'</span>';
                    })
                    ->editColumn('title', function ($row) {
                        return '
                            <a href="'.route('find_task.details', encrypt($row->post_task_id)).'" title="'.$row->postTask->title.'" class="text-info">
                                '.$row->postTask->title.'
                            </a>
                        ';
                    })
                    ->editColumn('income_of_each_worker', function ($row) {
                        return get_site_settings('site_currency_symbol') . ' ' . $row->postTask->income_of_each_worker;
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->with(['totalProofsCount' => $totalProofsCount])
                    ->rawColumns(['proof_id', 'title'])
                    ->make(true);
            }
            return view('frontend.worked_task.pending');
        }
    }

    public function workedTaskListApproved(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $proofTasks = ProofTask::where('user_id', Auth::id())->where('status', 'Approved');
                $query = $proofTasks->select('proof_tasks.*')->with('postTask');

                if ($request->filter_date){
                    $query->whereDate('proof_tasks.approved_at', $request->filter_date);
                }

                $query->whereDate('proof_tasks.approved_at', '>', now()->subDays(7));

                // Total filtered count
                $totalProofsCount = $query->count();

                $taskList = $query->get();

                return DataTables::of($taskList)
                    ->addIndexColumn()
                    ->editColumn('title', function ($row) {
                        return '
                            <a href="'.route('find_task.details', encrypt($row->post_task_id)).'" title="'.$row->postTask->title.'" class="text-info">
                                '.$row->postTask->title.'
                            </a>
                        ';
                    })
                    ->editColumn('income_of_each_worker', function ($row) {
                        return  get_site_settings('site_currency_symbol') . ' ' . $row->postTask->income_of_each_worker;
                    })
                    ->editColumn('rating', function ($row) {
                        $rating = Rating::where('user_id', Auth::id())->where('post_task_id', $row->post_task_id)->first();
                        return $rating ? $row->rating->rating . ' <i class="fa-solid fa-star text-warning"></i>' : 'Not Rated';
                    })
                    ->editColumn('bonus', function ($row) {
                        $bonus = Rating::where('user_id', Auth::id())->where('post_task_id', $row->post_task_id)->first();
                        return $bonus ? get_site_settings('site_currency_symbol') . ' ' . $row->bonus->amount : 'No Bonus';
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->editColumn('approved_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->approved_at));
                    })
                    ->editColumn('approved_by', function ($row) {
                        if ($row->approvedBy->user_type =='Backend') {
                            return '<span class="badge bg-primary">Admin</span>';
                        } else {
                            return '<span class="badge bg-info">'. $row->approvedBy->name .'</span>';
                        }
                    })
                    ->addColumn('action', function ($row) {
                        $action = '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">View</button>
                        ';
                        return $action;
                    })
                    ->with(['totalProofsCount' => $totalProofsCount])
                    ->rawColumns(['title', 'rating', 'bonus', 'approved_by', 'action'])
                    ->make(true);
            }
            return view('frontend.worked_task.approved');
        }
    }

    public function workedTaskViewApproved($id)
    {
        $proofTask = ProofTask::findOrFail($id);
        $postTask = PostTask::findOrFail($proofTask->post_task_id);
        return view('frontend.worked_task.approved_view', compact('proofTask', 'postTask'));
    }

    public function workedTaskListRejected(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $proofTasks = ProofTask::where('user_id', Auth::id())->where('status', 'Rejected');
                $query = $proofTasks->select('proof_tasks.*');

                if ($request->filter_date){
                    $query->whereDate('proof_tasks.rejected_at', $request->filter_date);
                }

                $query->whereDate('proof_tasks.rejected_at', '>', now()->subDays(7));

                // Total filtered count
                $totalProofsCount = $query->count();

                $taskList = $query->get();

                return DataTables::of($taskList)
                    ->addIndexColumn()
                    ->editColumn('title', function ($row) {
                        return '
                            <a href="'.route('find_task.details', encrypt($row->post_task_id)).'" title="'.$row->postTask->title.'" class="text-info">
                                '.$row->postTask->title.'
                            </a>
                        ';
                    })
                    ->editColumn('income_of_each_worker', function ($row) {
                        return  get_site_settings('site_currency_symbol') . ' ' . $row->postTask->income_of_each_worker;
                    })
                    ->editColumn('rejected_reason', function ($row) {
                        $rejected_reason = Str::limit($row->rejected_reason,40, '...');
                        return e($rejected_reason);
                    })
                    ->addColumn('rejected_reason_full', function ($row) {
                        $rejected_reason = nl2br(e($row->rejected_reason));
                        return '<span class="badge bg-info my-2">Reason: </span><br>' . $rejected_reason;
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->editColumn('rejected_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->rejected_at));
                    })
                    ->editColumn('rejected_by', function ($row) {
                        if ($row->rejectedBy->user_type =='Backend') {
                            return '<span class="badge bg-primary">Admin</span>';
                        } else {
                            return '<span class="badge bg-info">'. $row->rejectedBy->name .'</span>';
                        }
                    })
                    ->editColumn('review_send_expired', function ($row) {
                        $rejectedDate = Carbon::parse($row->rejected_at);
                        $endDate = $rejectedDate->addHours((int) get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time'));
                        if ($row->reviewed_at != null) {
                            return '<span class="badge bg-success">Already Reviewed</span>';
                        } else if ($endDate < now()) {
                            return '<span class="badge bg-danger">Expired</span>';
                        }
                        return '<span class="badge bg-primary">' . $endDate->format('d M, Y h:i:s A') . '</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $action = '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">Proof Check</button>
                        <button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs reviewedBtn">Reviewed Check</button>
                        ';
                        return $action;
                    })
                    ->with(['totalProofsCount' => $totalProofsCount])
                    ->rawColumns(['title', 'income_of_each_worker', 'rejected_reason', 'rejected_reason_full', 'rejected_by', 'review_send_expired', 'action'])
                    ->make(true);
            }
            return view('frontend.worked_task.rejected');
        }
    }

    public function workedTaskCheckRejected($id)
    {
        $proofTask = ProofTask::findOrFail($id);
        $postTask = PostTask::findOrFail($proofTask->post_task_id);
        return view('frontend.worked_task.rejected_check', compact('proofTask' , 'postTask'));
    }

    public function workedTaskCheckReviewed($id)
    {
        $proofTask = ProofTask::findOrFail($id);
        $postTask = PostTask::findOrFail($proofTask->post_task_id);
        return view('frontend.worked_task.reviewed_check', compact('proofTask' , 'postTask'));
    }

    public function workedTaskReviewedSend(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reviewed_reason' => 'required',
            'reviewed_reason_photos.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $formattedErrors = [];

            foreach ($errors as $key => $messages) {
                if (strpos($key, 'reviewed_reason_photos.') !== false) {
                    $index = explode('.', $key)[1]; // Extract the index
                    $formattedMessage = preg_replace('/reviewed_reason_photos\.\d+/', 'reviewed_reason_photos', $messages[0]);
                    $formattedErrors["reviewed_reason_photos_$index"] = $formattedMessage; // Use the corrected message
                } else {
                    $formattedErrors[$key] = $messages[0];
                }
            }

            return response()->json(['status' => 400, 'error' => $formattedErrors]);
        }

        $proofTask = ProofTask::findOrFail($id);

        if ($proofTask->status == 'Reviewed') {
            return response()->json([
                'status' => 401,
                'error' => 'This proof has already been reviewed.'
            ]);
        }

        // Deduct balance logic
        if ($request->user()->withdraw_balance < get_default_settings('rejected_worked_task_review_charge')) {
            return response()->json([
                'status' => 401,
                'error' => 'Insufficient balance in your account to review the proof.'
            ]);
        }

        User::where('id', Auth::id())->update([
            'withdraw_balance' => $request->user()->withdraw_balance - get_default_settings('rejected_worked_task_review_charge'),
        ]);

        $reviewedPhotos = [];
        $manager = new ImageManager(new Driver());
        if ($request->hasFile('reviewed_reason_photos')) {
            foreach ($request->file('reviewed_reason_photos') as $key => $reviewed_reason_photo) {
                $reviewed_reason_photo_name = $id . "-" . $request->user()->id . "-proof_photo-".($key+1).".". $reviewed_reason_photo->getClientOriginalExtension();
                $image = $manager->read($reviewed_reason_photo);
                $image->toJpeg(80)->save(base_path("public/uploads/task_proof_reviewed_reason_photo/").$reviewed_reason_photo_name);
                $reviewedPhotos[] = $reviewed_reason_photo_name;
            }
        }

        $proofTask->update([
            'status' => 'Reviewed',
            'reviewed_reason' => $request->reviewed_reason,
            'reviewed_charge' => get_default_settings('rejected_worked_task_review_charge'),
            'reviewed_reason_photos' => json_encode($reviewedPhotos),
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'status' => 200,
        ]);
    }

    public function workedTaskListReviewed(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $proofTasks = ProofTask::where('user_id', Auth::id())->where('status', 'Reviewed');
                $query = $proofTasks->select('proof_tasks.*');

                if ($request->filter_date){
                    $query->whereDate('proof_tasks.reviewed_at', $request->filter_date);
                }

                $query->whereDate('proof_tasks.reviewed_at', '>', now()->subDays(7));

                // Total filtered count
                $totalProofsCount = $query->count();

                $taskList = $query->get();

                return DataTables::of($taskList)
                    ->addIndexColumn()
                    ->editColumn('proof_id', function ($row) {
                        return '<span class="badge bg-primary">'.$row->postTask->id.'-'.$row->id.'</span>';
                    })
                    ->editColumn('title', function ($row) {
                        return '
                            <a href="'.route('find_task.details', encrypt($row->post_task_id)).'" title="'.$row->postTask->title.'" class="text-info">
                                '.$row->postTask->title.'
                            </a>
                        ';
                    })
                    ->editColumn('income_of_each_worker', function ($row) {
                        return  get_site_settings('site_currency_symbol') . ' ' . $row->postTask->income_of_each_worker;
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->editColumn('rejected_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->rejected_at));
                    })
                    ->editColumn('reviewed_reason', function ($row) {
                        $reviewed_reason = Str::limit($row->reviewed_reason,40, '...');
                        return e($reviewed_reason);
                    })
                    ->addColumn('reviewed_reason_full', function ($row) {
                        $reviewed_reason = nl2br(e($row->reviewed_reason));
                        return '<span class="badge bg-info my-2">Reason: </span><br>' . $reviewed_reason;
                    })
                    ->editColumn('reviewed_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->reviewed_at));
                    })
                    ->editColumn('checking_expired_time', function ($row) {
                        $submitDate = Carbon::parse($row->reviewed_at);
                        $endDate = $submitDate->addHours((int) get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time'));
                        if ($endDate < now()) {
                            return '<span class="badge bg-danger">Please contact support</span>';
                        }
                        return '<span class="badge bg-primary">' . $endDate->format('d M, Y h:i:s A') . '</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $action = '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-success btn-xs viewBtn">Proof Check</button>
                        <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs reviewedBtn">Reviewed Check</button>
                        ';
                        return $action;
                    })
                    ->with(['totalProofsCount' => $totalProofsCount])
                    ->rawColumns(['proof_id', 'title', 'income_of_each_worker', 'created_at', 'rejected_at', 'reviewed_reason', 'reviewed_reason_full', 'reviewed_at', 'checking_expired_time', 'action'])
                    ->make(true);
            }
            return view('frontend.worked_task.reviewed');
        }
    }
}
