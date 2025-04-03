<?php

declare(strict_types=1);

use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\GetMemberController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('/organizations', OrganizationController::class);
    Route::get('/organizations/{organization}/projects', [OrganizationController::class, 'projects']);
    Route::get('/organizations/{organization}/members', GetMemberController::class);
    Route::apiResource('/tasks', TaskController::class);
    Route::apiResource('/projects', ProjectController::class);
    Route::get('/projects/{project}/tasks', [ProjectController::class, 'tasks'])->name('project.tasks');
});

require __DIR__.'/auth.php';
