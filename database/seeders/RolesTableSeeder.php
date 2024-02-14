<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;

class RolesTableSeeder extends BaseSeeder
{
    public function run(): void
    {
        $permissions = Permission::query()->select('id')->get();

        $role = [
            'id' => 1,
            'name' => 'super-admin',
            'description' => 'Super Admin',
        ];

        $role = Role::query()->updateOrCreate(['id' => $role['id'], 'name' => $role['name']], $role);

        $role->permissions()->sync($permissions);
    }
}
