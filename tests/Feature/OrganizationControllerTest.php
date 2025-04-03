<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class OrganizationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_paginated_organizations(): void
    {
        $this->authenticate();
        Organization::factory()->count(10)->create();

        $response = $this->getJson('/api/organizations');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'address',
                        'email',
                        'phone',
                        'logo',
                        'status',
                    ],
                ],
            ]);
    }

    public function test_store_creates_an_organization(): void
    {
        $this->authenticate();

        $data = [
            'name' => 'Test Organization',
            'email' => 'test@example.com',
            'address' => '123 Test Street',
            'phone' => '1234567890',
            'logo' => 'test-logo.png',
            'status' => 1,
        ];

        $response = $this->postJson('/api/organizations', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['message' => 'Organization Created Successfully'])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'address',
                    'phone',
                    'logo',
                    'status',
                ],
            ]);

        $this->assertDatabaseHas('organizations', [
            'name' => 'Test Organization',
            'email' => 'test@example.com',
        ]);
    }

    public function test_show_returns_an_organization(): void
    {
        $this->authenticate();
        $organization = Organization::factory()->create();

        $response = $this->getJson("/api/organizations/{$organization->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'address',
                    'email',
                    'phone',
                    'logo',
                    'status',
                ],
            ]);
    }

    public function test_update_modifies_an_organization(): void
    {
        $this->authenticate();
        $organization = Organization::factory()->create();

        $data = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'address' => '456 Updated Street',
            'phone' => '9876543210',
            'logo' => 'updated-logo.png',
            'status' => 1,
        ];

        $response = $this->putJson("/api/organizations/{$organization->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Organization Updated Successfully'])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'address',
                    'phone',
                    'logo',
                    'status',
                ],
            ]);

        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    public function test_destroy_deletes_an_organization(): void
    {
        $this->authenticate();
        $organization = Organization::factory()->create();

        $response = $this->deleteJson("/api/organizations/{$organization->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Organization deleted successfully']);

        $this->assertSoftDeleted($organization);
    }

    public function test_store_fails_with_invalid_data(): void
    {
        $this->authenticate();

        $data = [
            'name' => '',
            'email' => 'invalid-email',
            'status' => 'invalid',
        ];

        $response = $this->postJson('/api/organizations', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'status']);
    }

    public function test_update_fails_with_invalid_data(): void
    {
        $this->authenticate();
        $organization = Organization::factory()->create();

        $data = [
            'name' => '',
            'email' => 'invalid-email',
            'address' => '',
            'phone' => '',
            'logo' => '',
            'status' => 'invalid',
        ];

        $response = $this->putJson("/api/organizations/{$organization->id}", $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'email',
                'address',
                'status',
            ]);
    }

    public function test_show_fails_for_non_existent_organization(): void
    {
        $this->authenticate();

        $response = $this->getJson('/api/organizations/9999');

        $response->assertStatus(404);
    }

    public function test_destroy_fails_for_non_existent_organization(): void
    {
        $this->authenticate();

        $response = $this->deleteJson('/api/organizations/999');

        $response->assertStatus(404);
    }

    public function test_retrieving_projects_requires_authentication(): void
    {
        $organization = Organization::factory()->create();

        $response = $this->getJson("/api/organizations/{$organization->id}/projects");

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_user_not_member_of_organization_cannot_retrieve_projects(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $organization = Organization::factory()->create();

        $response = $this->getJson("/api/organizations/{$organization->id}/projects");

        $response->assertStatus(403)
            ->assertJson(['message' => 'Unauthorized access to projects of this organization.']);
    }

    public function test_return_projects_of_organization(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $organization = Organization::factory()->create();

        $organization->members()->create([
            'user_id' => $user->id,
            'role' => 'admin',
        ]);

        $this->assertDatabaseHas('members', [
            'user_id' => $user->id,
            'organization_id' => $organization->id,
        ]);

        $projects = Project::factory(3)->create([
            'organization_id' => $organization->id,
        ]);

        Project::factory(2)->create();

        $response = $this->getJson("/api/organizations/{$organization->id}/projects");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Projects retrieved successfully'])
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'slug',
                        'organization_id',
                        'description',
                        'color',
                        'status',
                        'deadline',
                        'created_by',
                    ],
                ],
            ]);
    }

    protected function authenticate(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
    }
}
