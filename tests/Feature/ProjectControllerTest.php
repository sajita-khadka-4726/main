<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_paginated_projects(): void
    {
        $this->authenticate();
        Project::factory()->count(10)->create();

        $response = $this->getJson('/api/projects');

        $response->assertStatus(200)
            ->assertJsonStructure([
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
                'links',
                'meta',
            ]);
    }

    public function test_store_creates_a_project(): void
    {
        $this->authenticate();

        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        $data = [
            'title' => 'Test Project',
            'slug' => 'test-project',
            'organization_id' => $organization->id,
            'description' => 'Test Description',
            'color' => 'blue',
            'status' => 1,
            'deadline' => '2025-12-31',
            'created_by' => $user->id,
        ];

        $response = $this->postJson('/api/projects', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['message' => 'Project Created Successfully'])
            ->assertJsonStructure([
                'message',
                'data' => [
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
            ]);

        $this->assertDatabaseHas('projects', [
            'title' => 'Test Project',
            'slug' => 'test-project',
        ]);
    }

    public function test_show_returns_a_project(): void
    {
        $this->authenticate();
        $organization = Organization::factory()->create();
        $project = Project::factory()->create(['organization_id' => $organization->id]);

        $response = $this->getJson("/api/projects/{$project->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
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
            ]);
    }

    public function test_update_modifies_a_project(): void
    {
        $this->authenticate();
        $organization = Organization::factory()->create();
        $project = Project::factory()->create(['organization_id' => $organization->id]);

        $data = [
            'title' => 'Updated Project',
            'slug' => 'updated-project',
            'organization_id' => $organization->id,
            'description' => 'Updated Description',
            'color' => 'red',
            'status' => 0,
            'deadline' => '2026-12-31',
            'created_by' => 1,
        ];

        $response = $this->putJson("/api/projects/{$project->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Project Updated Successfully'])
            ->assertJsonStructure([
                'message',
                'data' => [
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
            ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'Updated Project',
            'slug' => 'updated-project',
        ]);
    }

    public function test_destroy_deletes_a_project(): void
    {
        $this->authenticate();
        $organization = Organization::factory()->create();
        $project = Project::factory()->create(['organization_id' => $organization->id]);

        $response = $this->deleteJson("/api/projects/{$project->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Project Deleted successfully']);

        $this->assertSoftDeleted($project);
    }

    public function test_store_fails_with_invalid_data(): void
    {
        $this->authenticate();

        $data = [
            'title' => '',
            'slug' => '',
            'status' => 'invalid',
        ];

        $response = $this->postJson('/api/projects', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'slug', 'status']);
    }

    public function test_update_fails_with_invalid_data(): void
    {
        $this->authenticate();
        $organization = Organization::factory()->create();
        $project = Project::factory()->create(['organization_id' => $organization->id]);

        $data = [
            'title' => '',
            'slug' => '',
            'description' => '',
            'color' => '',
            'status' => 'invalid',
        ];

        $response = $this->putJson("/api/projects/{$project->id}", $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'slug',
                'description',
                'color',
                'status',
            ]);
    }

    public function test_show_fails_for_non_existent_project(): void
    {
        $this->authenticate();

        $response = $this->getJson('/api/projects/999');

        $response->assertStatus(404);
    }

    public function test_destroy_fails_for_non_existent_project(): void
    {
        $this->authenticate();

        $response = $this->deleteJson('/api/projects/999');

        $response->assertStatus(404);
    }

    public function test_filter_tasks_by_project(): void
    {
        $this->authenticate();

        $project = Project::factory()->create();

        Task::factory(5)->create([
            'project_id' => $project->id,
        ]);

        $response = $this->getJson("/api/projects/{$project->id}/tasks");
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'project_id',
                        'status',
                        'due_date',
                        'order',
                        'assigned_by',
                        'assigned_to',
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
