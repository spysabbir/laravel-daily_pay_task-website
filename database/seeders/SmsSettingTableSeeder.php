<?php

namespace Database\Seeders;

use App\Models\SmsSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SmsSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = [
            'sms_driver' => 'sms_driver',
            'sms_api_key' => 'sms_api_key',
            'sms_send_from' => 'sms_send_from',
        ];

        SmsSetting::create($setting);

        $this->command->info('SMS settings added successfully.');

        return;
    }
}
