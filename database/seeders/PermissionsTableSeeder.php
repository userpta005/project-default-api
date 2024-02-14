<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;

class PermissionsTableSeeder extends BaseSeeder
{
    public function run(): void
    {
        $permissions = Permission::permissions;
        $permissionsToSave = [];

        $loop = function ($permissions, $parent = null) use (&$loop, &$permissionsToSave) {
            foreach ($permissions as $permission) {
                if (array_key_exists('id', $permission)) {
                    $permissionData = [
                        'id' => $permission['id'],
                        'name' => $permission['name'],
                        'description' => $permission['description'],
                        'parent_id' => $parent ? $parent['id'] : null,
                    ];
                    array_push($permissionsToSave, $permissionData);

                    if (array_key_exists('children', $permission)) {
                        $loop($permission['children'], $permission);
                    }
                }
            }
        };

        $loop($permissions);

        $roles = Role::query()
            ->with(['permissions:id,name'])
            ->select(['id', 'name'])
            ->get();

        Permission::query()->upsert($permissionsToSave, ['id'], ['name', 'description', 'parent_id']);

        Permission::query()
            ->whereNotIn('id', array_column($permissionsToSave, 'id'))
            ->delete();

        foreach ($roles as $role) {
            $permissionsName = $role->permissions->pluck('name');

            $permissions = Permission::query()
                ->select(['id'])
                ->whereIn('name', $permissionsName)
                ->get();

            $role->permissions()->sync($permissions);
        }
    }
}
