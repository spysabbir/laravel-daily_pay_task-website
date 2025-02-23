<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Verification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@spysabbir.com',
            'date_of_birth' => '2025-01-01',
            'gender' => 'Male',
            'password' => Hash::make('Ss@12345678'),
            'user_type' => 'Backend',
            'status' => 'Active',
        ]);

        $role = Role::create(['name' => 'Super Admin']);

        $permissions = Permission::pluck('id','id')->all();

        $role->syncPermissions($permissions);

        $superAdmin->assignRole([$role->id]);

        $user = User::create([
            'name' => 'Spy Sabbir',
            'email' => 'user@spysabbir.com',
            'date_of_birth' => '2025-01-01',
            'gender' => 'Male',
            'deposit_balance' => 10000,
            'withdraw_balance' => 10000,
            'password' => Hash::make('Ss@12345678'),
            'email_verified_at' => now(),
            'user_type' => 'Frontend',
            'referral_code' => Str::random(12),
            'status' => 'Active',
        ]);

        Verification::create([
            'user_id' => $user->id,
            'id_type' => 'NID',
            'id_number' => '0123456789',
            'id_front_image' => 'id_front_image.jpg',
            'id_with_face_image' => 'id_with_face_image.jpg',
            'status' => 'Approved',
            'approved_by' => 1,
            'approved_at' => now(),
        ]);

        $this->command->info('User added successfully.');

        return;
    }
}
