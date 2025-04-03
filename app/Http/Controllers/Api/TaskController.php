<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\TaskStoreRequest;
use App\Http\Requests\Task\TaskUpdateRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class TaskController extends Controller
{
    /**
     * Get Tasks
     */
    public function index(): AnonymousResourceCollection
    {
        $perPage = (int) request()->query('per_page', '10');

        $task = Task::paginate($perPage);

        return TaskResource::collection($task);
    }

    /**
     * Store Task
     */
    public function store(TaskStoreRequest $request): JsonResponse
    {
        $task = Task::create($request->validated());

        return response()->json([
            'message' => 'Organization Created Successfully',
            'data' => new TaskResource($task),
        ], 201);
    }

    /**
     * View Task
     */
    public function show(Task $task): TaskResource
    {
        return new TaskResource($task);
    }

    /**
     * Update Task
     */
    public function update(TaskUpdateRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());

        return response()->json([
            'message' => 'Task has been updated successfully',
            'data' => new TaskResource($task),
        ], 200);
    }

    /**
     * Delete Task
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([
            'message' => 'Task has been deleted successfully',
        ], 200);
    }
}
