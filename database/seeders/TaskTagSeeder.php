<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TaskTag;
use Illuminate\Database\Seeder;

final class TaskTagSeeder extends Seeder
{
    /**
     * Run the task_tags table seeding.
     *
     * @return void
     */
    public function run()
    {
        TaskTag::factory(20)->create();
    }
}
