<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

final class TagSeeder extends Seeder
{
    /**
     * Run the tags table seeding.
     */
    public function run(): void
    {
        Tag::factory(5)->create();
    }
}
