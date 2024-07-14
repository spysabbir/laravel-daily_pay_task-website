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
            'meta_title' => 'Daily Earnings BD',
            'meta_author' => 'Laravel',
            'meta_keywords' => 'Daily Earnings BD',
            'meta_description' => 'Daily Earnings BD',

            'og_title' => 'Daily Earnings BD',
            'og_type' => 'Laravel',
            'og_url' => 'Laravel',
            'og_image' => 'default_og_image.png',
            'og_description' => 'Daily Earnings BD',
            'og_site_name' => 'Laravel',

            'twitter_card' => 'Laravel',
            'twitter_site' => 'Laravel',
            'twitter_title' => 'Daily Earnings BD',
            'twitter_description' => 'Daily Earnings BD',
            'twitter_image' => 'default_twitter_image.png',
            'twitter_image_alt' => 'Daily Earnings BD',
        ];

        SeoSetting::create($setting);

        $this->command->info('SEO settings added successfully.');

        return;
    }
}
