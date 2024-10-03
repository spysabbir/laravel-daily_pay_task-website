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
            'referral_registration_bonus_amount' => 10.00,
            'referral_withdrawal_bonus_percentage' => 2.00,
            'deposit_bkash_account' => '01700000000',
            'deposit_rocket_account' => '01800000000',
            'deposit_nagad_account' => '01900000000',
            'min_deposit_amount' => 100.00,
            'max_deposit_amount' => 10000.00,
            'withdrawal_balance_deposit_charge_percentage' => 2.00,
            'instant_withdraw_charge' => 10.00,
            'withdraw_charge_percentage' => 20.00,
            'min_withdraw_amount' => 500.00,
            'max_withdraw_amount' => 10000.00,
            'job_posting_charge_percentage' => 5.00,
            'job_posting_additional_screenshot_charge' => 2.50,
            'job_posting_boosted_time_charge' => 5.00,
            'job_posting_additional_running_day_charge' => 2.50,
            'job_posting_min_budget' => 100.00,
            'max_job_proof_bonus_amount' => 20.00,
            'job_proof_monthly_free_review_time' => 30,
            'job_proof_additional_review_charge' => 0.25,
            'job_proof_status_auto_approved_time' => 72,
            'job_proof_status_rejected_charge_auto_refund_time' => 72,
            'user_max_blocked_time' => 3,
        ];

        DefaultSetting::create($setting);

        $this->command->info('Default settings added successfully.');

        return;
    }
}
