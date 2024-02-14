<?php

namespace App\Traits;

use App\Models\Role;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait Authorizable
{
    public function initializeAuthorizable()
    {
        $this->append('roles_ids');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_has_roles', 'user_id', 'role_id');
    }

    public function rolesIds(): Attribute
    {
        return new Attribute(
            get: fn () => $this->relationLoaded('roles') ? $this->roles->pluck('id') : []
        );
    }

    public function allPermissions(): Collection
    {
        $this->loadMissing('roles.permissions');

        return $this->roles->flatMap(function ($role) {
            return $role->permissions;
        })->pluck('name')->unique();
    }

    public function hasPermission(string $permissionName): bool
    {
        return $this->allPermissions()->contains($permissionName);
    }

    public function hasAnyPermission(array $permissionNames): bool
    {
        return $this->allPermissions()->intersect($permissionNames)->isNotEmpty();
    }
}
