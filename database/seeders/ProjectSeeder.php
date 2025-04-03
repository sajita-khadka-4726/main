<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

final class ProjectSeeder extends Seeder
{
    /**
     * Run the projects table seeding.
     */
    public function run(): void
    {
        Project::factory(10)->create();
    }
}
