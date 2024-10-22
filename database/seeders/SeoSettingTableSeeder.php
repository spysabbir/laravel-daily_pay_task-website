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
            'keywords' => 'Daily Micro Tasks',
            'title' => 'Turn Your Spare Time into Profit',
            'description' => 'Daily Micro Tasks - Turn Your Spare Time into Profit',

            'og_site_name' => 'Daily Micro Tasks',
            'og_url' => 'https://dailymicrotasks.com',

            'twitter_card' => 'Daily Micro Tasks',
            'twitter_site' => 'Daily Micro Tasks',

            'image' => 'default_seo_image.jpg',
            'image_alt' => 'seo_iamge',
        ];

        SeoSetting::create($setting);

        $this->command->info('SEO settings added successfully.');

        return;
    }
}
