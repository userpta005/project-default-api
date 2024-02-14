<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Traits\Authorizable;
use App\Traits\HasUserAudit;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use Authorizable;
    use HasApiTokens;
    use HasFactory;
    use HasUserAudit;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => UserStatus::class,
    ];

    protected $appends = [];

    public const FILTERABLE_COLUMNS = [
        'name',
        'email',
    ];
}
