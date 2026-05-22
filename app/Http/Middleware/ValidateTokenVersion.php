<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class ValidateTokenVersion
{
    /**
     * Rejeita tokens JWT emitidos antes de uma troca/reset de senha.
     *
     * Cada vez que o usuário troca ou redefine a senha, token_version é
     * incrementado no banco. O payload JWT carrega a versão no momento em
     * que o token foi emitido; se ela for menor que a atual, o token é
     * considerado revogado e a requisição é bloqueada.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('api')->user();

        if (! $user) {
            return $next($request);
        }

        $payload = JWTAuth::parseToken()->getPayload();
        $tokenVersion = $payload->get('token_version');

        if ($tokenVersion === null || (int) $tokenVersion !== (int) $user->token_version) {
            return response()->json(['message' => 'Token revogado. Faça login novamente.'], 401);
        }

        return $next($request);
    }
}
