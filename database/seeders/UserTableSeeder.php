<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Verification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('Ss@12345678'),
            'user_type' => 'Backend',
            'status' => 'Active',
        ]);

        $role = Role::create(['name' => 'Super Admin']);

        $permissions = Permission::pluck('id','id')->all();

        $role->syncPermissions($permissions);

        $superAdmin->assignRole([$role->id]);

        $user = User::create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'deposit_balance' => 10000,
            'withdraw_balance' => 10000,
            'password' => Hash::make('Ss@12345678'),
            'email_verified_at' => now(),
            'user_type' => 'Frontend',
            'status' => 'Active',
        ]);

        Verification::create([
            'user_id' => $user->id,
            'id_type' => 'NID',
            'id_number' => 12345678,
            'id_front_image' => 'id_front_image.jpg',
            'id_with_face_image' => 'id_with_face_image.jpg',
            'status' => 'Approved',
            'approved_by' => $superAdmin->id,
            'approved_at' => now(),
        ]);

        $this->command->info('User added successfully.');

        return;
    }
}
