<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Response;

/**
 * @tags Authentication
 **/
final class RegisterUserController extends Controller
{
    /**
     * Register
     */
    public function __invoke(RegisterUserRequest $request): Response
    {
        $data = $request->validated();

        $user = User::create($data);

        event(new Registered($user));

        return response()->noContent();
    }
}
