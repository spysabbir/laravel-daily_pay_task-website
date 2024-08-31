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
            'referal_registion_bonus_amount' => 10.00,
            'referal_earning_bonus_percentage' => 2.00,
            'deposit_bkash_account' => '01700000000',
            'deposit_rocket_account' => '01700000000',
            'deposit_nagad_account' => '01700000000',
            'min_deposit_amount' => 100.00,
            'max_deposit_amount' => 10000.00,
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
            'user_max_blocked_time' => 7,
        ];

        DefaultSetting::create($setting);

        $this->command->info('Default settings added successfully.');

        return;
    }
}
