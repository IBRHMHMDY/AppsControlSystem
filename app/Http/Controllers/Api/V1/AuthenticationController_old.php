<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Authentication\AuthenticateApplicationAction;
use App\Models\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticationController
{
    public function token(
        Request $request,
        AuthenticateApplicationAction $action,
    ): JsonResponse {
        $validated = $request->validate([
            'application_id' => ['required', 'integer'],
            'secret' => ['required', 'string'],
        ]);

        $application = Application::query()
            ->findOrFail($validated['application_id']);

        $token = $action->handle(
            $application,
            $validated['secret'],
        );

        return response()->json([
            'data' => [
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'data' => null,
            'message' => 'Token revoked successfully.',
        ]);
    }
}
