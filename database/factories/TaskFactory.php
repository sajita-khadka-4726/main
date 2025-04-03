<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
final class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->word,
            'description' => $this->faker->sentence,
            'project_id' => Project::factory(),
            'status' => $this->faker->randomElement(['not started', 'in progress', 'completed']),
            'due_date' => $this->faker->date(),
            'order' => $this->faker->randomNumber(),
            'assigned_by' => User::factory(),
            'assigned_to' => User::factory(),
        ];
    }
}
