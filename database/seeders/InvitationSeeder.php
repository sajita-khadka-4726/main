<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Invitation;
use Illuminate\Database\Seeder;

final class InvitationSeeder extends Seeder
{
    /**
     * Run the invitations table seeding.
     *
     * @return void
     */
    public function run()
    {
        Invitation::factory(20)->create();
    }
}
