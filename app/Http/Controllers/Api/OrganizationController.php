<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\OrganizationStoreRequest;
use App\Http\Requests\Organization\OrganizationUpdateRequest;
use App\Http\Resources\OrganizationResource;
use App\Http\Resources\ProjectResource;
use App\Models\Member;
use App\Models\Organization;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

final class OrganizationController extends Controller
{
    /**
     * Get Organizations
     **/
    public function index(): AnonymousResourceCollection|JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }
        $organizations = $user->organizations()
            ->withCount('projects')
            ->get();

        return OrganizationResource::collection($organizations);
    }

    /**
     * View Organization
     **/
    public function show(Organization $organization): OrganizationResource
    {
        return new OrganizationResource($organization);
    }

    /**
     * Store Organization
     **/
    public function store(OrganizationStoreRequest $request): JsonResponse
    {
        $organization = Organization::create($request->validated());
        Member::create([
            'user_id' => Auth::id(),
            'organization_id' => $organization->id,
            'role' => 'owner',
        ]);

        return response()->json([
            'message' => 'Organization Created Successfully',
            'data' => new OrganizationResource($organization),
        ], 201);
    }

    /**
     * Update Organization
     **/
    public function update(OrganizationUpdateRequest $request, Organization $organization): JsonResponse
    {
        $organization->update($request->validated());

        return response()->json([
            'message' => 'Organization Updated Successfully',
            'data' => new OrganizationResource($organization),
        ], 200);
    }

    /**
     * Delete Organization
     **/
    public function destroy(Organization $organization): JsonResponse
    {
        $organization->delete();

        return response()->json([
            'message' => 'Organization deleted successfully',
        ], 200);
    }

    /**
     * Get projects of the organizations
     **/
    public function projects(Organization $organization): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $isMember = $organization->members()->where('user_id', $user->id)->exists();
        if (! $isMember) {
            return response()->json([
                'message' => 'Unauthorized access to projects of this organization.',
            ], 403);
        }

        $projects = Project::where('organization_id', $organization->id)->get();

        return response()->json([
            'message' => 'Projects retrieved successfully',
            'data' => ProjectResource::collection($projects),
        ], 200);
    }
}
