<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\ProjectStoreRequest;
use App\Http\Requests\Project\ProjectUpdateRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ProjectController extends Controller
{
    /**
     * Get Projects
     **/
    public function index(): AnonymousResourceCollection
    {
        $perPage = (int) request()->query('per_page', '5');
        $project = Project::paginate($perPage);

        return ProjectResource::collection($project);
    }

    /**
     * View Project
     **/
    public function show(Project $project): ProjectResource
    {
        return new ProjectResource($project);
    }

    /**
     * Store Project
     **/
    public function store(ProjectStoreRequest $request): JsonResponse
    {
        $project = Project::create($request->validated());

        return response()->json([
            'message' => 'Project Created Successfully',
            'data' => new ProjectResource($project),
        ], 201);
    }

    /**
     * Update Project
     **/
    public function update(ProjectUpdateRequest $request, Project $project): JsonResponse
    {
        $project->update($request->validated());

        return response()->json([
            'message' => 'Project Updated Successfully',
            'data' => new ProjectResource($project),
        ], 200);
    }

    /**
     * Delete Project
     **/
    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return response()->json([
            'message' => 'Project Deleted successfully',
        ], 200);
    }

    /**
     * Get tasks of the project
     **/
    public function tasks(Project $project): AnonymousResourceCollection
    {
        $tasks = Task::where('project_id', $project->id)->get();

        return TaskResource::collection($tasks);
    }
}
