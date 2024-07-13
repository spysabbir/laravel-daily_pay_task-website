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
            'captcha_site_key' => '6LfrZwoqAAAAAFzI1W03nmwpeL88Jt8tJpE3aM7R',
            'captcha_secret_key' => '6LfrZwoqAAAAAFzI1W03nmwpeL88Jt8tJpE3aM7R'
        ];

        CaptchaSetting::create($setting);

        $this->command->info('Captcha settings added successfully.');

        return;
    }
}
