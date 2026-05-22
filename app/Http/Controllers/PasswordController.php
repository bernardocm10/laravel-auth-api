<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

class PasswordController extends Controller
{
    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {
        Password::sendResetLink(['email' => $request->email]);

        // Resposta genérica para não vazar se o e-mail existe na base
        return response()->json([
            'message' => 'Se este e-mail estiver cadastrado, você receberá um link de redefinição em breve.',
        ]);
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Incrementa token_version para revogar todos os JWTs anteriores
                // emitidos para este usuário em qualquer dispositivo/sessão.
                $user->forceFill([
                    'password'      => $password,
                    'token_version' => $user->token_version + 1,
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Token inválido ou expirado.'], 422);
        }

        return response()->json(['message' => 'Senha redefinida com sucesso.']);
    }

    public function update(UpdatePasswordRequest $request): JsonResponse
    {
        $user = auth('api')->user();

        // Incrementa token_version para invalidar todas as outras sessões ativas
        $user->forceFill([
            'password'      => $request->password,
            'token_version' => $user->token_version + 1,
        ])->save();

        // Invalida o token JWT da sessão atual
        auth('api')->logout();

        return response()->json(['message' => 'Senha atualizada com sucesso. Faça login novamente.']);
    }
}
