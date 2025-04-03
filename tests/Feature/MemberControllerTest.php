<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class GetMemberControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_members(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $organization = Organization::factory()->create();
        $organization->members()->create([
            'user_id' => $user->id,
            'role' => 'member',
        ]);

        Member::factory(5)->create([
            'organization_id' => $organization->id,
            'role' => 'member',
        ]);

        $response = $this->getJson("/api/organizations/{$organization->id}/members");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Members retrieved successfully'])
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                    ],
                ],
            ]);

        $responseData = $response->json('data');
        $this->assertCount(6, $responseData);
    }

    public function test_guest_user_cannot_view_members(): void
    {
        $organization = Organization::factory()->create();

        $response = $this->getJson("/api/organizations/{$organization->id}/members");

        $response->assertStatus(401)
            ->assertJsonFragment(['message' => 'Unauthenticated.']);
    }

    public function test_non_member_cannot_view_members(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $organization = Organization::factory()->create();

        $response = $this->getJson("/api/organizations/{$organization->id}/members");

        $response->assertStatus(403)
            ->assertJsonFragment(['message' => 'Unauthorized access to projects of this organization.']);
    }

    protected function authenticate(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
    }
}
