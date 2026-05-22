<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = auth('api')->user();

        $emailChanged = $user->email !== $request->email;

        $user->forceFill([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }

        return response()->json([
            'message' => $emailChanged
                ? 'Perfil atualizado. Verifique seu novo e-mail para reativar a conta.'
                : 'Perfil atualizado com sucesso.',
            'user' => new UserResource($user),
        ]);
    }
}
