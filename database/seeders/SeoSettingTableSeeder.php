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
            'author' => 'Spy Sabbir',
            'keywords' => 'Daily Pay Task',
            'title' => 'Turn Your Spare Time into Profit',
            'description' => 'Daily Pay Task - Turn Your Spare Time into Profit',

            'og_site_name' => 'Daily Pay Task',
            'og_url' => 'https://dailypaytask.com',

            'twitter_card' => 'Daily Pay Task',
            'twitter_site' => 'Daily Pay Task',

            'image' => 'default_seo_image.jpg',
            'image_alt' => 'seo_iamge',
        ];

        SeoSetting::create($setting);

        $this->command->info('SEO settings added successfully.');

        return;
    }
}
