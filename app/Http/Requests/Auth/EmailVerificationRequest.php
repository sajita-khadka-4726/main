<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Http\FormRequest;

final class EmailVerificationRequest extends FormRequest
{
    private User $user;

    public function authorize(): bool
    {
        if (! $this->route()) {
            return false;
        }
        $user = User::find($this->route()->parameter('id'));

        $hash = $this->route()->parameter('hash');

        if (! $user || ! $hash) {
            return false;
        }

        if (! is_string($hash)) {
            return false;
        }

        $this->user = $user;
        if (! hash_equals(
            sha1($this->user->getEmailForVerification()),
            (string) $hash
        )) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }

    public function fulfill(): void
    {
        if (! $this->user->hasVerifiedEmail()) {
            $this->user->markEmailAsVerified();
            event(new Verified($this->user));
        }
    }
}
