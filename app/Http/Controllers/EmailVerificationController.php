<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function verify(Request $request, int $id, string $hash): JsonResponse
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Link de verificação inválido.'], 403);
        }

        if (! $request->hasValidSignature()) {
            return response()->json(['message' => 'Link de verificação expirado ou inválido.'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'E-mail já verificado.']);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(['message' => 'E-mail verificado com sucesso.']);
    }

    public function resend(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'E-mail já verificado.']);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Link de verificação reenviado.']);
    }
}
