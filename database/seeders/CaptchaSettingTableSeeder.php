<?php

namespace Database\Seeders;

use App\Models\CaptchaSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CaptchaSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = [
            'captcha_site_key' => 'captcha_site_key',
            'captcha_secret_key' => 'captcha_site_key'
        ];

        CaptchaSetting::create($setting);

        $this->command->info('Captcha settings added successfully.');

        return;
    }
}
