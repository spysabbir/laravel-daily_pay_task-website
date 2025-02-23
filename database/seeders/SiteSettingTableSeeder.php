<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SiteSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = [
            'site_name' => 'Daily Pay Task',
            'site_slogan' => 'Your Effort, Your Reward',
            'site_description' => 'Earn daily by completing quick micro-tasks. Get paid instantly for your time and effort. Join now and turn your spare moments into real cash!',
            'site_url' => 'https://dailypaytask.com',
            'site_timezone' => 'Asia/Dhaka',
            'site_currency' => 'BDT',
            'site_currency_symbol' => '৳',
            'site_logo' => 'default_site_logo.png',
            'site_favicon' => 'default_site_favicon.png',
            'site_main_email' => 'info@dailypaytask.com',
            'site_support_email' => 'support@dailypaytask.com',
            'site_main_phone' => '01883714846',
            'site_support_phone' => '01883714846',
            'site_address' => 'Dhaka, Bangladesh',
            'site_notice' => 'Submit only authentic evidence. Your account may be suspended if you submit false or group work evidence. Thanks for your cooperation in keeping our community fair and trustworthy.',
            'site_facebook_url' => 'https://www.facebook.com/dailypaytask',
            'site_twitter_url' => 'https://x.com/dailypaytask',
            'site_instagram_url' => 'https://www.instagram.com/dailypaytask/',
            'site_linkedin_url' => 'https://www.pinterest.com/dailypaytask',
            'site_pinterest_url' => 'https://www.pinterest.com/dailypaytask',
            'site_youtube_url' => 'https://www.youtube.com/@DailyPayTask',
            'site_whatsapp_url' => 'https://whatsapp.com',
            'site_telegram_url' => 'https://t.me/dailypaytask_support',
            'site_tiktok_url' => 'https://www.tiktok.com/@dailypaytask',
        ];

        SiteSetting::create($setting);

        $this->command->info('Site settings added successfully.');

        return;
    }
}
