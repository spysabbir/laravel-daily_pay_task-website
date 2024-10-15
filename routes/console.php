<?php

use App\Models\Newsletter;
use Illuminate\Support\Facades\Schedule;
use App\Mail\NewsletterMail;
use App\Models\ProofTask;
use App\Models\PostTask;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\UserStatus;
use Illuminate\Support\Facades\Mail;
use App\Notifications\UserStatusNotification;
use Carbon\Carbon;

// Send newsletters
// Schedule::call(function () {
//     $newsletters = Newsletter::where('status', 'Draft')
//         ->where('sent_at', '<=', now())
//         ->get();

//     foreach ($newsletters as $newsletter) {
//         $newsletter->update(['status' => 'Sent']);

//         $recipients = $newsletter->mail_type == 'Subscriber'
//             ? Subscriber::where('status', 'Active')->pluck('email')
//             : User::where('status', 'Active')->pluck('email');

//         if ($recipients->isNotEmpty()) {
//             foreach ($recipients as $email) {
//                 Mail::to($email)->queue(new NewsletterMail($newsletter));
//             }
//         }
//     }
// })->everyMinute();

// Unblock users
// Schedule::call(function () {
//     $user_statuses = UserStatus::where('status', 'Blocked')
//         ->where('blocked_resolved', null)
//         ->where('blocked_duration', '<=', now())
//         ->get();

//     foreach ($user_statuses as $userStatus) {
//         $userStatus->update(['blocked_resolved' => now()]);

//         $user = User::find($userStatus->user_id);
//         $user->update(['status' => 'Active']);

//         $userStatus = [
//             'status' => 'Active',
//             'reason' => 'Your account has been unblocked successfully!',
//             'blocked_duration' => null,
//             'created_at' => now(),
//         ];

//         $user->notify(new UserStatusNotification($userStatus));
//     }
// })->everyMinute();

// Task proof status update to Approved
// Schedule::call(function () {
//     $autoApproveTimeInHours = get_default_settings('task_proof_status_auto_approved_time');
//     $proofTasks = ProofTask::where('status', 'Pending')->get();

//     $now = now();

//     foreach ($proofTasks as $proofTask) {
//         $approvalTime = $proofTask->created_at->copy()->addHours($autoApproveTimeInHours);

//         if ($approvalTime->isPast()) {
//             $proofTask->update([
//                 'status' => 'Approved',
//                 'approved_at' => $now,
//                 'approved_by' => 1,
//             ]);

//             $postTask = PostTask::find($proofTask->post_task_id);
//             if ($postTask) {
//                 User::where('id', $proofTask->user_id)->increment('withdraw_balance', $postTask->earnings_from_work);
//             }
//         }
//     }
// })->everyMinute();

// Task proof status Rejected refund to task owner
Schedule::call(function () {
    $autoRefundTimeInHours = get_default_settings('task_proof_status_rejected_charge_auto_refund_time');
    $proofTasks = ProofTask::where('status', 'Rejected')->where('reviewed_at', null)->get();

    foreach ($proofTasks as $proofTask) {
        $refundTime = Carbon::parse($proofTask->rejected_at)->addHours(1);

        if (now()->isSameMinute($refundTime)) {
            User::where('id', 2)->decrement('deposit_balance', 5);
        }
    }
})->everyMinute();

