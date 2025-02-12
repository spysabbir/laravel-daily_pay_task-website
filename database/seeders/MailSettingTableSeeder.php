<?php

namespace Database\Seeders;

use App\Models\MailSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MailSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = [
            'mail_driver' => 'mail_driver',
            'mail_mailer' => 'mail_mailer',
            'mail_host' => 'mail_host',
            'mail_port' => 'mail_port',
            'mail_username' => 'mail_username',
            'mail_password' => 'mail_password',
            'mail_encryption' => 'mail_encryption',
            'mail_from_address' => 'mail_from_address',
        ];

        MailSetting::create($setting);

        $this->command->info('Mail settings added successfully.');

        return;
    }
}
