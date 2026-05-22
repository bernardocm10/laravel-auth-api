<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerified
{
    /**
     * Bloqueia o acesso de usuários com e-mail não verificado.
     *
     * Retorna JSON em vez de redirecionar — adequado para APIs.
     * Deve ser aplicado APÓS o middleware auth:api nas rotas.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('api')->user();

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return response()->json(
                ['message' => 'Seu e-mail não foi verificado. Verifique sua caixa de entrada.'],
                403
            );
        }

        return $next($request);
    }
}
