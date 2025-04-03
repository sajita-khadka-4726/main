<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

final class OrganizationSeeder extends Seeder
{
    /**
     * Run the organizations table seeding.
     */
    public function run(): void
    {
        Organization::factory(5)->create();
    }
}
