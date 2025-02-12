<?php

namespace Database\Seeders;

use App\Models\SeoSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeoSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = [
            'title' => 'Daily Pay Task - Your Effort, Your Reward',
            'author' => 'Spy Sabbir',
            'keywords' => 'Daily Pay Task, Daily Pay, Daily Task, Pay Daily, Pay Task, Task Pay, Task Daily, Earn Money, Earn Money Online',
            'description' => 'Earn daily by completing quick micro-tasks. Get paid instantly for your time and effort. Join now and turn your spare moments into real cash!',
            'image' => 'default_seo_image.png',
            'image_alt' => 'Daily Pay Task - Your Effort, Your Reward',
        ];

        SeoSetting::create($setting);

        $this->command->info('SEO settings added successfully.');

        return;
    }
}
