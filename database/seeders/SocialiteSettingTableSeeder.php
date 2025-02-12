<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SocialiteSetting;

class SocialiteSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = [
            'google_client_id' => 'google_client_id',
            'google_client_secret' => 'google_client_secret',
            'facebook_client_id' => 'facebook_client_id',
            'facebook_client_secret' => 'facebook_client_secret',
        ];

        SocialiteSetting::create($setting);

        $this->command->info('Socialite setting seeded successfully.');

        return;
    }
}
