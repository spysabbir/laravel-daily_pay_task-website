<?php

use App\Models\Newsletter;
use Illuminate\Support\Facades\Schedule;
use App\Mail\NewsletterMail;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\UserStatus;
use Illuminate\Support\Facades\Mail;
use App\Notifications\UserStatusNotification;


Schedule::call(function () {
    $newsletters = Newsletter::where('status', 'Draft')
        ->where('sent_at', '<=', now())
        ->get();

    foreach ($newsletters as $newsletter) {
        $newsletter->update(['status' => 'Sent']);

        $recipients = $newsletter->mail_type == 'Subscriber'
            ? Subscriber::where('status', 'Active')->pluck('email')
            : User::where('status', 'Active')->pluck('email');

        if ($recipients->isNotEmpty()) {
            foreach ($recipients as $email) {
                Mail::to($email)->queue(new NewsletterMail($newsletter));
            }
        }
    }
})->everyMinute();

Schedule::call(function () {
    $user_statuses = UserStatus::where('status', 'Blocked')
        ->where('blocked_resolved', null)
        ->where('blocked_duration', '<=', now())
        ->get();

    foreach ($user_statuses as $userStatus) {
        $userStatus->update(['blocked_resolved' => now()]);

        $user = User::find($userStatus->user_id);
        $user->update(['status' => 'Active']);

        $userStatus = [
            'status' => 'Active',
            'reason' => 'Your account has been unblocked successfully!',
            'blocked_duration' => null,
            'created_at' => now(),
        ];

        $user->notify(new UserStatusNotification($userStatus));
    }
})->everyMinute();
