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
                ->where('status', 'Approved')
                ->whereNot('method', 'Deposit Balance')
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
                ->where('status', 'Approved')
                ->whereNot('method', 'Withdraw Balance')
                ->whereMonth('created_at', $month->format('m'))
                ->whereYear('created_at', $month->format('Y'))
                ->sum('amount');

            $monthlyDeposite[$formattedMonth] = number_format($amount, 2, '.', '');
        }
        $monthlyDeposite = array_reverse($monthlyDeposite, true);

        // totalBalanceTransferChartjsLineData
        $totalBalanceTransferChartjsLineFixedStatuses = ['Withdraw Balance', 'Deposit Balance'];

        // Collect all unique years from both models
        $totalBalanceTransferChartjsLineYears = array_unique(
            array_merge(
                Withdraw::where('method', 'Deposit Balance')
                    ->whereYear('approved_at', '<=', date('Y'))
                    ->where('user_id', Auth::id())
                    ->pluck('approved_at')
                    ->map(fn($date) => Carbon::parse($date)->format('Y'))
                    ->toArray(),
                Deposit::where('method', 'Withdraw Balance')
                    ->whereYear('approved_at', '<=', date('Y'))
                    ->where('user_id', Auth::id())
                    ->pluck('approved_at')
                    ->map(fn($date) => Carbon::parse($date)->format('Y'))
                    ->toArray()
            )
        );
        sort($totalBalanceTransferChartjsLineYears); // Sort the years

        // Prepare data for the chart
        $totalBalanceTransferChartjsLineData = [
            'labels' => $totalBalanceTransferChartjsLineYears,
            'datasets' => []
        ];

        // Loop through statuses and collect data
        foreach ($totalBalanceTransferChartjsLineFixedStatuses as $status) {
            $data = [];
            foreach ($totalBalanceTransferChartjsLineYears as $year) {
                if ($status === 'Withdraw Balance') {
                    $amount = Deposit::whereYear('approved_at', $year)
                        ->where('user_id', Auth::id())
                        ->where('method', $status)
                        ->sum('amount');
                } elseif ($status === 'Deposit Balance') {
                    $amount = Withdraw::whereYear('approved_at', $year)
                        ->where('user_id', Auth::id())
                        ->where('method', $status)
                        ->sum('amount');
                } else {
                    $amount = 0; // Default to 0 for safety
                }
                $data[] = $amount;
            }
            $totalBalanceTransferChartjsLineData['datasets'][] = [
                'label' => $status,
                'data' => $data,
            ];
        };

        // return $totalBalanceTransferChartjsLineData;

        // totalReportSendApexLineData
        $totalReportSendApexLineFixedStatuses = ['Pending', 'False', 'Received'];
        $totalReportSendApexLineYears = Report::where('reported_by', Auth::id())->whereYear('created_at', '<=', date('Y'))->pluck('created_at')->map(fn($date) => Carbon::parse($date)->format('Y'))->unique()->sort()->values()->toArray();
        $totalReportSendApexLineData = [
            'categories' => $totalReportSendApexLineYears,
            'series' => []
        ];
        foreach ($totalReportSendApexLineFixedStatuses as $status) {
            $data = [];
            foreach ($totalReportSendApexLineYears as $year) {
                $count = Report::where('reported_by', Auth::id())->whereYear('created_at', $year)->where('status', $status)->count();
                $data[] = $count;
            }
            $totalReportSendApexLineData['series'][] = [
                'name' => $status,
                'data' => $data
            ];
        }

        $userStatus = UserStatus::where('user_id', Auth::id())->latest()->first();

        // PostTask Charges
        $postTaskChargeTotal = PostTask::where('user_id', Auth::id())
            ->whereNotNull('approved_at')
            ->sum('total_cost');

        $postTasks = PostTask::where('user_id', Auth::id())
            ->whereNotNull('approved_at')
            ->with(['proofTasks' => function ($query) {
                $query->select('id', 'post_task_id', 'status', 'reviewed_at', 'rejected_at');
            }])
            ->get();

        $canceledTasks = $postTasks->where('status', 'Canceled');
        $otherTasks = $postTasks->where('status', '!=', 'Canceled');

        $postTaskChargeWaiting = 0;
        $postTaskChargePayment = 0;
        $postTaskChargeRefund = 0;
        $postTaskChargeHold = 0;

        // Handle Canceled Tasks
        foreach ($canceledTasks as $task) {
            $chargePerTask = ($task->sub_cost + $task->site_charge) / $task->worker_needed;
            $proofCount = $task->proofTasks->count();
            $postTaskChargeRefund += $chargePerTask * ($task->worker_needed - $proofCount);
        }

        // Handle Other Tasks
        foreach ($otherTasks as $task) {
            $chargePerTask = ($task->sub_cost + $task->site_charge) / $task->worker_needed;

            $pendingCount = $task->proofTasks->where('status', 'Pending')->count();
            $approvedCount = $task->proofTasks->where('status', 'Approved')->count();
            $rejectedCount = $task->proofTasks->where('status', 'Rejected')
                ->where(function ($proof) {
                    return is_null($proof->reviewed_at)
                        && $proof->rejected_at <= now()->subHours(get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time'))
                        || !is_null($proof->reviewed_at);
                })->count();
            $holdTaskCount = $task->proofTasks->where(function ($proof) {
                return $proof->status === 'Reviewed'
                    || ($proof->status === 'Rejected' && is_null($proof->reviewed_at)
                    && $proof->rejected_at > now()->subHours(get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time')));
            })->count();

            $notSubmittingCount = $task->worker_needed - $task->proofTasks->count();
            $notSubmittingCharge = $chargePerTask * $notSubmittingCount;

            $postTaskChargeWaiting += $chargePerTask * $pendingCount + $notSubmittingCharge;
            $postTaskChargePayment += ($task->income_of_each_worker * $approvedCount)
                + (($task->site_charge / $task->worker_needed) * $approvedCount
                + $task->required_proof_photo_charge + $task->boosting_time_charge
                + $task->work_duration_charge);
            $postTaskChargeRefund += $chargePerTask * $rejectedCount;
            $postTaskChargeHold += $chargePerTask * $holdTaskCount;
        }

        return view('frontend/dashboard', [
            // Posted Task .............................................................................................................
            // Today Status
            'today_posted_task' => PostTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->count(),
            // 'today_pending_posted_task' => PostTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Pending')->count(),
            // 'today_running_posted_task' => PostTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Running')->count(),
            // 'today_rejected_posted_task' => PostTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Rejected')->count(),
            // 'today_canceled_posted_task' => PostTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Canceled')->count(),
            // 'today_paused_posted_task' => PostTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Paused')->count(),
            // 'today_completed_posted_task' => PostTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Completed')->count(),
            // Monthly Status
            'monthly_posted_task' => PostTask::where('user_id', Auth::id())->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count(),
            // Yearly Status
            'yearly_posted_task' => PostTask::where('user_id', Auth::id())->whereYear('created_at', date('Y'))->count(),
            // Total Status
            'total_posted_task' => PostTask::where('user_id', Auth::id())->count(),
            'total_pending_posted_task' => PostTask::where('user_id', Auth::id())->where('status', 'Pending')->count(),
            'total_running_posted_task' => PostTask::where('user_id', Auth::id())->where('status', 'Running')->count(),
            'total_rejected_posted_task' => PostTask::where('user_id', Auth::id())->where('status', 'Rejected')->count(),
            'total_canceled_posted_task' => PostTask::where('user_id', Auth::id())->where('status', 'Canceled')->count(),
            'total_paused_posted_task' => PostTask::where('user_id', Auth::id())->where('status', 'Paused')->count(),
            'total_completed_posted_task' => PostTask::where('user_id', Auth::id())->where('status', 'Completed')->count(),
            // Posted Task Proof Submit .............................................................................................................
            'today_posted_task_proof_submit' => ProofTask::whereDate('created_at', date('Y-m-d'))->whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->count(),
            // 'today_pending_posted_task_proof_submit' => ProofTask::where('status', 'Pending')->whereDate('created_at', date('Y-m-d'))->whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->count(),
            // 'today_approved_posted_task_proof_submit' => ProofTask::where('status', 'Approved')->whereDate('created_at', date('Y-m-d'))->whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->count(),
            // 'today_rejected_posted_task_proof_submit' => ProofTask::where('status', 'Rejected')->whereDate('created_at', date('Y-m-d'))->whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->count(),
            // 'today_reviewed_posted_task_proof_submit' => ProofTask::where('status', 'Reviewed')->whereDate('created_at', date('Y-m-d'))->whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->count(),
            // Monthly Status
            'monthly_posted_task_proof_submit' => ProofTask::whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count(),
            // Yearly Status
            'yearly_posted_task_proof_submit' => ProofTask::whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->whereYear('created_at', date('Y'))->count(),
            // Total Status
            'total_posted_task_proof_submit' => ProofTask::whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->count(),
            'total_pending_posted_task_proof_submit' => ProofTask::whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->where('status', 'Pending')->count(),
            'total_approved_posted_task_proof_submit' => ProofTask::whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->where('status', 'Approved')->count(),
            'total_rejected_posted_task_proof_submit' => ProofTask::whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->where('status', 'Rejected')->count(),
            'total_reviewed_posted_task_proof_submit' => ProofTask::whereIn('post_task_id', PostTask::where('user_id', Auth::id())->pluck('id'))->where('status', 'Reviewed')->count(),
            // Worked Task .............................................................................................................
            // Today Status
            'today_worked_task' => ProofTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->count(),
            // 'today_pending_worked_task' => ProofTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Pending')->count(),
            // 'today_approved_worked_task' => ProofTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Approved')->count(),
            // 'today_rejected_worked_task' => ProofTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Rejected')->count(),
            // 'today_reviewed_worked_task' => ProofTask::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Reviewed')->count(),
            // Monthly Status
            'monthly_worked_task' => ProofTask::where('user_id', Auth::id())->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count(),
            // Yearly Status
            'yearly_worked_task' => ProofTask::where('user_id', Auth::id())->whereYear('created_at', date('Y'))->count(),
            // Total Status
            'total_worked_task' => ProofTask::where('user_id', Auth::id())->count(),
            'total_pending_worked_task' => ProofTask::where('user_id', Auth::id())->where('status', 'Pending')->count(),
            'total_approved_worked_task' => ProofTask::where('user_id', Auth::id())->where('status', 'Approved')->count(),
            'total_rejected_worked_task' => ProofTask::where('user_id', Auth::id())->where('status', 'Rejected')->count(),
            'total_reviewed_worked_task' => ProofTask::where('user_id', Auth::id())->where('status', 'Reviewed')->count(),
            // Deposit .............................................................................................................
            'total_pending_deposit' => Deposit::where('user_id', Auth::id())->where('status', 'Pending')->whereNot('method', 'Withdraw Balance')->sum('amount'),
            'total_rejected_deposit' => Deposit::where('user_id', Auth::id())->where('status', 'Rejected')->whereNot('method', 'Withdraw Balance')->sum('amount'),
            'total_approved_deposit' => Deposit::where('user_id', Auth::id())->where('status', 'Approved')->whereNot('method', 'Withdraw Balance')->sum('amount'),
            'today_deposit' => Deposit::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Approved')->whereNot('method', 'Withdraw Balance')->sum('amount'),
            'monthly_deposit' => Deposit::where('user_id', Auth::id())->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('status', 'Approved')->whereNot('method', 'Withdraw Balance')->sum('amount'),
            'yearly_deposit' => Deposit::where('user_id', Auth::id())->whereYear('created_at', date('Y'))->where('status', 'Approved')->whereNot('method', 'Withdraw Balance')->sum('amount'),
            'total_deposit' => Deposit::where('user_id', Auth::id())->where('status', 'Approved')->whereNot('method', 'Withdraw Balance')->sum('amount'),
            // Withdraw .............................................................................................................
            'total_pending_withdraw' => Withdraw::where('user_id', Auth::id())->where('status', 'Pending')->whereNot('method', 'Deposit Balance')->sum('amount'),
            'total_rejected_withdraw' => Withdraw::where('user_id', Auth::id())->where('status', 'Rejected')->whereNot('method', 'Deposit Balance')->sum('amount'),
            'total_approved_withdraw' => Withdraw::where('user_id', Auth::id())->where('status', 'Approved')->whereNot('method', 'Deposit Balance')->sum('amount'),
            'today_withdraw' => Withdraw::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->whereNot('method', 'Deposit Balance')->sum('amount'),
            'monthly_withdraw' => Withdraw::where('user_id', Auth::id())->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('status', 'Approved')->whereNot('method', 'Deposit Balance')->sum('amount'),
            'yearly_withdraw' => Withdraw::where('user_id', Auth::id())->whereYear('created_at', date('Y'))->where('status', 'Approved')->whereNot('method', 'Deposit Balance')->sum('amount'),
            'total_withdraw' => Withdraw::where('user_id', Auth::id())->where('status', 'Approved')->whereNot('method', 'Deposit Balance')->sum('amount'),
            // // Report Send ............................................................................................................
            // 'total_pending_report_send' => Report::where('reported_by', Auth::id())->where('status', 'Pending')->count(),
            // 'total_false_report_send' => Report::where('reported_by', Auth::id())->where('status', 'False')->count(),
            // 'total_received_report_send' => Report::where('reported_by', Auth::id())->where('status', 'Received')->count(),
        ], compact('monthlyWithdraw', 'monthlyDeposite', 'totalBalanceTransferChartjsLineData', 'totalReportSendApexLineData', 'userStatus', 'postTaskChargeTotal', 'postTaskChargeWaiting', 'postTaskChargePayment', 'postTaskChargeRefund', 'postTaskChargeHold'));
    }

    // Profile.............................................................................................................

    public function profileEdit(Request $request)
    {
        $user = $request->user();
        $verification = Verification::where('user_id', $user->id)->first();
        $ratingGiven = Rating::where('rated_by', $user->id)->get();
        $ratingReceived  = Rating::where('user_id', $user->id)->get();
        $reportUserCount = Report::where('user_id', $user->id)->where('type', 'User')->where('status', 'Received')->count();
        $reportPostTaskCount = Report::where('user_id', $user->id)->where('type', 'Post Task')->where('status', 'Received')->count();
        $reportProofTaskCount = Report::where('user_id', $user->id)->where('type', 'Proof Task')->where('status', 'Received')->count();
        return view('profile.edit', compact('user', 'verification', 'ratingGiven', 'ratingReceived', 'reportUserCount', 'reportPostTaskCount', 'reportProofTaskCount'));
    }

    public function profileSetting(Request $request)
    {
        $user = $request->user();
        $userDetails = UserDetail::where('user_id', $user->id)->latest()->take(5)->get();
        $verification = Verification::where('user_id', $user->id)->first();
        $ratingGiven = Rating::where('rated_by', $user->id)->get();
        $ratingReceived  = Rating::where('user_id', $user->id)->get();
        $blockedStatuses = UserStatus::where('user_id', $user->id)->where('status', 'Blocked')->latest()->get();
        $reportUserCount = Report::where('user_id', $user->id)->where('type', 'User')->where('status', 'Received')->count();
        $reportPostTaskCount = Report::where('user_id', $user->id)->where('type', 'Post Task')->where('status', 'Received')->count();
        $reportProofTaskCount = Report::where('user_id', $user->id)->where('type', 'Proof Task')->where('status', 'Received')->count();
        return view('profile.setting', compact('user', 'userDetails', 'verification', 'ratingGiven', 'ratingReceived', 'blockedStatuses', 'reportUserCount', 'reportPostTaskCount', 'reportProofTaskCount'));
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

        $reportUserCount = Report::where('user_id', $user->id)->where('reported_by', Auth::id())->where('type', 'User')->count();

        return view('frontend.user_profile.index', compact('user', 'blocked', 'nowPostTaskRunningCount', 'totalPostTaskApprovedCount', 'totalPastedTaskProofCount', 'totalPendingTasksProofCount', 'totalApprovedTasksProofCount', 'totalRejectedTasksProofCount', 'nowReviewedTasksProofCount', 'totalReviewedTasksProofCount', 'totalWorkedTask', 'totalPendingWorkedTask', 'totalApprovedWorkedTask', 'totalRejectedWorkedTask', 'nowReviewedWorkedTask', 'totalReviewedWorkedTask', 'reportUserCount'));
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
            'id_number' => 'required|string|unique:verifications,id_number,'.$request->user()->id.',user_id',
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
            'id_number' => 'required|string|unique:verifications,id_number,'.$request->user()->id.',user_id',
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
                            if ($row->method == 'Withdraw Balance') {
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
            $checkDeposit = Deposit::whereNot('status', 'Rejected')->where('method', $request->method)->where('number', $request->number)->where('transaction_id', $request->transaction_id)->first();

            if ($checkDeposit) {
                return response()->json([
                    'status' => 401,
                    'error'=> 'This transaction is already exist.'
                ]);
            }

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

    public function depositBalanceFromWithdrawBalanceStore(Request $request)
    {
        $currencySymbol = get_site_settings('site_currency_symbol');
        $chargePercentage = get_default_settings('deposit_balance_from_withdraw_balance_charge_percentage');

        $validator = Validator::make($request->all(), [
            'deposit_amount' => "required|numeric|min:1",
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
                $payable_amount = $request->deposit_amount - ($request->deposit_amount * $chargePercentage / 100);

                Deposit::create([
                    'user_id' => $request->user()->id,
                    'method' => 'Withdraw Balance',
                    'amount' => $request->deposit_amount,
                    'payable_amount' => $payable_amount,
                    'approved_at' => now(),
                    'status' => 'Approved',
                ]);

                $request->user()->decrement('withdraw_balance', $request->deposit_amount);
                $request->user()->increment('deposit_balance', $payable_amount);
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
                $query = Withdraw::where('user_id', Auth::id())->whereNot('method', 'Deposit Balance');

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
                    ->editColumn('number', function ($row) {
                        return $row->number;
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
                    ->rawColumns(['type', 'method', 'amount', 'payable_amount', 'created_at', 'number', 'approved_or_rejected_at', 'status'])
                    ->make(true);
            }

            $total_withdraw = Withdraw::where('user_id', $request->user()->id)->whereNot('method', 'Deposit Balance')->where('status', 'Approved')->sum('amount');
            $withdrawBalanceFromDepositBalance = Withdraw::where('user_id', $request->user()->id)->where('method', 'Deposit Balance')->sum('amount');

            return view('frontend.withdraw.index', compact('total_withdraw', 'withdrawBalanceFromDepositBalance'));
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

    public function withdrawBalanceFromDepositBalance(Request $request)
    {
        if ($request->ajax()) {
            $query = Withdraw::where('user_id', Auth::id())->where('method', 'Deposit Balance');

            $query->select('withdraws.*')->orderBy('created_at', 'desc');

            $withdraws = $query->get();

            return DataTables::of($withdraws)
                ->addIndexColumn()
                ->editColumn('amount', function ($row) {
                    return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->amount . '</span>';
                })
                ->editColumn('payable_amount', function ($row) {
                    return '<span class="badge bg-dark">' . get_site_settings('site_currency_symbol') . ' ' . $row->payable_amount . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y h:i A');
                })
                ->addColumn('approved_at', function ($row) {
                    return date('d M Y h:i A', strtotime($row->approved_at));
                })
                ->rawColumns(['amount', 'payable_amount', 'created_at', 'approved_at'])
                ->make(true);
        }

        return view('frontend.withdraw.index');
    }

    public function withdrawBalanceFromDepositBalanceStore(Request $request)
    {
        $currencySymbol = get_site_settings('site_currency_symbol');
        $chargePercentage = get_default_settings('withdraw_balance_from_deposit_balance_charge_percentage');

        $validator = Validator::make($request->all(), [
            'withdraw_balance' => "required|numeric|min:1",
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            if ($request->withdraw_balance > $request->user()->deposit_balance) {
                return response()->json([
                    'status' => 401,
                    'error'=> 'Insufficient balance in your account to withdraw balance ' . $currencySymbol .' '. $request->withdraw_balance .
                            '. Your current balance is ' . $currencySymbol .' '. $request->user()->deposit_balance
                ]);
            }else {
                $payable_amount = $request->withdraw_balance - ($request->withdraw_balance * $chargePercentage / 100);

                Withdraw::create([
                    'user_id' => $request->user()->id,
                    'type' => 'Instant',
                    'method' => 'Deposit Balance',
                    'amount' => $request->withdraw_balance,
                    'payable_amount' => $payable_amount,
                    'approved_at' => now(),
                    'status' => 'Approved',
                ]);

                $request->user()->decrement('deposit_balance', $request->withdraw_balance);
                $request->user()->increment('withdraw_balance', $payable_amount);
                $totalWithdrawBalanceFromDepositBalance = Withdraw::where('user_id', $request->user()->id)->where('method', 'Deposit Balance')->sum('amount');

                return response()->json([
                    'status' => 200,
                    'deposit_balance' => number_format($request->user()->deposit_balance, 2, '.', ''),
                    'withdraw_balance' => number_format($request->user()->withdraw_balance, 2, '.', ''),
                    'totalWithdrawBalanceFromDepositBalance' => $totalWithdrawBalanceFromDepositBalance,
                ]);
            }
        }
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
                            $bonus_by = '<span class="badge bg-success">Buyer</span>';
                        } else {
                            $bonus_by = '<span class="badge bg-primary">Admin</span>';
                        }
                        return $bonus_by;
                    })
                    ->editColumn('for_user', function ($row) {
                        $for_user = '
                        <a href="'.route('user.profile', encrypt($row->bonusBy->id)).'" title="'.$row->bonusBy->name.'" class="text-info">
                            '.$row->bonusBy->name.'
                        </a>
                        ';
                        return $for_user;
                    })
                    ->editColumn('amount', function ($row) {
                        return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->amount . '</span>';
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->rawColumns(['type', 'for_user', 'bonus_by', 'amount', 'created_at'])
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
            $notificationsQuery = $user->notifications();

            if ($request->status) {
                if ($request->status == 'Read') {
                    $notificationsQuery->whereNotNull('read_at');
                } else {
                    $notificationsQuery->whereNull('read_at');
                }
            }

            // Clone the query for counts
            $readNotificationsCount = (clone $notificationsQuery)->whereNotNull('read_at')->count();
            $unreadNotificationsCount = (clone $notificationsQuery)->whereNull('read_at')->count();

            $notifications = $notificationsQuery->get();

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
                        $status = '<span class="badge bg-success">Read</span>';
                    } else {
                        $status = '<span class="badge bg-danger">Unread</span>';
                    }
                    return $status;
                })
                ->with([
                    'readNotificationsCount' => $readNotificationsCount,
                    'unreadNotificationsCount' => $unreadNotificationsCount,
                ])
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

                // Total filtered count
                $totalBlockedsCount = $query->count();

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
                    ->with(['totalBlockedsCount' => $totalBlockedsCount])
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

    public function report(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } elseif ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $reportedUsers = Report::where('reported_by', Auth::id());
                $query = $reportedUsers->select('reports.*');

                if ($request->type) {
                    $query->where('reports.type', $request->type);
                }

                if ($request->status) {
                    $query->where('reports.status', $request->status);
                }

                // Clone query for each count
                $pendingReportsCount = (clone $query)->where('status', 'Pending')->count();
                $falseReportsCount = (clone $query)->where('status', 'False')->count();
                $receivedReportsCount = (clone $query)->where('status', 'Received')->count();

                $reportedList = $query->get();

                return DataTables::of($reportedList)
                    ->addIndexColumn()
                    ->editColumn('type', function ($row) {
                        if ($row->type == 'User') {
                            $type = '<span class="badge bg-primary">' . $row->type . '</span>';
                        } elseif ($row->type == 'Post Task') {
                            $type = '<span class="badge bg-secondary">' . $row->type . '</span>';
                        } else {
                            $type = '<span class="badge bg-info">' . $row->type . '</span>';
                        }
                        return $type;
                    })
                    ->editColumn('user', function ($row) {
                        return '<a href="' . route('user.profile', encrypt($row->reported->id)) . '" title="' . $row->reported->name . '" class="text-info">' . $row->reported->name . '</a>';
                    })
                    ->editColumn('created_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->created_at));
                    })
                    ->editColumn('status', function ($row) {
                        if ($row->status == 'Pending') {
                            $status = '<span class="badge bg-info">' . $row->status . '</span>';
                        } elseif ($row->status == 'False') {
                            $status = '<span class="badge bg-danger">' . $row->status . '</span>';
                        } else {
                            $status = '<span class="badge bg-success">' . $row->status . '</span>';
                        }
                        return $status;
                    })
                    ->addColumn('action', function ($row) {
                        return '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">View</button>';
                    })
                    ->with(['pendingReportsCount' => $pendingReportsCount, 'falseReportsCount' => $falseReportsCount, 'receivedReportsCount' => $receivedReportsCount])
                    ->rawColumns(['type', 'user', 'status', 'action'])
                    ->make(true);
            }
            return view('frontend.report.index');
        }
    }

    public function reportView($id)
    {
        $report = Report::findOrFail($id);
        $report_reply = ReportReply::where('report_id', $id)->first();
        return view('frontend.report.view', compact('report', 'report_reply'));
    }

    public function reportSend(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:5000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $reportExists = Report::where('user_id', $userId)->where('post_task_id', $request->post_task_id)->where('proof_task_id', $request->proof_task_id)->exists();

        if ($reportExists) {
            return response()->json([
                'status' => 401,
                'error' => 'You have already reported this user.'
            ]);
        }

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            $photo_name = null;
            if ($request->file('photo')) {
                $manager = new ImageManager(new Driver());
                $photo_name = $userId."-report_photo-".date('YmdHis').".".$request->file('photo')->getClientOriginalExtension();
                $image = $manager->read($request->file('photo'));
                $image->toJpeg(80)->save(base_path("public/uploads/report_photo/").$photo_name);
            }

            Report::create([
                'type' => $request->type,
                'user_id' => $userId,
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
                'sender_id' => $support->sender_id,
                'receiver_id' => $support->receiver_id,
                'sender_photo' => $support->sender->profile_photo, // Assuming 'profile_photo' exists for sender
                'receiver_photo' => $support->receiver->profile_photo, // Assuming 'profile_photo' exists for receiver
                'message' => $support->message,
                'photo' => $support->photo,
                'status' => $support->status,
                'created_at' => $support->created_at->diffForHumans(), // Relative time like "2 minutes ago"
            ];
        });

        return response()->json([
            'status' => 200,
            'supports' => $customSupports,
        ]);
    }

    public function supportSendMessage(Request $request){
        $validator = Validator::make($request->all(), [
            'message' => 'nullable|string|max:5000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!$request->message && !$request->file('photo')) {
                $validator->errors()->add('validator_alert', 'Either a message or a photo is required.');
            }
        });

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            $photo_name = null;
            if ($request->file('photo')) {
                $manager = new ImageManager(new Driver());
                $photo_name = Auth::id() . "-support_photo_" . date('YmdHis') . "." . $request->file('photo')->getClientOriginalExtension();
                $image = $manager->read($request->file('photo'));
                $image->toJpeg(80)->save(base_path("public/uploads/support_photo/") . $photo_name);
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
