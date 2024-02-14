<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasUserAudit
{
    protected function initializeHasUserAudit()
    {
        static::creating(function ($model) {
            $model->created_by = auth()->id() ?? 1;
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id() ?? 1;
        });

        static::saving(function ($model) {
            $model->updated_by = auth()->id() ?? 1;
        });

        static::deleting(function ($model) {
            $model->deleted_by = auth()->id() ?? 1;
            $model->save();
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id');
    }
}
