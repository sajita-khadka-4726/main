<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TaskComment;
use Illuminate\Database\Seeder;

final class TaskCommentSeeder extends Seeder
{
    /**
     * Run the task_comments table seeding.
     *
     * @return void
     */
    public function run()
    {
        TaskComment::factory(30)->create();
    }
}
