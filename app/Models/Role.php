<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $appends = [
        'permissions_ids',
    ];

    public const FILTERABLE_COLUMNS = [
        'name',
        'description',
    ];

    public function permissionsIds(): Attribute
    {
        return new Attribute(
            get: fn () => $this->relationLoaded('permissions') ? $this->permissions->pluck('id') : []
        );
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }
}
