<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskShareLink extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'task_id',
        'created_by_user_id',
        'share_token',
        'permission',
        'is_active',
        'max_uses',
        'current_uses',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'expires_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the task that is shared.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user who created the share link.
     */
    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Check if the share link is still valid.
     */
    public function isValid(): bool
    {
        // Check if link is active
        if (!$this->is_active) {
            return false;
        }

        // Check if expired
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        // Check if max uses reached
        if ($this->max_uses && $this->current_uses >= $this->max_uses) {
            return false;
        }

        return true;
    }
}
