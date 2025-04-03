<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(OrganizationSeeder::class);
        $this->call(MemberSeeder::class);
        $this->call(TagSeeder::class);
        $this->call(ProjectSeeder::class);
        $this->call(TaskSeeder::class);
        $this->call(TaskCommentSeeder::class);
        $this->call(TaskTagSeeder::class);
        $this->call(InvitationSeeder::class);
    }
}
