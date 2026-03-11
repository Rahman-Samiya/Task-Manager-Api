<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\ShareTaskByEmailRequest;
use App\Http\Requests\Task\ShareTaskByLinkRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskController extends Controller
{
    use ApiResponse;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected TaskService $taskService
    ) {}

    /**
     * Display a listing of tasks.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $perPage = $request->input('per_page', 15);

            $tasks = $this->taskService->getUserTasks($user, $perPage);
            $taskItems = $tasks instanceof LengthAwarePaginator ? $tasks->items() : $tasks;
            $taskData = collect($taskItems)
                ->map(fn ($task) => (new TaskResource($task))->resolve())
                ->all();

            return $this->successResponse([
                'tasks' => $taskData,
                'pagination' => [
                    'total' => $tasks->total() ?? $tasks->count(),
                    'per_page' => $perPage,
                    'current_page' => $tasks->currentPage(),
                    'next_page_url' => $tasks->nextPageUrl(),
                    'prev_page_url' => $tasks->previousPageUrl(),
                ],
            ], 'Tasks retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Store a newly created task.
     *
     * @param StoreTaskRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTaskRequest $request)
    {
        try {
            $user = auth('sanctum')->user();
            $task = $this->taskService->createTask($user, $request->validated());

            return $this->successResponse(
                new TaskResource($task),
                'Task created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Display the specified task.
     *
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Task $task)
    {
        try {
            $user = auth('sanctum')->user();

            if ($task->user_id !== $user->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            return $this->successResponse(
                new TaskResource($task),
                'Task retrieved successfully',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Update the specified task.
     *
     * @param UpdateTaskRequest $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        try {
            $user = auth('sanctum')->user();

            if ($task->user_id !== $user->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $updatedTask = $this->taskService->updateTask($task, $request->validated());

            return $this->successResponse(
                new TaskResource($updatedTask),
                'Task updated successfully',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Delete the specified task.
     *
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Task $task)
    {
        try {
            $user = auth('sanctum')->user();

            if ($task->user_id !== $user->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $this->taskService->deleteTask($task);

            return $this->successResponse(null, 'Task deleted successfully', 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Toggle task completion status.
     *
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Task $task)
    {
        try {
            $user = auth('sanctum')->user();

            if ($task->user_id !== $user->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $updatedTask = $this->taskService->toggleCompletion($task);

            return $this->successResponse(
                new TaskResource($updatedTask),
                'Task status updated successfully',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get dashboard statistics for authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        try {
            $user = auth('sanctum')->user();

            $stats = [
                'total_tasks' => $this->taskService->getTotalTasksCount($user),
                'completed_tasks' => $this->taskService->getCompletedTasksCount($user),
                'pending_tasks' => $this->taskService->getPendingTasksCount($user),
                'overdue_tasks' => count($this->taskService->getOverdueTasks($user)),
            ];

            return $this->successResponse($stats, 'Dashboard statistics retrieved', 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get completed tasks.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function completed()
    {
        try {
            $user = auth('sanctum')->user();
            $tasks = $this->taskService->getCompletedTasks($user);

            return $this->successResponse(
                TaskResource::collection($tasks),
                'Completed tasks retrieved successfully',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get pending tasks.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function pending()
    {
        try {
            $user = auth('sanctum')->user();
            $tasks = $this->taskService->getPendingTasks($user);

            return $this->successResponse(
                TaskResource::collection($tasks),
                'Pending tasks retrieved successfully',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get tasks by priority level.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function byPriority(Request $request)
    {
        try {
            $request->validate([
                'priority' => ['required', 'in:low,medium,high'],
            ]);

            $user = auth('sanctum')->user();
            $tasks = $this->taskService->getTasksByPriority($user, $request->input('priority'));

            return $this->successResponse(
                TaskResource::collection($tasks),
                "Tasks with {$request->input('priority')} priority retrieved successfully",
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get overdue tasks.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function overdue()
    {
        try {
            $user = auth('sanctum')->user();
            $tasks = $this->taskService->getOverdueTasks($user);

            return $this->successResponse(
                TaskResource::collection($tasks),
                'Overdue tasks retrieved successfully',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Share task with another user by email.
     *
     * @param Task $task
     * @param ShareTaskByEmailRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function shareByEmail(Task $task, ShareTaskByEmailRequest $request)
    {
        try {
            $user = auth('sanctum')->user();

            if ($task->user_id !== $user->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $this->taskService->shareTaskWithEmail(
                $task,
                $user,
                $request->email,
                $request->permission
            );

            return $this->successResponse(
                null,
                'Task shared successfully',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Create a shareable link for the task.
     *
     * @param Task $task
     * @param ShareTaskByLinkRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createShareLink(Task $task, ShareTaskByLinkRequest $request)
    {
        try {
            $user = auth('sanctum')->user();

            if ($task->user_id !== $user->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $shareLink = $this->taskService->createShareLink(
                $task,
                $user,
                $request->permission,
                $request->input('max_uses'),
                $request->input('expiry_hours', 168) // Default 7 days
            );

            return $this->successResponse([
                'share_token' => $shareLink->share_token,
                'share_url' => route('tasks.access-via-link', ['token' => $shareLink->share_token]),
                'permission' => $shareLink->permission,
                'expires_at' => $shareLink->expires_at,
            ], 'Share link created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get task members (those with whom task is shared).
     *
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMembers(Task $task)
    {
        try {
            $user = auth('sanctum')->user();

            if ($task->user_id !== $user->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $members = $this->taskService->getTaskMembers($task);

            return $this->successResponse(
                $members,
                'Task members retrieved successfully',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Remove a member from task share.
     *
     * @param Task $task
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeMember(Task $task, Request $request)
    {
        try {
            $user = auth('sanctum')->user();

            if ($task->user_id !== $user->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $request->validate([
                'user_id' => ['required', 'exists:users,id'],
            ]);

            $sharedWithUser = User::findOrFail($request->user_id);
            $this->taskService->removeTaskShare($task, $sharedWithUser);

            return $this->successResponse(
                null,
                'Member removed successfully',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Get shared with me tasks.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSharedWithMe(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $perPage = $request->input('per_page', 15);

            $tasks = $this->taskService->getSharedWithMeTasks($user, $perPage);
            $taskItems = $tasks instanceof LengthAwarePaginator ? $tasks->items() : $tasks;
            $taskData = collect($taskItems)
                ->map(fn ($task) => (new TaskResource($task))->resolve())
                ->all();

            return $this->successResponse([
                'tasks' => $taskData,
                'pagination' => [
                    'total' => $tasks->total() ?? $tasks->count(),
                    'per_page' => $perPage,
                    'current_page' => $tasks->currentPage(),
                    'next_page_url' => $tasks->nextPageUrl(),
                    'prev_page_url' => $tasks->previousPageUrl(),
                ],
            ], 'Shared tasks retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * Deactivate a share link.
     *
     * @param Task $task
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivateShareLink(Task $task, Request $request)
    {
        try {
            $user = auth('sanctum')->user();

            if ($task->user_id !== $user->id) {
                return $this->errorResponse('Unauthorized', 403);
            }

            $request->validate([
                'share_token' => ['required', 'string'],
            ]);

            $shareLink = $task->shareLinks()
                ->where('share_token', $request->share_token)
                ->firstOrFail();

            $this->taskService->deactivateShareLink($shareLink);

            return $this->successResponse(
                null,
                'Share link deactivated successfully',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}
