<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;

final class MemberSeeder extends Seeder
{
    /**
     * Run the members table seeding.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $organizations = Organization::all();

        foreach ($organizations as $organization) {
            $organization->users()->attach(
                $users->random(3)->mapWithKeys(function ($user) {
                    return [
                        $user->id => ['role' => 'member'],  // Default role
                    ];
                })
            );
        }
    }
}
