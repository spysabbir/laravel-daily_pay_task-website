<?php

namespace Database\Seeders;

use App\Models\DefaultSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = [
            'referral_registration_bonus_amount' => 10.00, // every referral
            'referral_withdrawal_bonus_percentage' => 2.00, // every withdrawal
            'deposit_bkash_account' => '01700000000',
            'deposit_rocket_account' => '01800000000',
            'deposit_nagad_account' => '01900000000',
            'min_deposit_amount' => 100.00, // every deposit
            'max_deposit_amount' => 10000.00, // every deposit
            'deposit_balance_from_withdraw_balance_charge_percentage' => 2.00, // every deposit
            'withdraw_balance_from_deposit_balance_charge_percentage' => 2.00, // every withdraw
            'instant_withdraw_charge' => 10.00, // every withdraw
            'withdraw_charge_percentage' => 20.00, // every withdraw
            'min_withdraw_amount' => 500.00, // every withdraw
            'max_withdraw_amount' => 10000.00, // every withdraw
            'task_posting_charge_percentage' => 5.00, // every task
            'task_posting_additional_required_proof_photo_charge' => 2.50, // every required proof photo
            'task_posting_boosting_time_charge' => 5.00, // every 15 minutes
            'task_posting_additional_work_duration_charge' => 2.50, // every day
            'task_posting_min_budget' => 100.00, // every task
            'posted_task_proof_submit_user_max_bonus_amount' => 20.00, // every proof
            'posted_task_proof_submit_auto_approved_time' => 72, // hours
            'posted_task_proof_submit_rejected_charge_auto_refund_time' => 24, // hours
            'rejected_worked_task_review_charge' => 0.25, // every review
            'user_max_blocked_time_for_banned' => 3, // every banned
        ];

        DefaultSetting::create($setting);

        $this->command->info('Default settings added successfully.');

        return;
    }
}
