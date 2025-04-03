<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

final class EmailVerificationControllerTest extends TestCase
{
    public function test_email_can_be_verified(): void
    {

        $user = User::factory()->unverified()->create();

        $user = User::first();

        $this->assertNotNull($user);

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->getJson($verificationUrl);

        Event::assertDispatched(Verified::class);

        $this->assertTrue($user->fresh()?->hasVerifiedEmail());

        $response->assertJson([
            'message' => 'Email successfully verified.',
        ]);
    }

    public function test_email_already_verified(): void
    {

        User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $user = User::first();

        $this->assertNotNull($user);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->getJson($verificationUrl);

        $this->assertTrue($user->fresh()?->hasVerifiedEmail());
        $response->assertStatus(200);
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        User::factory()->create([
            'email_verified_at' => null,
        ]);

        $user = User::first();
        $this->assertNotNull($user);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $response = $this->getJson($verificationUrl);

        $this->assertFalse($user->fresh()?->hasVerifiedEmail());
        $response->assertStatus(403);
    }
}
