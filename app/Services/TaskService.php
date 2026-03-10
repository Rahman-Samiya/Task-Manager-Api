<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Pagination\Paginator;

class TaskService
{
    /**
     * Get paginated tasks for a user.
     *
     * @param User $user
     * @param int $perPage
     * @return Paginator
     */
    public function getUserTasks(User $user, int $perPage = 15): Paginator
    {
        return $user->tasks()
            ->orderBy('created_at', 'desc')
            ->simplePaginate($perPage);
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
}
