<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Bonus;
use App\Models\Deposit;
use App\Models\User;
use App\Models\Verification;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Location\Facades\Location;
use App\Models\Report;
use App\Models\ReportReply;
use App\Models\Support;
use App\Models\PostTask;
use App\Models\ProofTask;
use App\Models\UserStatus;
use App\Models\UserDetail;
use App\Models\Rating;
use App\Events\SupportEvent;
use Carbon\Carbon;

class UserController extends Controller
{
    public function dashboard()
    {
        // Monthly Withdraw
        $monthlyWithdraw = [];
        $currentMonth = Carbon::now()->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $month = $currentMonth->copy()->subMonths($i);
            $formattedMonth = $month->format('M-y');

            $amount = Withdraw::where('user_id', Auth::id())
                ->whereMonth('created_at', $month->format('m'))
                ->whereYear('created_at', $month->format('Y'))
                ->sum('amount');

            $monthlyWithdraw[$formattedMonth] = number_format($amount, 2, '.', '');
        }
        $monthlyWithdraw = array_reverse($monthlyWithdraw, true);

        // Monthly Deposite
        $monthlyDeposite = [];
        $currentMonth = Carbon::now()->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $month = $currentMonth->copy()->subMonths($i);
            $formattedMonth = $month->format('M-y');

            $amount = Deposit::where('user_id', Auth::id())
                ->whereMonth('created_at', $month->format('m'))
                ->whereYear('created_at', $month->format('Y'))
                ->sum('amount');

            $monthlyDeposite[$formattedMonth] = number_format($amount, 2, '.', '');
        }
        $monthlyDeposite = array_reverse($monthlyDeposite, true);

        // totalTaskProofSubmitChartjsLineData
        $totalTaskProofSubmitChartjsLineFixedStatuses = ['Pending', 'Approved', 'Rejected', 'Reviewed'];
        $totalTaskProofSubmitChartjsLineYears = ProofTask::whereYear('created_at', '<=', date('Y'))->whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->pluck('created_at')->map(fn($date) => Carbon::parse($date)->format('Y'))->unique()->sort()->values()->toArray();
        $totalTaskProofSubmitChartjsLineData = [
            'labels' => $totalTaskProofSubmitChartjsLineYears,
            'datasets' => []
        ];
        foreach ($totalTaskProofSubmitChartjsLineFixedStatuses as $status) {
            $data = [];
            foreach ($totalTaskProofSubmitChartjsLineYears as $year) {
                $count = ProofTask::whereYear('created_at', $year)->whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->where('status', $status)->count();
                $data[] = $count;
            }
            $totalTaskProofSubmitChartjsLineData['datasets'][] = [
                'label' => $status,
                'data' => $data
            ];
        }

        // totalWorkedTaskApexLineData
        $totalWorkedTaskApexLineFixedStatuses = ['Pending', 'Approved', 'Rejected', 'Reviewed'];
        $totalWorkedTaskApexLineYears = ProofTask::whereYear('created_at', '<=', date('Y'))->where('user_id', Auth::id())->pluck('created_at')->map(fn($date) => Carbon::parse($date)->format('Y'))->unique()->sort()->values()->toArray();
        $totalWorkedTaskApexLineData = [
            'categories' => $totalWorkedTaskApexLineYears,
            'series' => []
        ];
        foreach ($totalWorkedTaskApexLineFixedStatuses as $status) {
            $data = [];
            foreach ($totalWorkedTaskApexLineYears as $year) {
                $count = ProofTask::whereYear('created_at', $year)->where('user_id', Auth::id())->where('status', $status)->count();
                $data[] = $count;
            }
            $totalWorkedTaskApexLineData['series'][] = [
                'name' => $status,
                'data' => $data
            ];
        }

        $postTaskChargeWaiting = 0;

        $userStatus = UserStatus::where('user_id', Auth::id())->latest()->first();

        return $postTaskIds = PostTask::where('user_id', Auth::id())
        ->where(function ($query) {
            $query->where('status', 'Canceled')
                  ->orWhereNotIn('status', ['Pending', 'Rejected']);
        })
        ->pluck('id')
        ->toArray();

// Initialize an array to store valid PostTask IDs
$validPostTaskIds = [];

// Loop through each postTaskId and check the ProofTask count
foreach ($postTaskIds as $postTaskId) {
    // Check the count of ProofTask related to this PostTask ID
    $proofCount = ProofTask::where('post_task_id', $postTaskId)->count();

    // Only add postTaskId to validPostTaskIds if proofCount is greater than 0
    if ($proofCount > 0) {
    return $validPostTaskIds[] = $postTaskId;
    }
}



        $postTaskChargeTotal = PostTask::where('user_id', Auth::id())->whereNot('status', 'Canceled')->sum('total_charge');

        $postTaskRunningIds = PostTask::where('user_id', Auth::id())->whereIn('status', ['Running', 'Paused'])->pluck('id')->toArray();
        foreach ($postTaskRunningIds as $postTaskRunningId) {
            $postTaskRunning = PostTask::find($postTaskRunningId);

            if (!$postTaskRunning) {
                continue; // Skip if no PostTask found
            }

            $chargePerTask = $postTaskRunning->charge / $postTaskRunning->worker_needed;

            // Calculate counts and charges
            $proofCount = ProofTask::where('post_task_id', $postTaskRunningId)->count();
            $postTaskChargeWaiting += $chargePerTask * ($postTaskRunning->worker_needed - $proofCount) ?? 0;
        }

        $postTaskChargeCanceled = 0;
        $postTaskChargePending = 0;
        $postTaskChargeWorkerPayment = 0;
        $postTaskChargeSitePayment = 0;
        $postTaskChargeRefund = 0;
        $postTaskChargeHold = 0;

        $postTaskIds = PostTask::where('user_id', Auth::id())->pluck('id')->toArray();
        foreach ($postTaskIds as $postTaskId) {
            $postTask = PostTask::find($postTaskId);

            if (!$postTask) {
                continue; // Skip if no PostTask found
            }

            $chargePerTask = $postTask->charge / $postTask->worker_needed;

            // Calculate counts and charges
            $pendingCount = ProofTask::where('post_task_id', $postTaskId)->where('status', 'Pending')->count();
            $approvedCount = ProofTask::where('post_task_id', $postTaskId)->where('status', 'Approved')->count();
            $rejectedCount = ProofTask::where('post_task_id', $postTaskId)->where('status', 'Rejected')
                ->where(function ($query) {
                    $query->whereNull('reviewed_at')->where('rejected_at', '<=', now()->subHours(72))
                        ->orWhereNotNull('reviewed_at');
                })->count();
            $reviewedCount = ProofTask::where('post_task_id', $postTaskId)
                ->where(function ($query) {
                    $query->where('status', 'Reviewed')
                        ->orWhere('status', 'Rejected')->whereNull('reviewed_at')
                        ->where('rejected_at', '>', now()->subHours(72));
                })->count();

            $postTaskChargePending += $chargePerTask * $pendingCount ?? 0;
            $postTaskChargeWorkerPayment += $postTask->working_charge * $approvedCount ?? 0;
            $postTaskChargeSitePayment += $postTask->site_charge / $postTask->worker_needed * $approvedCount + $postTask->required_proof_photo_charge + $postTask->boosting_time_charge +  $postTask->work_duration_charge ?? 0;

            $postTaskChargeRefund += $chargePerTask * $rejectedCount ?? 0;
            $postTaskChargeHold += $chargePerTask * $reviewedCount ?? 0;
        }

        return view('frontend/dashboard', [
            // Posted Task .............................................................................................................
            // Today Status
            'today_pending_posted_task' => PostTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Pending')->count(),
            'today_running_posted_task' => PostTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Running')->count(),
            'today_rejected_posted_task' => PostTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Rejected')->count(),
            'today_canceled_posted_task' => PostTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Canceled')->count(),
            'today_paused_posted_task' => PostTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Paused')->count(),
            'today_completed_posted_task' => PostTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Completed')->count(),
            // Monthly Status
            'monthly_posted_task' => PostTask::where('user_id', Auth::id())->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count(),
            // Yearly Status
            'yearly_posted_task' => PostTask::where('user_id', Auth::id())->whereYear('created_at', date('Y'))->count(),
            // Total Status
            'total_pending_posted_task' => PostTask::where('user_id', Auth::id())->where('status', 'Pending')->count(),
            'total_running_posted_task' => PostTask::where('user_id', Auth::id())->where('status', 'Running')->count(),
            'total_rejected_posted_task' => PostTask::where('user_id', Auth::id())->where('status', 'Rejected')->count(),
            'total_canceled_posted_task' => PostTask::where('user_id', Auth::id())->where('status', 'Canceled')->count(),
            'total_paused_posted_task' => PostTask::where('user_id', Auth::id())->where('status', 'Paused')->count(),
            'total_completed_posted_task' => PostTask::where('user_id', Auth::id())->where('status', 'Completed')->count(),
            'total_posted_task' => PostTask::where('user_id', Auth::id())->count(),
            // Task Proof Submit .............................................................................................................
            'today_pending_task_proof_submit' => ProofTask::where('status', 'Pending')->whereDate('created_at', date('Y-m-d'))->whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->count(),
            'today_approved_task_proof_submit' => ProofTask::where('status', 'Approved')->whereDate('created_at', date('Y-m-d'))->whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->count(),
            'today_rejected_task_proof_submit' => ProofTask::where('status', 'Rejected')->whereDate('created_at', date('Y-m-d'))->whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->count(),
            'today_reviewed_task_proof_submit' => ProofTask::where('status', 'Reviewed')->whereDate('created_at', date('Y-m-d'))->whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->count(),
            // Monthly Status
            'monthly_task_proof_submit' => ProofTask::whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count(),
            // Yearly Status
            'yearly_task_proof_submit' => ProofTask::whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->whereYear('created_at', date('Y'))->count(),
            // Total Status
            'total_pending_task_proof_submit' => ProofTask::whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->where('status', 'Pending')->count(),
            'total_approved_task_proof_submit' => ProofTask::whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->where('status', 'Approved')->count(),
            'total_rejected_task_proof_submit' => ProofTask::whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->where('status', 'Rejected')->count(),
            'total_reviewed_task_proof_submit' => ProofTask::whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->where('status', 'Reviewed')->count(),
            'total_task_proof_submit' => ProofTask::whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->count(),
            // Worked Task .............................................................................................................
            // Today Status
            'today_pending_worked_task' => ProofTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Pending')->count(),
            'today_approved_worked_task' => ProofTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Approved')->count(),
            'today_rejected_worked_task' => ProofTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Rejected')->count(),
            'today_reviewed_worked_task' => ProofTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Reviewed')->count(),
            // Monthly Status
            'monthly_worked_task' => ProofTask::where('user_id', Auth::id())->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count(),
            // Yearly Status
            'yearly_worked_task' => ProofTask::where('user_id', Auth::id())->whereYear('created_at', date('Y'))->count(),
            // Total Status
            'total_pending_worked_task' => ProofTask::where('user_id', Auth::id())->where('status', 'Pending')->count(),
            'total_approved_worked_task' => ProofTask::where('user_id', Auth::id())->where('status', 'Approved')->count(),
            'total_rejected_worked_task' => ProofTask::where('user_id', Auth::id())->where('status', 'Rejected')->count(),
            'total_reviewed_worked_task' => ProofTask::where('user_id', Auth::id())->where('status', 'Reviewed')->count(),
            'total_worked_task' => ProofTask::where('user_id', Auth::id())->count(),
            // Deposit .............................................................................................................
            'total_pending_deposit' => Deposit::where('user_id', Auth::id())->where('status', 'Pending')->sum('amount'),
            'total_rejected_deposit' => Deposit::where('user_id', Auth::id())->where('status', 'Rejected')->sum('amount'),
            'total_approved_deposit' => Deposit::where('user_id', Auth::id())->where('status', 'Approved')->sum('amount'),
            'monthly_deposit' => Deposit::where('user_id', Auth::id())->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('amount'),
            'yearly_deposit' => Deposit::where('user_id', Auth::id())->whereYear('created_at', date('Y'))->sum('amount'),
            'total_deposit' => Deposit::where('user_id', Auth::id())->sum('amount'),
            // Withdraw .............................................................................................................
            'total_pending_withdraw' => Withdraw::where('user_id', Auth::id())->where('status', 'Pending')->sum('amount'),
            'total_rejected_withdraw' => Withdraw::where('user_id', Auth::id())->where('status', 'Rejected')->sum('amount'),
            'total_approved_withdraw' => Withdraw::where('user_id', Auth::id())->where('status', 'Approved')->sum('amount'),
            'monthly_withdraw' => Withdraw::where('user_id', Auth::id())->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('amount'),
            'yearly_withdraw' => Withdraw::where('user_id', Auth::id())->whereYear('created_at', date('Y'))->sum('amount'),
            'total_withdraw' => Withdraw::where('user_id', Auth::id())->sum('amount'),
            // Report .............................................................................................................
            'today_pending_report' => Report::where('reported_by', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Pending')->count(),
            'today_resolved_report' => Report::where('reported_by', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Resolved')->count(),
            'monthly_report' => Report::where('reported_by', Auth::id())->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count(),
            'yearly_report' => Report::where('reported_by', Auth::id())->whereYear('created_at', date('Y'))->count(),
            'total_report' => Report::where('reported_by', Auth::id())->count(),
        ], compact('monthlyWithdraw', 'monthlyDeposite', 'totalTaskProofSubmitChartjsLineData', 'totalWorkedTaskApexLineData', 'userStatus', 'postTaskChargeTotal', 'postTaskChargeWaiting', 'postTaskChargeCanceled', 'postTaskChargePending', 'postTaskChargeWorkerPayment', 'postTaskChargeSitePayment', 'postTaskChargeRefund', 'postTaskChargeHold'));
    }

    // Profile.............................................................................................................

    public function profileEdit(Request $request)
    {
        $user = $request->user();
        $verification = Verification::where('user_id', $user->id)->first();
        $ratingGiven = Rating::where('rated_by', $user->id)->get();
        $ratingReceived  = Rating::where('user_id', $user->id)->get();
        $userDetails = UserDetail::where('user_id', $user->id)->latest()->take(5)->get();
        $reportUserCount = Report::where('user_id', $user->id)->where('type', 'User')->count();
        $reportPostTaskCount = Report::where('user_id', $user->id)->where('type', 'Post Task')->count();
        $reportProofTaskCount = Report::where('user_id', $user->id)->where('type', 'Proof Task')->count();
        return view('profile.edit', compact('user', 'verification', 'userDetails', 'ratingGiven', 'ratingReceived', 'reportUserCount', 'reportPostTaskCount', 'reportProofTaskCount'));
    }

    public function profileSetting(Request $request)
    {
        $user = $request->user();
        $verification = Verification::where('user_id', $user->id)->first();
        $ratingGiven = Rating::where('rated_by', $user->id)->get();
        $ratingReceived  = Rating::where('user_id', $user->id)->get();
        $blockedStatuses = UserStatus::where('user_id', $user->id)->where('status', 'Blocked')->latest()->get();
        $reportUserCount = Report::where('user_id', $user->id)->where('type', 'User')->count();
        $reportPostTaskCount = Report::where('user_id', $user->id)->where('type', 'Post Task')->count();
        $reportProofTaskCount = Report::where('user_id', $user->id)->where('type', 'Proof Task')->count();
        return view('profile.setting', compact('user', 'verification', 'ratingGiven', 'ratingReceived', 'blockedStatuses', 'reportUserCount', 'reportPostTaskCount', 'reportProofTaskCount'));
    }

    public function userProfile($id)
    {
        $user = User::findOrFail(decrypt($id));
        $blocked = Block::where('user_id', $user->id)->where('blocked_by', Auth::id())->exists();
        $nowPostTaskRunningCount = PostTask::where('user_id', $user->id)->where('status', 'Running')->count();
        $totalPostTaskApprovedCount = PostTask::where('user_id', $user->id)->where('approved_by', '!=', null)->count();
        $totalPostTaskApprovedIds = PostTask::where('user_id', $user->id)->where('approved_by', '!=', null)->pluck('id')->toArray();

        $totalPastedTaskProofCount = ProofTask::whereIn('post_task_id', $totalPostTaskApprovedIds)->count();
        $totalPendingTasksProofCount = ProofTask::whereIn('post_task_id', $totalPostTaskApprovedIds)->where('status', 'Pending')->count();
        $totalApprovedTasksProofCount = ProofTask::whereIn('post_task_id', $totalPostTaskApprovedIds)->where('status', 'Approved')->count();
        $totalRejectedTasksProofCount = ProofTask::whereIn('post_task_id', $totalPostTaskApprovedIds)->where('status', 'Rejected')->count();
        $nowReviewedTasksProofCount = ProofTask::whereIn('post_task_id', $totalPostTaskApprovedIds)->where('status', 'Reviewed')->count();
        $totalReviewedTasksProofCount = ProofTask::whereIn('post_task_id', $totalPostTaskApprovedIds)->where('reviewed_at', '!=', null)->count();

        $totalWorkedTask = ProofTask::where('user_id', $user->id)->count();
        $totalPendingWorkedTask = ProofTask::where('user_id', $user->id)->where('status', 'Pending')->count();
        $totalApprovedWorkedTask = ProofTask::where('user_id', $user->id)->where('status', 'Approved')->count();
        $totalRejectedWorkedTask = ProofTask::where('user_id', $user->id)->where('status', 'Rejected')->count();
        $nowReviewedWorkedTask = ProofTask::where('user_id', $user->id)->where('status', 'Reviewed')->count();
        $totalReviewedWorkedTask = ProofTask::where('user_id', $user->id)->where('reviewed_at', '!=', null)->count();

        return view('frontend.user_profile.index', compact('user', 'blocked', 'nowPostTaskRunningCount', 'totalPostTaskApprovedCount', 'totalPastedTaskProofCount', 'totalPendingTasksProofCount', 'totalApprovedTasksProofCount', 'totalRejectedTasksProofCount', 'nowReviewedTasksProofCount', 'totalReviewedTasksProofCount', 'totalWorkedTask', 'totalPendingWorkedTask', 'totalApprovedWorkedTask', 'totalRejectedWorkedTask', 'nowReviewedWorkedTask', 'totalReviewedWorkedTask'));
    }

    // Verification.............................................................................................................

    public function verification(Request $request)
    {
        $verification = Verification::where('user_id', $request->user()->id)->first();
        $user = $request->user();
        return view('frontend.verification.index', compact('user', 'verification'));
    }

    public function verificationStore(Request $request)
    {
        $request->validate([
            'id_type' => 'required|in:NID,Passport,Driving License',
            'id_number' => 'required|string|min:10|unique:verifications,id_number,'.$request->user()->id.',user_id',
            'id_front_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'id_with_face_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $manager = new ImageManager(new Driver());
        // id_front_image
        $id_front_image_name = $request->user()->id."-id_front_image".".". $request->file('id_front_image')->getClientOriginalExtension();
        $image = $manager->read($request->file('id_front_image'));
        $image->toJpeg(80)->save(base_path("public/uploads/verification_photo/").$id_front_image_name);
        // id_with_face_image
        $id_with_face_image_name = $request->user()->id."-id_with_face_image".".". $request->file('id_with_face_image')->getClientOriginalExtension();
        $image = $manager->read($request->file('id_with_face_image'));
        $image->toJpeg(80)->save(base_path("public/uploads/verification_photo/").$id_with_face_image_name);

        Verification::create([
            'user_id' => $request->user()->id,
            'id_type' => $request->id_type,
            'id_number' => $request->id_number,
            'id_front_image' => $id_front_image_name,
            'id_with_face_image' => $id_with_face_image_name,
        ]);

        $notification = array(
            'message' => 'Id Verification request submitted successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    public function reVerificationStore(Request $request)
    {
        $request->validate([
            'id_type' => 'required|in:NID,Passport,Driving License',
            'id_number' => 'required|string|max:255|unique:verifications,id_number,'.$request->user()->id.',user_id',
            'id_front_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'id_with_face_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $verification = Verification::where('id', $request->verification_id)->first();

        $manager = new ImageManager(new Driver());
        // id_front_image
        if ($request->file('id_front_image')) {
            unlink(base_path("public/uploads/verification_photo/").$verification->id_front_image);
            $id_front_image_name = $request->user()->id."-id_front_image".".". $request->file('id_front_image')->getClientOriginalExtension();
            $image = $manager->read($request->file('id_front_image'));
            $image->toJpeg(80)->save(base_path("public/uploads/verification_photo/").$id_front_image_name);
            $verification->update([
                'id_front_image' => $id_front_image_name,
            ]);
        }
        // id_with_face_image
        if ($request->file('id_with_face_image')) {
            unlink(base_path("public/uploads/verification_photo/").$verification->id_with_face_image);
            $id_with_face_image_name = $request->user()->id."-id_with_face_image".".". $request->file('id_with_face_image')->getClientOriginalExtension();
            $image = $manager->read($request->file('id_with_face_image'));
            $image->toJpeg(80)->save(base_path("public/uploads/verification_photo/").$id_with_face_image_name);
            $verification->update([
                'id_with_face_image' => $id_with_face_image_name,
            ]);
        }

        $verification->update([
            'id_type' => $request->id_type,
            'id_number' => $request->id_number,
            'status' => 'Pending',
        ]);

        $notification = array(
            'message' => 'Id Verification request updated successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    // Deposit.............................................................................................................

    public function deposit(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $query = Deposit::where('user_id', Auth::id());

                if ($request->status) {
                    $query->where('deposits.status', $request->status);
                }

                if ($request->method){
                    $query->where('deposits.method', $request->method);
                }

                $query->select('deposits.*')->orderBy('created_at', 'desc');

                $deposits = $query->get();

                return DataTables::of($deposits)
                    ->addIndexColumn()
                    ->editColumn('amount', function ($row) {
                        return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->amount . '</span>';
                    })
                    ->editColumn('payable_amount', function ($row) {
                        return '<span class="badge bg-dark">' . get_site_settings('site_currency_symbol') . ' ' . $row->payable_amount . '</span>';
                    })
                    ->editColumn('method', function ($row) {
                        if ($row->method == 'Bkash') {
                            $method = '
                            <span class="badge bg-primary">' . $row->method . '</span>
                            ';
                        } else if ($row->method == 'Nagad') {
                            $method = '
                            <span class="badge bg-warning">' . $row->method . '</span>
                            ';
                        } else if ($row->method == 'Rocket') {
                            $method = '
                            <span class="badge bg-info">' . $row->method . '</span>
                            ';
                        } else {
                            $method = '
                            <span class="badge bg-success">' . $row->method . '</span>
                            ';
                        }
                        return $method;
                    })
                    ->editColumn('number', function ($row) {
                        if ($row->number) {
                            $number = $row->number;
                        } else {
                            $number = 'N/A';
                        }
                        return $number;
                    })
                    ->editColumn('transaction_id', function ($row) {
                        if ($row->transaction_id) {
                            $transaction_id = $row->transaction_id;
                        } else {
                            $transaction_id = 'N/A';
                        }
                        return $transaction_id;
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->addColumn('approved_or_rejected_at', function ($row) {
                        if ($row->status == 'Approved') {
                            if ($row->method == 'Withdrawal Balance') {
                                $date = date('d M Y h:i A', strtotime($row->created_at));
                            } else {
                                $date = date('d M Y h:i A', strtotime($row->approved_at));
                            }
                        } else if ($row->status == 'Rejected') {
                            $date = date('d M Y h:i A', strtotime($row->rejected_at));
                        } else {
                            if ($row->created_at->addHours(24) > now()) {
                                $date = 'Waiting until ' . $row->created_at->addHours(24)->format('d M Y h:i A');
                            } else {
                                $date = 'Please contact with us.';
                            }
                        }
                        return $date;
                    })
                    ->editColumn('status', function ($row) {
                        if ($row->status == 'Pending') {
                            $status = '
                            <span class="badge bg-info text-white">' . $row->status . '</span>
                            ';
                        } else if ($row->status == 'Approved') {
                            $status = '
                            <span class="badge bg-success">' . $row->status . '</span>
                            ';
                        } else {
                            $status = '
                            <span class="badge bg-danger">' . $row->status . '</span>
                            ';
                        }
                        return $status;
                    })
                    ->rawColumns(['method', 'number', 'amount', 'payable_amount', 'created_at', 'approved_or_rejected_at', 'status'])
                    ->make(true);
            }

            $total_deposit = Deposit::where('user_id', $request->user()->id)->where('status', 'Approved')->sum('amount');

            return view('frontend.deposit.index', compact('total_deposit'));
        }
    }

    public function depositStore(Request $request)
    {
        $minDepositAmount = get_default_settings('min_deposit_amount');
        $maxDepositAmount = get_default_settings('max_deposit_amount');

        $validator = Validator::make($request->all(), [
            'amount' => "required|numeric|min:$minDepositAmount|max:$maxDepositAmount",
            'method' => 'required|in:Bkash,Nagad,Rocket',
            'number' => ['required', 'string', 'regex:/^(?:\+8801|01)[3-9]\d{8}$/'],
            'transaction_id' => 'required|string|max:255',
        ],
        [
            'number.regex' => 'The phone number must be a valid Bangladeshi number (+8801XXXXXXXX or 01XXXXXXXX).',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            Deposit::create([
                'user_id' => $request->user()->id,
                'amount' => $request->amount,
                'payable_amount' => $request->amount,
                'method' => $request->method,
                'number' => $request->number,
                'transaction_id' => $request->transaction_id,
                'status' => 'Pending',
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function withdrawalBalanceDepositStore(Request $request)
    {
        $minDepositAmount = get_default_settings('min_deposit_amount');
        $maxDepositAmount = get_default_settings('max_deposit_amount');
        $currencySymbol = get_site_settings('site_currency_symbol');

        $validator = Validator::make($request->all(), [
            'deposit_amount' => "required|numeric|min:$minDepositAmount|max:$maxDepositAmount",
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            if ($request->deposit_amount > $request->user()->withdraw_balance) {
                return response()->json([
                    'status' => 401,
                    'error'=> 'Insufficient balance in your account to deposit ' . $currencySymbol .' '. $request->deposit_amount .
                            '. Your current balance is ' . $currencySymbol .' '. $request->user()->withdraw_balance
                ]);
            }else {
                $deposit_amount = $request->deposit_amount - ($request->deposit_amount * get_default_settings('withdrawal_balance_deposit_charge_percentage') / 100);

                Deposit::create([
                    'user_id' => $request->user()->id,
                    'method' => 'Withdrawal Balance',
                    'amount' => $request->deposit_amount,
                    'payable_amount' => $deposit_amount,
                    'approved_at' => now(),
                    'status' => 'Approved',
                ]);

                $request->user()->increment('deposit_balance', $deposit_amount);
                $request->user()->decrement('withdraw_balance', $request->deposit_amount);
                $total_deposit = Deposit::where('user_id', $request->user()->id)->where('status', 'Approved')->sum('amount');

                return response()->json([
                    'status' => 200,
                    'deposit_balance' => number_format($request->user()->deposit_balance, 2, '.', ''),
                    'withdraw_balance' => number_format($request->user()->withdraw_balance, 2, '.', ''),
                    'total_deposit' => $total_deposit,
                ]);
            }
        }
    }

    // Withdraw.............................................................................................................

    public function withdraw(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $query = Withdraw::where('user_id', Auth::id());

                if ($request->status) {
                    $query->where('withdraws.status', $request->status);
                }

                if ($request->method){
                    $query->where('withdraws.method', $request->method);
                }

                if ($request->type){
                    $query->where('withdraws.type', $request->type);
                }

                $query->select('withdraws.*')->orderBy('created_at', 'desc');

                $withdraws = $query->get();

                return DataTables::of($withdraws)
                    ->addIndexColumn()
                    ->editColumn('type', function ($row) {
                        if ($row->type == 'Ragular') {
                            $type = '
                            <span class="badge bg-dark">' . $row->type . '</span>
                            ';
                        } else {
                            $type = '
                            <span class="badge bg-primary">' . $row->type . '</span>
                            ';
                        }
                        return $type;
                    })
                    ->editColumn('amount', function ($row) {
                        return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->amount . '</span>';
                    })
                    ->editColumn('payable_amount', function ($row) {
                        return '<span class="badge bg-dark">' . get_site_settings('site_currency_symbol') . ' ' . $row->payable_amount . '</span>';
                    })
                    ->editColumn('method', function ($row) {
                        if ($row->method == 'Bkash') {
                            $method = '
                            <span class="badge bg-primary">' . $row->method . '</span>
                            ';
                        } else if ($row->method == 'Nagad') {
                            $method = '
                            <span class="badge bg-success">' . $row->method . '</span>
                            ';
                        } else {
                            $method = '
                            <span class="badge bg-info">' . $row->method . '</span>
                            ';
                        }
                        return $method;
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->addColumn('approved_or_rejected_at', function ($row) {
                        if ($row->status == 'Approved') {
                            $date = date('d M Y h:i A', strtotime($row->approved_at));
                        } else if ($row->status == 'Rejected') {
                            $date = date('d M Y h:i A', strtotime($row->rejected_at));
                        } else {
                            if ($row->type == 'Instant') {
                                if ($row->created_at->addMinutes(30) > now()) {
                                    $date = 'Waiting until ' . $row->created_at->addMinutes(30)->format('d M Y h:i A');
                                } else {
                                    $date = 'Please contact with us.';
                                }
                            } else {
                                if ($row->created_at->addHours(24) > now()) {
                                    $date = 'Waiting until ' . $row->created_at->addHours(24)->format('d M Y h:i A');
                                } else {
                                    $date = 'Please contact with us.';
                                }
                            }
                        }
                        return $date;
                    })
                    ->editColumn('status', function ($row) {
                        if ($row->status == 'Pending') {
                            $status = '
                            <span class="badge bg-info text-white">' . $row->status . '</span>
                            ';
                        } else if ($row->status == 'Approved') {
                            $status = '
                            <span class="badge bg-success">' . $row->status . '</span>
                            ';
                        } else {
                            $status = '
                            <span class="badge bg-danger">' . $row->status . '</span>
                            ';
                        }
                        return $status;
                    })
                    ->rawColumns(['type', 'method', 'amount', 'payable_amount', 'created_at', 'approved_or_rejected_at', 'status'])
                    ->make(true);
            }

            $total_withdraw = Withdraw::where('user_id', $request->user()->id)->where('status', 'Approved')->sum('amount');

            return view('frontend.withdraw.index', compact('total_withdraw'));
        }
    }

    public function withdrawStore(Request $request)
    {
        $minWithdrawAmount = get_default_settings('min_withdraw_amount');
        $maxWithdrawAmount = get_default_settings('max_withdraw_amount');
        $currencySymbol = get_site_settings('site_currency_symbol');
        $withdrawChargePercentage = get_default_settings('withdraw_charge_percentage');
        $instantWithdrawCharge = get_default_settings('instant_withdraw_charge');

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:Ragular,Instant',
            'amount' => "required|numeric|min:$minWithdrawAmount|max:$maxWithdrawAmount",
            'method' => 'required|in:Bkash,Nagad,Rocket',
            'number' => ['required', 'string', 'regex:/^(?:\+8801|01)[3-9]\d{8}$/'],
        ],
        [
            'number.regex' => 'The phone number must be a valid Bangladeshi number (+8801XXXXXXXX or 01XXXXXXXX).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        }

        if ($request->amount > $request->user()->withdraw_balance) {
            return response()->json([
                'status' => 402,
                'error' => 'Insufficient balance in your account to withdraw ' . $currencySymbol . $request->amount .
                        '. Your current balance is ' . $currencySymbol . $request->user()->withdraw_balance
            ]);
        }

        $payableAmount = $request->amount - ($request->amount * $withdrawChargePercentage / 100);
        if ($request->type == 'Instant') {
            $payableAmount -= $instantWithdrawCharge;
        }

        Withdraw::create([
            'type' => $request->type,
            'user_id' => $request->user()->id,
            'amount' => $request->amount,
            'method' => $request->method,
            'number' => $request->number,
            'payable_amount' => $payableAmount,
            'status' => 'Pending',
        ]);

        $request->user()->decrement('withdraw_balance', $request->amount);

        return response()->json([
            'status' => 200,
            'withdraw_balance' => number_format($request->user()->withdraw_balance, 2, '.', ''),
        ]);
    }

    // Bonus.............................................................................................................

    public function bonus(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $query = Bonus::where('user_id', Auth::id());

                if ($request->type) {
                    $query->where('bonuses.type', $request->type);
                }

                $query->select('bonuses.*')->orderBy('created_at', 'desc');

                $bonuses = $query->get();

                return DataTables::of($bonuses)
                    ->addIndexColumn()
                    ->editColumn('type', function ($row) {
                        $type = '
                            <span class="badge bg-primary">' . $row->type . '</span>
                        ';
                        return $type;
                    })
                    ->editColumn('bonus_by', function ($row) {
                        if ($row->type == 'Proof Task Approved Bonus') {
                            $bonus_by = '
                            <a href="'.route('user.profile', encrypt($row->bonusBy->id)).'" title="'.$row->bonusBy->name.'" class="text-info">
                                '.$row->bonusBy->name.'
                            </a>
                            ';
                        } else {
                            $bonus_by = '
                            <span class="badge bg-primary">'.get_site_settings('site_name').'</span>
                            ';
                        }
                        return $bonus_by;
                    })
                    ->editColumn('amount', function ($row) {
                        return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->amount . '</span>';
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->rawColumns(['type', 'bonus_by', 'amount', 'created_at'])
                    ->make(true);
            }

            $total_bonus = Bonus::where('user_id', Auth::id())->sum('amount');

            return view('frontend.bonus.index', compact('total_bonus'));
        }
    }

    // Notification.............................................................................................................

    public function notification(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $notifications = $user->notifications;

            return DataTables::of($notifications)
                ->addIndexColumn()
                ->editColumn('type', function ($row) {
                    return class_basename($row->type);
                })
                ->editColumn('title', function ($row) {
                    return $row->data['title'];
                })
                ->editColumn('message', function ($row) {
                    return $row->data['message'];
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->diffForHumans();
                })
                ->editColumn('status', function ($row) {
                    if ($row->read_at) {
                        $status = '
                        <span class="badge bg-success">Read</span>
                        ';
                    } else {
                        $status = '
                        <span class="badge bg-danger">Unread</span>
                        ';
                    }
                    return $status;
                })
                ->rawColumns(['status'])
                ->make(true);
        }

        return view('frontend.notification.index');
    }

    public function notificationRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }
        return redirect()->route('notification');
    }

    public function notificationReadAll()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return redirect()->route('notification');
    }

    // Refferal.............................................................................................................

    public function refferal()
    {
        $referralCount = User::where('referred_by', Auth::user()->id)->count();

        $referralEarned = Bonus::where('user_id', Auth::user()->id)
                        ->whereIn('type', ['Referral Registration Bonus', 'Referral Withdrawal Bonus'])
                        ->whereNot('bonus_by', Auth::user()->referred_by)
                        ->sum('amount');

        return view('frontend.refferal.index', compact('referralCount', 'referralEarned'));
    }

    // Block.............................................................................................................

    public function blockList(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $blockedUsers = Block::where('blocked_by', Auth::id());

                $query = $blockedUsers->select('blocks.*');

                $blockedList = $query->get();

                return DataTables::of($blockedList)
                    ->addIndexColumn()
                    ->editColumn('user', function ($row) {
                        return '
                            <a href="'.route('user.profile', encrypt($row->blocked->id)).'" title="'.$row->blocked->name.'" class="text-info">
                                '.$row->blocked->name.'
                            </a>
                        ';
                    })
                    ->editColumn('blocked_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->blocked_at));
                    })
                    ->addColumn('action', function ($row) {
                        $action = '
                        <a href="'.route('block.unblock.user', $row->blocked->id).'" title="Unblock" class="btn btn-danger btn-sm">
                            Unblock
                        </a>
                        ';
                        return $action;
                    })
                    ->rawColumns(['user', 'action'])
                    ->make(true);
            }
            return view('frontend.block_list.index');
        }
    }

    public function blockUnblockUser($id)
    {
        $blocked = Block::where('user_id', $id)->where('blocked_by', Auth::id())->exists();

        if ($blocked) {
            Block::where('user_id', $id)->where('blocked_by', Auth::id())->delete();

            $notification = array(
                'message' => 'User unblocked successfully.',
                'alert-type' => 'success'
            );

            return back()->with($notification);
        }

        Block::create([
            'user_id' => $id,
            'blocked_by' => Auth::id(),
            'blocked_at' => now(),
        ]);

        $notification = array(
            'message' => 'User blocked successfully.',
            'alert-type' => 'error'
        );

        return back()->with($notification);
    }

    // Report.............................................................................................................

    public function reportList(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $reportedUsers = Report::where('reported_by', Auth::id());

                $query = $reportedUsers->select('reports.*');

                if ($request->status) {
                    $query->where('reports.status', $request->status);
                }

                $reportedList = $query->get();

                return DataTables::of($reportedList)
                    ->addIndexColumn()
                    ->editColumn('type', function ($row) {
                        if ($row->type == 'User') {
                            $type = '
                            <span class="badge bg-primary">' . $row->type . '</span>
                            ';
                        } else if ($row->type == 'Post Task') {
                            $type = '
                            <span class="badge bg-secondary">' . $row->type . '</span>
                            ';
                        } else {
                            $type = '
                            <span class="badge bg-info">' . $row->type . '</span>
                            ';
                        }
                        return $type;
                    })
                    ->editColumn('user', function ($row) {
                        return '
                            <a href="'.route('user.profile', encrypt($row->reported->id)).'" title="'.$row->reported->name.'" class="text-info">
                                '.$row->reported->name.'
                        ';
                    })
                    ->editColumn('created_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->created_at));
                    })
                    ->editColumn('status', function ($row) {
                        if ($row->status == 'Pending') {
                            $status = '
                            <span class="badge bg-warning">' . $row->status . '</span>
                            ';
                        } else {
                            $status = '
                            <span class="badge bg-success">' . $row->status . '</span>
                            ';
                        }
                        return $status;
                    })
                    ->addColumn('action', function ($row) {
                        $action = '
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                        ';
                        return $action;
                    })
                    ->rawColumns(['type', 'user', 'status', 'action'])
                    ->make(true);
            }
            return view('frontend.report_list.index');
        }
    }

    public function reportView($id)
    {
        $report = Report::findOrFail($id);
        $report_reply = ReportReply::where('report_id', $id)->first();
        return view('frontend.report_list.view', compact('report', 'report_reply'));
    }

    public function reportSend(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:5000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            $photo_name = null;
            if ($request->file('photo')) {
                $manager = new ImageManager(new Driver());
                $photo_name = $id."-report_photo-".date('YmdHis').".".$request->file('photo')->getClientOriginalExtension();
                $image = $manager->read($request->file('photo'));
                $image->toJpeg(80)->save(base_path("public/uploads/report_photo/").$photo_name);
            }

            Report::create([
                'type' => $request->type,
                'user_id' => $id,
                'post_task_id' => $request->post_task_id ?? null,
                'proof_task_id' => $request->proof_task_id ?? null,
                'reason' => $request->reason,
                'photo' => $photo_name,
                'status' => 'Pending',
                'reported_by' => Auth::id(),
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    // Support.............................................................................................................

    public function support(){
        $supports = Support::where('sender_id', Auth::id())->orWhere('receiver_id', Auth::id())->get();

        Support::where('receiver_id', Auth::id())->where('status', 'Unread')->each(function ($message) {
            $message->status = 'Read';
            $message->save();
        });

        return view('frontend.support.index' , compact('supports'));
    }

    public function supportGetMessage(){
        $supports = Support::where('sender_id', Auth::id())->orWhere('receiver_id', Auth::id())->get();

        $customSupports = $supports->map(function ($support) {
            return [
                'id' => $support->id,
                'message' => $support->message,
                'created_at' => $support->created_at->diffForHumans(), // Relative time like "2 minutes ago"
                'sender_id' => $support->sender_id,
                'receiver_id' => $support->receiver_id,
                'sender_photo' => $support->sender->profile_photo, // Assuming 'profile_photo' exists for sender
                'receiver_photo' => $support->receiver->profile_photo, // Assuming 'profile_photo' exists for receiver
                'photo' => $support->photo,
            ];
        });

        return response()->json([
            'status' => 200,
            'supports' => $customSupports,
        ]);
    }

    public function supportSendMessage(Request $request){
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:5000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            $photo_name = null;
            if ($request->file('photo')) {
                $manager = new ImageManager(new Driver());
                $photo_name = Auth::id()."-support_photo_".date('YmdHis').".".$request->file('photo')->getClientOriginalExtension();
                $image = $manager->read($request->file('photo'));
                $image->toJpeg(80)->save(base_path("public/uploads/support_photo/").$photo_name);
            }

            $support = Support::create([
                'sender_id' => Auth::id(),
                'receiver_id' => 1,
                'message' => $request->message,
                'photo' => $photo_name,
            ]);

            Support::where('receiver_id', Auth::id())->where('status', 'Unread')->each(function ($message) {
                $message->status = 'Read';
                $message->save();
            });

            SupportEvent::dispatch($support);

            return response()->json([
                'status' => 200,
                'support' => [
                    'message' => $support->message,
                    'photo' => $support->photo,
                    'sender_id' => $support->sender_id,
                    'created_at' => Carbon::parse($support->created_at)->diffForHumans(),
                ]
            ]);
        }
    }
}
