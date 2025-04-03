<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @tags Authentication
 **/
final class LoginUserController extends Controller
{
    /**
     * Login
     */
    public function __invoke(LoginUserRequest $request): JsonResponse
    {

        $request->authenticate();

        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => 'User not authenticated.'], 401);
        }

        $token = $user->createToken('user_token')->plainTextToken;

        return response()->json([
            'message' => 'Successfully logged in',
            'token' => $token,
            'user' => $user,
        ], 200);
    }
}
