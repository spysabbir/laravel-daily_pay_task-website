<?php

use App\Models\Newsletter;
use Illuminate\Support\Facades\Schedule;
use App\Mail\NewsletterMail;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

// Schedule::command('disposable:update')->weekly();
// Schedule::command('queue:work')->everySecond();

Schedule::call(function () {
    $newsletters = Newsletter::where('status', 'Draft')
        ->get();

    foreach ($newsletters as $newsletter) {
        if ($newsletter->sent_at <= date('Y-m-d H:i:s', strtotime(now()))) {
            $newsletter->update(['status' => 'Sent']);
        }

        $recipients = $newsletter->mail_type == 'Subscriber'
                ? Subscriber::where('status', 'Active')->pluck('email')
                : User::where('status', 'Active')->pluck('email');

            foreach ($recipients as $email) {
                Mail::to($email)->queue(new NewsletterMail($newsletter));
            }
    }

})->everyMinute();
