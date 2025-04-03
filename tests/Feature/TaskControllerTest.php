<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_paginated_tasks(): void
    {
        $this->authenticate();
        Task::factory()->count(10)->create();
        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
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
                'links',
                'meta',
            ]);
    }

    public function test_store_creates_a_task(): void
    {
        $this->authenticate();
        $project = Project::factory()->create();
        $user = User::factory()->create();
        $data = [
            'title' => 'Test Task',
            'description' => 'Task description for testing.',
            'project_id' => $project->id,
            'status' => 'pending',
            'due_date' => '2025-12-31',
            'order' => '1',
            'assigned_by' => $user->id,
            'assigned_to' => $user->id,
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['message' => 'Organization Created Successfully'])
            ->assertJsonStructure([
                'message',
                'data' => [
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
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'status' => 'pending',
        ]);
    }

    public function test_show_returns_a_task(): void
    {
        $this->authenticate();
        $task = Task::factory()->create([
            'project_id' => Project::factory()->create()->id,
            'assigned_by' => User::factory()->create()->id,
            'assigned_to' => User::factory()->create()->id,
        ]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
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
            ]);
    }

    public function test_update_modifies_a_task(): void
    {
        $this->authenticate();
        $task = Task::factory()->create([
            'project_id' => Project::factory()->create()->id,
            'assigned_by' => User::factory()->create()->id,
            'assigned_to' => User::factory()->create()->id,
        ]);

        $data = [
            'title' => 'Updated Task Title',
            'status' => 'completed',
            'due_date' => '2022-01-12',
            'project_id' => 1,
            'order' => 1,
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Task has been updated successfully'])
            ->assertJsonStructure([
                'message',
                'data' => [
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
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title',
            'status' => 'completed',
        ]);
    }

    public function test_destroy_deletes_a_task(): void
    {
        $this->authenticate();
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Task has been deleted successfully']);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_store_fails_with_invalid_data(): void
    {
        $this->authenticate();
        $data = [
            'title' => '',
            'status' => 'invalid',
            'project_id' => 999,
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'status', 'project_id']);
    }

    public function test_update_fails_with_invalid_data(): void
    {
        $this->authenticate();
        $task = Task::factory()->create([
            'project_id' => Project::factory()->create()->id,
            'assigned_by' => User::factory()->create()->id,
            'assigned_to' => User::factory()->create()->id,
        ]);

        $data = [
            'title' => '',
            'status' => 'invalid',
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'status']);
    }

    public function test_show_fails_for_non_existent_task(): void
    {
        $this->authenticate();
        $response = $this->getJson('/api/tasks/999');
        $response->assertStatus(404);
    }

    public function test_destroy_fails_for_non_existent_task(): void
    {
        $this->authenticate();
        $response = $this->deleteJson('/api/tasks/999');
        $response->assertStatus(404);
    }

    protected function authenticate(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
    }
}
