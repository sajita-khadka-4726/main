<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;

final class ResentEmailVerificationControllerTest extends TestCase
{
    public function test_user_can_resend_verification_link(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);
        $user = User::first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->postJson(route('verification.send'));

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Verification link sent.']);
    }

    public function test_user_cannot_resend_verification_link_if_email_already_verified(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user = User::first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->postJson(route('verification.send'));

        $response->assertJson(['message' => 'Email is already verified.']);
    }
}
