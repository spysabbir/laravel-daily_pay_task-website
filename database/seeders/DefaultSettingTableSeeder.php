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
            'site_name' => 'Daily Earnings',
            'site_url' => 'http://127.0.0.1:8000',
            'site_timezone' => 'Asia/Dhaka',
            'site_currency' => 'BDT',
            'site_currency_symbol' => '৳',
            'site_logo' => 'default_site_logo.png',
            'site_favicon' => 'default_site_favicon.png',
            'site_main_email' => 'info@gmail.com',
            'site_support_email' => 'support@gmail.com',
            'site_main_phone' => '1234567890',
            'site_support_phone' => '1234567890',
            'site_address' => 'Dhaka, Bangladesh',
            'referal_registion_bonus_amount' => 10.00,
            'referal_earning_bonus_percentage' => 2.00,
            'min_deposit_amount' => 100.00,
            'max_deposit_amount' => 10000.00,
            'instant_withdraw_charge' => 10.00,
            'withdraw_charge_percentage' => 20.00,
            'min_withdraw_amount' => 500.00,
            'max_withdraw_amount' => 10000.00,
            'site_notice' => 'Welcome to our website.',
            'site_facebook' => 'https://facebook.com',
            'site_twitter' => 'https://twitter.com',
            'site_instagram' => 'https://instagram.com',
            'site_linkedin' => 'https://linkedin.com',
            'site_pinterest' => 'https://pinterest.com',
            'site_youtube' => 'https://youtube.com',
            'site_whatsapp' => 'https://whatsapp.com',
            'site_telegram' => 'https://telegram.com',
            'site_tiktok' => 'https://tiktok.com',
        ];

        DefaultSetting::create($setting);

        $this->command->info('Default settings added successfully.');

        return;
    }
}
