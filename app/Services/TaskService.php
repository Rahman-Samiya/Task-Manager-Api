<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskShare;
use App\Models\TaskShareLink;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class TaskService
{
    /**
     * Get paginated tasks for a user.
     *
     * @param User $user
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUserTasks(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $user->tasks()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get completed tasks for a user.
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCompletedTasks(User $user)
    {
        return $user->tasks()
            ->where('is_completed', true)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Get pending tasks for a user.
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingTasks(User $user)
    {
        return $user->tasks()
            ->where('is_completed', false)
            ->orderBy('deadline', 'asc')
            ->orderBy('priority', 'desc')
            ->get();
    }

    /**
     * Get tasks by priority.
     *
     * @param User $user
     * @param string $priority
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTasksByPriority(User $user, string $priority)
    {
        return $user->tasks()
            ->where('priority', $priority)
            ->where('is_completed', false)
            ->orderBy('deadline', 'asc')
            ->get();
    }

    /**
     * Create a new task for a user.
     *
     * @param User $user
     * @param array $data
     * @return Task
     */
    public function createTask(User $user, array $data): Task
    {
        return $user->tasks()->create($data);
    }

    /**
     * Update a task.
     *
     * @param Task $task
     * @param array $data
     * @return Task
     */
    public function updateTask(Task $task, array $data): Task
    {
        $task->update($data);
        return $task;
    }

    /**
     * Delete a task.
     *
     * @param Task $task
     * @return bool|null
     */
    public function deleteTask(Task $task): ?bool
    {
        return $task->delete();
    }

    /**
     * Toggle task completion status.
     *
     * @param Task $task
     * @return Task
     */
    public function toggleCompletion(Task $task): Task
    {
        $task->update(['is_completed' => !$task->is_completed]);
        return $task;
    }

    /**
     * Get total tasks count for a user.
     *
     * @param User $user
     * @return int
     */
    public function getTotalTasksCount(User $user): int
    {
        return $user->tasks()->count();
    }

    /**
     * Get completed tasks count for a user.
     *
     * @param User $user
     * @return int
     */
    public function getCompletedTasksCount(User $user): int
    {
        return $user->tasks()->where('is_completed', true)->count();
    }

    /**
     * Get pending tasks count for a user.
     *
     * @param User $user
     * @return int
     */
    public function getPendingTasksCount(User $user): int
    {
        return $user->tasks()->where('is_completed', false)->count();
    }

    /**
     * Get task overdue by deadline.
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOverdueTasks(User $user)
    {
        return $user->tasks()
            ->where('is_completed', false)
            ->whereDate('deadline', '<', today())
            ->orderBy('deadline', 'asc')
            ->get();
    }

    /**
     * Share a task with another user by email.
     *
     * @param Task $task
     * @param User $sharedByUser
     * @param string $email
     * @param string $permission
     * @return TaskShare
     */
    public function shareTaskWithEmail(Task $task, User $sharedByUser, string $email, string $permission = 'view'): TaskShare
    {
        $user = User::where('email', $email)->firstOrFail();

        // Check if already shared with this user
        $existingShare = TaskShare::where('task_id', $task->id)
            ->where('shared_with_user_id', $user->id)
            ->first();

        if ($existingShare) {
            // Update permission if already shared
            $existingShare->update(['permission' => $permission]);
            return $existingShare;
        }

        return TaskShare::create([
            'task_id' => $task->id,
            'shared_by_user_id' => $sharedByUser->id,
            'shared_with_user_id' => $user->id,
            'permission' => $permission,
        ]);
    }

    /**
     * Create a shareable link for a task.
     *
     * @param Task $task
     * @param User $createdByUser
     * @param string $permission
     * @param int|null $maxUses
     * @param int|null $expiryHours
     * @return TaskShareLink
     */
    public function createShareLink(
        Task $task,
        User $createdByUser,
        string $permission = 'view',
        ?int $maxUses = null,
        ?int $expiryHours = null
    ): TaskShareLink {
        $shareLink = TaskShareLink::create([
            'task_id' => $task->id,
            'created_by_user_id' => $createdByUser->id,
            'share_token' => Str::random(32),
            'permission' => $permission,
            'max_uses' => $maxUses,
            'expires_at' => $expiryHours ? now()->addHours($expiryHours) : null,
        ]);

        return $shareLink;
    }

    /**
     * Access task via share link.
     *
     * @param string $token
     * @return Task|null
     */
    public function accessViaShareLink(string $token): ?Task
    {
        $shareLink = TaskShareLink::where('share_token', $token)->first();

        if (!$shareLink || !$shareLink->isValid()) {
            return null;
        }

        // Increment usage counter
        $shareLink->increment('current_uses');

        return $shareLink->task;
    }

    /**
     * Get shared with me tasks for a user.
     *
     * @param User $user
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getSharedWithMeTasks(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Task::whereHas('shares', function ($query) use ($user) {
            $query->where('shared_with_user_id', $user->id);
        })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get task members (users task is shared with).
     *
     * @param Task $task
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTaskMembers(Task $task)
    {
        return $task->shares()
            ->with('sharedWithUser')
            ->get()
            ->map(function ($share) {
                return [
                    'id' => $share->sharedWithUser->id,
                    'name' => $share->sharedWithUser->name,
                    'email' => $share->sharedWithUser->email,
                    'permission' => $share->permission,
                ];
            });
    }

    /**
     * Remove task share.
     *
     * @param Task $task
     * @param User $user
     * @return bool
     */
    public function removeTaskShare(Task $task, User $user): bool
    {
        return (bool) TaskShare::where('task_id', $task->id)
            ->where('shared_with_user_id', $user->id)
            ->delete();
    }

    /**
     * Deactivate share link.
     *
     * @param TaskShareLink $shareLink
     * @return bool
     */
    public function deactivateShareLink(TaskShareLink $shareLink): bool
    {
        return $shareLink->update(['is_active' => false]);
    }
}
