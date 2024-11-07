<?php

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
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

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [LoginController::class, 'logout']);

    Route::get('get-tasks-statistics', [DashboardController::class, 'taskStatistics']);
    Route::get('get-upcoming-tasks', [DashboardController::class, 'getUpcomingDeadlines']);

    Route::prefix('projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index']);
        Route::get('{project}/edit', [ProjectController::class, 'edit']);
        Route::post('/store', [ProjectController::class, 'store']);
        Route::post('{project}/update', [ProjectController::class, 'update']);
        Route::delete('/{project}/delete', [ProjectController::class, 'delete']);
    });

    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index']);
        Route::get('{task}/edit', [TaskController::class, 'edit']);
        Route::post('/store', [TaskController::class, 'store']);
        Route::post('{task}/update', [TaskController::class, 'update']);
        Route::post('{task}/start', [TaskController::class, 'start']);
        Route::post('{task}/complete', [TaskController::class, 'complete']);
        Route::delete('/{task}/delete', [TaskController::class, 'delete']);
    });



});
