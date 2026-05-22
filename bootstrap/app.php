<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // CORS: deve rodar antes de qualquer outro middleware para responder
        // corretamente às preflight requests (OPTIONS) do browser.
        $middleware->prepend(\Illuminate\Http\Middleware\HandleCors::class);

        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Aliases usados nas rotas
        $middleware->alias([
            'token.version'  => \App\Http\Middleware\ValidateTokenVersion::class,
            'email.verified' => \App\Http\Middleware\EnsureEmailVerified::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('api/*')) {
                return null;
            }
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (JWTException $e, Request $request) {
            if ($request->is('api/*')) {
                $message = match (true) {
                    $e instanceof TokenExpiredException => 'Token expirado.',
                    $e instanceof TokenInvalidException => 'Token inválido.',
                    default                             => 'Token ausente ou malformado.',
                };

                return response()->json(['message' => $message], 401);
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Não autenticado.'], 401);
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Dados inválidos.',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(
                    ['message' => 'Muitas tentativas. Tente novamente em breve.'],
                    429,
                    ['Retry-After' => $e->getHeaders()['Retry-After'] ?? 60]
                );
            }
        });

        $exceptions->render(function (InvalidSignatureException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Link inválido ou expirado.'], 403);
            }
        });
    })->create();
