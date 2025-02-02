<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\BlockedUser;
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
use App\Models\UserDevice;
use App\Models\Rating;
use App\Models\BalanceTransfer;
use App\Events\SupportEvent;
use App\Models\FavoriteUser;
use Carbon\Carbon;
use App\Notifications\UserStatusNotification;

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
                ->whereMonth('created_at', $month->format('m'))
                ->whereYear('created_at', $month->format('Y'))
                ->sum('amount');

            $monthlyDeposite[$formattedMonth] = number_format($amount, 2, '.', '');
        }
        $monthlyDeposite = array_reverse($monthlyDeposite, true);

        // Fixed statuses for the chart
        $totalBalanceTransferChartjsLineFixedStatuses = ['Withdraw Balance', 'Deposit Balance'];

        // Collect all unique years from the `BalanceTransfer` model
        $totalBalanceTransferChartjsLineYears = BalanceTransfer::where('user_id', Auth::id())
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->pluck('year')
            ->toArray();
        sort($totalBalanceTransferChartjsLineYears); // Sort the years

        // Prepare data structure for the chart
        $totalBalanceTransferChartjsLineData = [
            'labels' => $totalBalanceTransferChartjsLineYears,
            'datasets' => []
        ];

        // Loop through fixed statuses and collect data
        foreach ($totalBalanceTransferChartjsLineFixedStatuses as $status) {
            $data = [];
            foreach ($totalBalanceTransferChartjsLineYears as $year) {
                // Calculate the total amount for each status and year
                $amount = BalanceTransfer::whereYear('created_at', $year)
                    ->where('user_id', Auth::id())
                    ->where('send_method', $status)
                    ->sum('amount');
                $data[] = $amount;
            }

            // Add dataset for the current status
            $totalBalanceTransferChartjsLineData['datasets'][] = [
                'label' => $status,
                'data' => $data,
            ];
        }

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
        $userBlockedStatusCount = UserStatus::where('user_id', Auth::id())->where('status', 'Blocked')->count();

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
            'total_pending_deposit' => Deposit::where('user_id', Auth::id())->where('status', 'Pending')->sum('amount'),
            'total_rejected_deposit' => Deposit::where('user_id', Auth::id())->where('status', 'Rejected')->sum('amount'),
            'total_approved_deposit' => Deposit::where('user_id', Auth::id())->where('status', 'Approved')->sum('amount'),
            'today_deposit' => Deposit::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->where('status', 'Approved')->sum('amount'),
            'monthly_deposit' => Deposit::where('user_id', Auth::id())->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('status', 'Approved')->sum('amount'),
            'yearly_deposit' => Deposit::where('user_id', Auth::id())->whereYear('created_at', date('Y'))->where('status', 'Approved')->sum('amount'),
            'total_deposit' => Deposit::where('user_id', Auth::id())->where('status', 'Approved')->sum('amount'),
            // Withdraw .............................................................................................................
            'total_pending_withdraw' => Withdraw::where('user_id', Auth::id())->where('status', 'Pending')->sum('amount'),
            'total_rejected_withdraw' => Withdraw::where('user_id', Auth::id())->where('status', 'Rejected')->sum('amount'),
            'total_approved_withdraw' => Withdraw::where('user_id', Auth::id())->where('status', 'Approved')->sum('amount'),
            'today_withdraw' => Withdraw::where('user_id', Auth::id())->whereDate('created_at', date('Y-m-d'))->sum('amount'),
            'monthly_withdraw' => Withdraw::where('user_id', Auth::id())->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->where('status', 'Approved')->sum('amount'),
            'yearly_withdraw' => Withdraw::where('user_id', Auth::id())->whereYear('created_at', date('Y'))->where('status', 'Approved')->sum('amount'),
            'total_withdraw' => Withdraw::where('user_id', Auth::id())->where('status', 'Approved')->sum('amount'),
            // // Report Send ............................................................................................................
            // 'total_pending_report_send' => Report::where('reported_by', Auth::id())->where('status', 'Pending')->count(),
            // 'total_false_report_send' => Report::where('reported_by', Auth::id())->where('status', 'False')->count(),
            // 'total_received_report_send' => Report::where('reported_by', Auth::id())->where('status', 'Received')->count(),
        ], compact('monthlyWithdraw', 'monthlyDeposite', 'totalBalanceTransferChartjsLineData', 'totalReportSendApexLineData', 'userStatus', 'postTaskChargeTotal', 'postTaskChargeWaiting', 'postTaskChargePayment', 'postTaskChargeRefund', 'postTaskChargeHold', 'userBlockedStatusCount'));
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
        $userDevices = UserDevice::where('user_id', $user->id)->latest()->take(5)->get();
        $verification = Verification::where('user_id', $user->id)->first();
        $ratingGiven = Rating::where('rated_by', $user->id)->get();
        $ratingReceived  = Rating::where('user_id', $user->id)->get();
        $userStatuses = UserStatus::where('user_id', $user->id)->latest()->get();
        $reportUserCount = Report::where('user_id', $user->id)->where('type', 'User')->where('status', 'Received')->count();
        $reportPostTaskCount = Report::where('user_id', $user->id)->where('type', 'Post Task')->where('status', 'Received')->count();
        $reportProofTaskCount = Report::where('user_id', $user->id)->where('type', 'Proof Task')->where('status', 'Received')->count();
        return view('profile.setting', compact('user', 'userDevices', 'verification', 'ratingGiven', 'ratingReceived', 'userStatuses', 'reportUserCount', 'reportPostTaskCount', 'reportProofTaskCount'));
    }

    public function userProfile($id)
    {
        $user = User::findOrFail(decrypt($id));
        $blocked = BlockedUser::where('user_id', Auth::id())->where('blocked_user_id', $user->id)->exists();
        $favorite = FavoriteUser::where('user_id', Auth::id())->where('favorite_user_id', $user->id)->exists();
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

        return view('frontend.user_profile.index', compact('user', 'blocked', 'favorite', 'nowPostTaskRunningCount', 'totalPostTaskApprovedCount', 'totalPastedTaskProofCount', 'totalPendingTasksProofCount', 'totalApprovedTasksProofCount', 'totalRejectedTasksProofCount', 'nowReviewedTasksProofCount', 'totalReviewedTasksProofCount', 'totalWorkedTask', 'totalPendingWorkedTask', 'totalApprovedWorkedTask', 'totalRejectedWorkedTask', 'nowReviewedWorkedTask', 'totalReviewedWorkedTask', 'reportUserCount'));
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

    // Instant Unblocked
    public function instantUnblocked(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:Deposit Balance,Withdraw Balance',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            $user = User::findOrFail(Auth::id());
            $userStatus = UserStatus::where('user_id', $user->id)->latest()->first();
            $userBlockedStatusCount = UserStatus::where('user_id', Auth::id())->where('status', 'Blocked')->count();
            $user_blocked_instant_resolved_charge = get_default_settings('user_blocked_instant_resolved_charge') * $userBlockedStatusCount;

            if ($request->payment_method == 'Deposit Balance') {
                if ($user->deposit_balance >= $user_blocked_instant_resolved_charge) {
                    $user->update([
                        'deposit_balance' => $user->deposit_balance - $user_blocked_instant_resolved_charge,
                    ]);
                } else {
                    return response()->json([
                        'status' => 401,
                        'message' => 'You do not have enough balance in your deposit balance.',
                    ]);
                }
            } else if ($request->payment_method == 'Withdraw Balance') {
                if ($user->withdraw_balance >= $user_blocked_instant_resolved_charge) {
                    $user->update([
                        'withdraw_balance' => $user->withdraw_balance - $user_blocked_instant_resolved_charge,
                    ]);
                } else {
                    return response()->json([
                        'status' => 401,
                        'message' => 'You do not have enough balance in your withdraw balance.',
                    ]);
                }
            }

            $user->update([
                'status' => 'Active',
            ]);

            $userStatus->update([
                'blocked_resolved_charge' => $user_blocked_instant_resolved_charge,
                'resolved_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            $userStatus = UserStatus::create([
                'user_id' => $user->id,
                'status' => 'Active',
                'reason' => 'Your account has been instantly unblocked successfully.',
                'resolved_at' => now(),
                'created_by' => Auth::id(),
            ]);

            $user->notify(new UserStatusNotification($userStatus));

            return response()->json([
                'status' => 200,
                'message' => 'Instant Unblocked request submitted successfully.',
            ]);
        }
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

                $totalDepositAmount = (clone $query)->sum('amount');

                $deposits = $query->get();

                return DataTables::of($deposits)
                    ->addIndexColumn()
                    ->editColumn('amount', function ($row) {
                        return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->amount . '</span>';
                    })
                    ->editColumn('method', function ($row) {
                        if ($row->method == 'Bkash') {
                            $method = '
                            <span class="badge bg-primary">' . $row->method . '</span>
                            ';
                        } else if ($row->method == 'Nagad') {
                            $method = '
                            <span class="badge bg-info">' . $row->method . '</span>
                            ';
                        } else {
                            $method = '
                            <span class="badge bg-secondary">' . $row->method . '</span>
                            ';
                        }
                        return $method;
                    })
                    ->editColumn('number', function ($row) {
                        return $row->number;
                    })
                    ->editColumn('transaction_id', function ($row) {
                        return $row->transaction_id;
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
                    ->with(['totalDepositAmount' => $totalDepositAmount])
                    ->rawColumns(['method', 'number', 'amount', 'created_at', 'approved_or_rejected_at', 'status'])
                    ->make(true);
            }

            return view('frontend.deposit.index');
        }
    }

    public function depositStore(Request $request)
    {
        $minDepositAmount = get_default_settings('min_deposit_amount');
        $maxDepositAmount = get_default_settings('max_deposit_amount');

        $validator = Validator::make($request->all(), [
            'method' => 'required|in:Bkash,Nagad,Rocket',
            'number' => ['string', 'required', 'regex:/^(?:\+8801|01)[3-9]\d{8}$/'],
            'transaction_id' => 'string|required|max:255',
            'amount' => "required|numeric|min:$minDepositAmount|max:$maxDepositAmount",
        ], [
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
                    'error'=> 'This transaction is already exist. Please try another transaction.',
                ]);
            }

            Deposit::create([
                'user_id' => $request->user()->id,
                'method' => $request->method,
                'number' => $request->number,
                'transaction_id' => $request->transaction_id,
                'amount' => $request->amount,
                'status' => 'Pending',
                'created_by' => $request->user()->id,
            ]);

            return response()->json([
                'status' => 200,
            ]);
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

                $totalWithdrawAmount = (clone $query)->sum('amount');

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
                            <span class="badge bg-secondary">' . $row->method . '</span>
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
                    ->with(['totalWithdrawAmount' => $totalWithdrawAmount])
                    ->rawColumns(['type', 'method', 'amount', 'payable_amount', 'created_at', 'number', 'approved_or_rejected_at', 'status'])
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
            'method' => 'required|in:Bkash,Nagad,Rocket',
            'number' => ['required', 'string', 'regex:/^(?:\+8801|01)[3-9]\d{8}$/'],
            'amount' => "required|numeric|min:$minWithdrawAmount|max:$maxWithdrawAmount",
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
            'method' => $request->method,
            'number' => $request->number,
            'amount' => $request->amount,
            'payable_amount' => $payableAmount,
            'status' => 'Pending',
            'created_by' => $request->user()->id,
        ]);

        $request->user()->decrement('withdraw_balance', $request->amount);

        return response()->json([
            'status' => 200,
            'withdraw_balance' => number_format($request->user()->withdraw_balance, 2, '.', ''),
        ]);
    }

    // Transfer.............................................................................................................

    public function balanceTransfer(Request $request)
    {
        if ($request->ajax()) {
            $query = BalanceTransfer::where('user_id', Auth::id());

            $query->select('balance_transfers.*')->orderBy('created_at', 'desc');

            if ($request->send_method) {
                $query->where('balance_transfers.send_method', $request->send_method);
            }

            if ($request->receive_method) {
                $query->where('balance_transfers.receive_method', $request->receive_method);
            }

            $totalBalanceTransferAmount = (clone $query)->sum('amount');

            $balance_transfers = $query->get();

            return DataTables::of($balance_transfers)
                ->addIndexColumn()
                ->editColumn('send_method', function ($row) {
                    if ($row->send_method == 'Deposit Balance') {
                        $send_method = '<span class="badge bg-info">' . $row->send_method . '</span>';
                    } else {
                        $send_method = '<span class="badge bg-primary">' . $row->send_method . '</span>';
                    }
                    return $send_method;
                })
                ->editColumn('receive_method', function ($row) {
                    if ($row->receive_method == 'Deposit Balance') {
                        $receive_method = '<span class="badge bg-info">' . $row->receive_method . '</span>';
                    } else {
                        $receive_method = '<span class="badge bg-primary">' . $row->receive_method . '</span>';
                    }
                    return $receive_method;
                })
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
                ->with(['totalBalanceTransferAmount' => $totalBalanceTransferAmount])
                ->rawColumns(['send_method', 'receive_method', 'amount', 'payable_amount', 'created_at', 'approved_at'])
                ->make(true);
        }

        return view('frontend.balance_transfer.index');
    }

    public function balanceTransferStore(Request $request)
    {
        $currencySymbol = get_site_settings('site_currency_symbol');
        $depositChargePercentage = get_default_settings('deposit_balance_transfer_charge_percentage');
        $withdrawChargePercentage = get_default_settings('withdraw_balance_transfer_charge_percentage');

        $validator = Validator::make($request->all(), [
            'send_method' => 'required|in:Deposit Balance,Withdraw Balance',
            'receive_method' => 'required|in:Deposit Balance,Withdraw Balance',
            'amount' => 'required|numeric|min:1',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            if ($request->send_method == 'Deposit Balance') {
                if ($request->amount > $request->user()->deposit_balance) {
                    return response()->json([
                        'status' => 401,
                        'error'=> 'Insufficient balance in your account to transfer balance ' . $currencySymbol .' '. $request->amount .
                                '. Your current balance is ' . $currencySymbol .' '. $request->user()->deposit_balance
                    ]);
                } else {
                    $payable_amount = $request->amount - ($request->amount * $depositChargePercentage / 100);

                    $request->user()->decrement('deposit_balance', $request->amount);
                    $request->user()->increment('withdraw_balance', $payable_amount);
                }
            } else {
                if ($request->amount > $request->user()->withdraw_balance) {
                    return response()->json([
                        'status' => 401,
                        'error'=> 'Insufficient balance in your account to transfer balance ' . $currencySymbol .' '. $request->amount .
                                '. Your current balance is ' . $currencySymbol .' '. $request->user()->withdraw_balance
                    ]);
                } else {
                    $payable_amount = $request->amount - ($request->amount * $withdrawChargePercentage / 100);

                    $request->user()->decrement('withdraw_balance', $request->amount);
                    $request->user()->increment('deposit_balance', $payable_amount);
                }
            }

            BalanceTransfer::create([
                'user_id' => $request->user()->id,
                'send_method' => $request->send_method,
                'receive_method' => $request->receive_method,
                'amount' => $request->amount,
                'payable_amount' => $payable_amount,
                'created_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 200,
                'deposit_balance' => number_format($request->user()->deposit_balance, 2, '.', ''),
                'withdraw_balance' => number_format($request->user()->withdraw_balance, 2, '.', ''),
            ]);
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

                // sum of total bonuses
                $totalBonusAmount = (clone $query)->sum('amount');

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
                            $bonus_by = '<span class="badge bg-primary">Site</span>';
                        }
                        return $bonus_by;
                    })
                    ->editColumn('bonus_by_user_name', function ($row) {
                        if ($row->type == 'Proof Task Approved Bonus') {
                            if ($row->bonusBy->deleted_at == null) {
                                $bonus_by_user_name = '
                                <a href="'.route('user.profile', encrypt($row->bonusBy->id)).'" title="'.$row->bonusBy->name.'" class="text-info">
                                    '.$row->bonusBy->name.'
                                </a>
                                ';
                            } else {
                                $bonus_by_user_name = '<span class="badge bg-primary">' . $row->bonusBy->name . '</span>';
                            }
                        } else {
                            $bonus_by_user_name = '<span class="badge bg-primary">Site</span>';
                        }

                        return $bonus_by_user_name;
                    })
                    ->editColumn('amount', function ($row) {
                        return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->amount . '</span>';
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->with([
                        'totalBonusAmount' => $totalBonusAmount,
                    ])
                    ->rawColumns(['type', 'bonus_by_user_name', 'bonus_by', 'amount', 'created_at'])
                    ->make(true);
            }

            return view('frontend.bonus.index');
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

    // Favorite.............................................................................................................

    public function favoriteUserList(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $favoriteUsers = FavoriteUser::where('user_id', Auth::id());

                $query = $favoriteUsers->select('favorite_users.*');

                // Total filtered count
                $totalFavoritesCount = $query->count();

                $favoriteList = $query->get();

                return DataTables::of($favoriteList)
                    ->addIndexColumn()
                    ->editColumn('favorite_user', function ($row) {
                        if ($row->favoriteUser->deleted_at == null) {
                            $favoriteUser = '
                            <a href="'.route('user.profile', encrypt($row->favoriteUser->id)).'" title="'.$row->favoriteUser->name.'" class="text-info">
                                '.$row->favoriteUser->name.'
                            </a>
                            ';
                        } else {
                            $favoriteUser = '<span class="badge bg-primary">' . $row->favoriteUser->name . '</span>';
                        }
                        return $favoriteUser;
                    })
                    ->editColumn('created_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->created_at));
                    })
                    ->addColumn('action', function ($row) {
                        $action = '
                        <a href="'.route('unfavorite.user', $row->favoriteUser->id).'" title="Unfavorite" class="btn btn-danger btn-sm">
                            Unfavorite
                        </a>
                        ';
                        return $action;
                    })
                    ->with(['totalFavoritesCount' => $totalFavoritesCount])
                    ->rawColumns(['favorite_user', 'action'])
                    ->make(true);
            }
            return view('frontend.favorite_list.index');
        }
    }

    public function favoriteUser($id)
    {
        $favorite = FavoriteUser::where('user_id', Auth::id())->where('favorite_user_id', $id)->exists();

        if ($favorite) {
            $notification = array(
                'message' => 'User already favorite.',
                'alert-type' => 'info'
            );

            return back()->with($notification);
        }

        FavoriteUser::create([
            'user_id' => Auth::id(),
            'favorite_user_id' => $id,
        ]);

        $notification = array(
            'message' => 'User favorite successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    public function unfavoriteUser($id)
    {
        $favorite = FavoriteUser::where('user_id', Auth::id())->where('favorite_user_id', $id)->exists();

        if (!$favorite) {
            $notification = array(
                'message' => 'User already unfavorite.',
                'alert-type' => 'info'
            );

            return back()->with($notification);
        }

        FavoriteUser::where('user_id', Auth::id())->where('favorite_user_id', $id)->delete();

        $notification = array(
            'message' => 'User unfavorite successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    // Blocked.............................................................................................................

    public function blockedUserList(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $blockedUsers = BlockedUser::where('user_id', Auth::id());

                $query = $blockedUsers->select('blocked_users.*');

                // Total filtered count
                $totalBlockedsCount = $query->count();

                $blockedList = $query->get();

                return DataTables::of($blockedList)
                    ->addIndexColumn()
                    ->editColumn('blocked_user', function ($row) {
                        if ($row->blockedUser->deleted_at == null) {
                            $blockedUser = '
                            <a href="'.route('user.profile', encrypt($row->blockedUser->id)).'" title="'.$row->blockedUser->name.'" class="text-info">
                                '.$row->blockedUser->name.'
                            </a>
                            ';
                        } else {
                            $blockedUser = '<span class="badge bg-primary">' . $row->blockedUser->name . '</span>';
                        }
                        return $blockedUser;
                    })
                    ->editColumn('created_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->created_at));
                    })
                    ->addColumn('action', function ($row) {
                        $action = '
                        <a href="'.route('unblocked.user', $row->blockedUser->id).'" title="Unblocked" class="btn btn-danger btn-sm">
                            Unblocked
                        </a>
                        ';
                        return $action;
                    })
                    ->with(['totalBlockedsCount' => $totalBlockedsCount])
                    ->rawColumns(['blocked_user', 'action'])
                    ->make(true);
            }
            return view('frontend.blocked_list.index');
        }
    }

    public function blockedUser($id)
    {
        $blocked = BlockedUser::where('user_id', Auth::id())->where('blocked_user_id', $id)->exists();

        if ($blocked) {
            $notification = array(
                'message' => 'User already blocked.',
                'alert-type' => 'success'
            );

            return back()->with($notification);
        }

        BlockedUser::create([
            'user_id' => Auth::id(),
            'blocked_user_id' => $id,
        ]);

        $notification = array(
            'message' => 'User blocked successfully.',
            'alert-type' => 'error'
        );

        return back()->with($notification);
    }

    public function unblockedUser($id)
    {
        $blocked = BlockedUser::where('user_id', Auth::id())->where('blocked_user_id', $id)->exists();

        if (!$blocked) {
            $notification = array(
                'message' => 'User already unblocked.',
                'alert-type' => 'success'
            );

            return back()->with($notification);
        }

        BlockedUser::where('user_id', Auth::id())->where('blocked_user_id', $id)->delete();

        $notification = array(
            'message' => 'User unblocked successfully.',
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
                        if ($row->reported->deleted_at == null) {
                            $reported = '
                            <a href="'.route('user.profile', encrypt($row->reported->id)).'" title="'.$row->reported->name.'" class="text-info">
                                '.$row->reported->name.'
                            </a>
                            ';
                        } else {
                            $reported = '<span class="badge bg-primary">' . $row->reported->name . '</span>';
                        }
                        return $reported;
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
