<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // Authentication Routes (Public)
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('login', [AuthController::class, 'login'])->name('auth.login');

        // Protected Auth Routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
            Route::post('logout-all-devices', [AuthController::class, 'logoutFromAllDevices'])->name('auth.logout.all');
            Route::get('me', [AuthController::class, 'me'])->name('auth.me');
        });
    });

    // Task Routes (Protected)
    Route::middleware('auth:sanctum')->prefix('tasks')->group(function () {
        // Resource Routes
        Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
        Route::post('/', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('{task}', [TaskController::class, 'show'])->name('tasks.show');
        Route::put('{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

        // Custom Actions
        Route::post('{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
        Route::get('filters/completed', [TaskController::class, 'completed'])->name('tasks.completed');
        Route::get('filters/pending', [TaskController::class, 'pending'])->name('tasks.pending');
        Route::get('filters/overdue', [TaskController::class, 'overdue'])->name('tasks.overdue');
        Route::get('filters/by-priority', [TaskController::class, 'byPriority'])->name('tasks.by-priority');

        // Statistics
        Route::get('dashboard/stats', [TaskController::class, 'stats'])->name('tasks.stats');
    });
});

// Health Check Route
Route::get('health', function () {
    return response()->json([
        'status' => true,
        'message' => 'API is running',
        'data' => [
            'version' => '1.0',
            'timestamp' => now(),
        ],
    ]);
})->name('health');
