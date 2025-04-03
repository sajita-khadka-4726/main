<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;

/**
 * @tags Authentication
 **/
final class EmailVerificationController extends Controller
{
    /**
     * Email Verification
     **/
    public function __invoke(EmailVerificationRequest $request): JsonResponse
    {
        $request->fulfill();

        return response()->json([
            'message' => 'Email successfully verified.',
        ]);
    }
}
