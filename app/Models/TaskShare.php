<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskShare extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'task_id',
        'shared_by_user_id',
        'shared_with_user_id',
        'permission',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
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
     * Get the user who shared the task.
     */
    public function sharedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_by_user_id');
    }

    /**
     * Get the user with whom the task is shared.
     */
    public function sharedWithUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_with_user_id');
    }
}
