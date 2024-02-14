<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = [
            'id' => 1,
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('@Dmin.135'),
            'created_by' => 1,
        ];

        $user = User::query()->updateOrCreate(['id' => $user['id'], 'email' => $user['email']], $user);
        $role = Role::query()->findOrFail(1);
        $user->roles()->sync($role);
    }
}
