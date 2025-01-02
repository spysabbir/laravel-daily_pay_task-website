<?php

use App\Models\Newsletter;
use Illuminate\Support\Facades\Schedule;
use App\Mail\NewsletterMail;
use App\Models\NotInterestedTask;
use App\Models\ProofTask;
use App\Models\PostTask;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\UserStatus;
use Illuminate\Support\Facades\Mail;
use App\Notifications\UserStatusNotification;
use Carbon\Carbon;

// Send newsletters to subscribers and users
Schedule::call(function () {
    $now = now();
    $newsletters = Newsletter::where('status', 'Draft')
        ->where('sent_at', '<=', $now)
        ->get();

    foreach ($newsletters as $newsletter) {
        $newsletter->update(['status' => 'Sent']);

        $recipients = $newsletter->mail_type == 'Subscriber'
            ? Subscriber::where('status', 'Active')->pluck('email')
            : User::where('status', 'Active')->pluck('email');

        if ($recipients->isNotEmpty()) {
            Mail::to($recipients)->queue(new NewsletterMail($newsletter));
        }
    }
})->everyMinute();

// Unblock users after blocked duration
Schedule::call(function () {
    $now = now();
    $userStatuses = UserStatus::where('status', 'Blocked')
        ->where('blocked_resolved', null)
        ->get();

    foreach ($userStatuses as $userStatus) {

        $activeTime = Carbon::parse($userStatus->created_at)->addHours((int) $userStatus->blocked_duration);

        if ($now->isSameMinute($activeTime)) {
            $userStatus->update(['blocked_resolved' => $now]);

            $user = User::find($userStatus->user_id);
            if ($user) {
                $user->update(['status' => 'Active']);
                $user->notify(new UserStatusNotification([
                    'status' => 'Active',
                    'reason' => 'Your account has been unblocked successfully!',
                    'blocked_duration' => null,
                    'created_at' => $now,
                ]));
            }
        }
    }
})->everyMinute();

// Task proof status update to Approved
Schedule::call(function () {
    $now = now();
    $autoApproveTimeInHours = get_default_settings('posted_task_proof_submit_auto_approved_time');
    $proofTasks = ProofTask::where('status', 'Pending')->get();

    foreach ($proofTasks as $proofTask) {
        $approvalTime = Carbon::parse($proofTask->created_at)->addHours((int) $autoApproveTimeInHours);

        if ($now->isSameMinute($approvalTime)) {
            $proofTask->update([
                'status' => 'Approved',
                'approved_at' => $now,
                'approved_by' => 1,
            ]);

            $postTask = PostTask::find($proofTask->post_task_id);
            if ($postTask) {
                User::where('id', $proofTask->user_id)->increment('withdraw_balance', $postTask->income_of_each_worker);
            }
        }
    }
})->everyMinute();

// Task proof status update to Completed
Schedule::call(function () {
    $postTasks = PostTask::where('status', 'Running')->get();

    foreach ($postTasks as $postTask) {
        $proofTasks = ProofTask::where('post_task_id', $postTask->id)->count();

        if ($postTask->worker_needed == $proofTasks) {
            $postTask->update([
                'status' => 'Completed',
                'completed_at' => now(),
            ]);
        }
    }
})->everyMinute();

// Task proof status update to Canceled
Schedule::call(function () {
    $postTasks = PostTask::where('status', 'Running')->get();

    foreach ($postTasks as $postTask) {
        $canceledTime = Carbon::parse($postTask->approved_at)->addDays((int) $postTask->work_duration);

        if (now()->isSameMinute($canceledTime)) {
            $postTask->update([
                'status' => 'Canceled',
                'cancellation_reason' => 'Work duration exceeded',
                'canceled_by' => 1,
                'canceled_at' => now(),
            ]);

            $proofTasks = ProofTask::where('post_task_id', $postTask->id)->count();

            if ($proofTasks == 0) {
                User::where('id', $postTask->user_id)->increment('deposit_balance', $postTask->total_cost);
            } else {
                $refundAmount = ((($postTask->sub_cost + $postTask->site_charge) / $postTask->worker_needed) * ($postTask->worker_needed - $proofTasks));
                User::where('id', $postTask->user_id)->increment('deposit_balance', $refundAmount);
            }
        }
    }
})->everyMinute();

// Task proof status Rejected refund to task owner
Schedule::call(function () {
    $now = now();
    $autoRefundTimeInHours = get_default_settings('posted_task_proof_submit_rejected_charge_auto_refund_time');
    $proofTasks = ProofTask::where('status', 'Rejected')->where('reviewed_at', null)->get();

    foreach ($proofTasks as $proofTask) {
        $refundTime = Carbon::parse($proofTask->rejected_at)->addHours((int) $autoRefundTimeInHours);

        if ($now->isSameMinute($refundTime)) {
            $postTask = PostTask::find($proofTask->post_task_id);
            if ($postTask) {
                $refundAmount = (($postTask->sub_cost + $postTask->site_charge) / $postTask->worker_needed);
                User::where('id', $postTask->user_id)->increment('deposit_balance', $refundAmount);
            }
        }
    }
})->everyMinute();

// Not interested tasks auto delete
Schedule::call(function () {
    $postTasks = PostTask::whereIn('status', ['Canceled', 'Completed'])->get();

    foreach ($postTasks as $postTask) {
        $not_interested_tasks = NotInterestedTask::where('post_task_id', $postTask->id)->get();

        foreach ($not_interested_tasks as $not_interested_task) {
            $not_interested_task->delete();
        }
    }
})->everyMinute();
