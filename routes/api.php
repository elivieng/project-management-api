<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [RegisterController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [RegisterController::class, 'logout']);

    // Create new project.
    Route::post('/projects', [ProjectController::class, 'store']);
    // List all projects
    Route::get('/projects', [ProjectController::class, 'index']);
    // Get a specific project
    Route::get('/projects/{id}', [ProjectController::class, 'show']);
    // Update project
    Route::put('/projects/{id}', [ProjectController::class, 'update']);
    // delete project
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);


    // Create new task
    Route::post('/tasks', [TaskController::class, 'store']);  
    // List all tasks
    Route::get('/tasks', [TaskController::class, 'index']);   
    // Get task
    Route::get('/tasks/{id}', [TaskController::class, 'show']); 
    // update task
    Route::put('/tasks/{id}', [TaskController::class, 'update']); 
    // delete task
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']); 
});


