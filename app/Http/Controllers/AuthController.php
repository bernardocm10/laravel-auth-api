<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
        ]);

        $user->sendEmailVerificationNotification();

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Usuário criado com sucesso. Verifique seu e-mail para ativar a conta.',
            'token'   => $token,
            'user'    => new UserResource($user),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciais inválidas.',
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me(): JsonResponse
    {
        return response()->json(new UserResource(auth('api')->user()));
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

    public function refresh(): JsonResponse
    {
        try {
            $token = auth('api')->refresh();
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException) {
            return response()->json(['message' => 'Token inválido.'], 401);
        }

        return $this->respondWithToken($token);
    }

    private function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
