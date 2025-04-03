<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final class GetMemberController extends Controller
{
    /**
     * Get members of organization.
     *
     * Request for all members of an organization
     */
    public function __invoke(Organization $organization): JsonResponse
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

        $members = $organization->users()
            ->select('users.id as user_id', 'users.name', 'users.email','members.id')
            ->get();

        return response()->json([
            'message' => 'Members retrieved successfully',
            'data' => UserResource::collection($members),
        ], 200);
    }
}
