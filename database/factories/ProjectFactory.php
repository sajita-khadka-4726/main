<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
final class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->word,
            'slug' => $this->faker->slug,
            'organization_id' => Organization::factory(),
            'description' => $this->faker->sentence,
            'color' => $this->faker->hexColor,
            'status' => $this->faker->randomElement([1, 2, 3]),
            'deadline' => $this->faker->date(),
            'created_by' => User::factory(),
        ];
    }
}
