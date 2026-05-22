<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'uuid',
        'token_version',
    ];

    // Garante que novos modelos criados em PHP já tenham token_version = 1,
    // sem depender de ler o DEFAULT do banco após o INSERT.
    protected $attributes = [
        'token_version' => 1,
    ];

    protected static function booted(): void
    {
        // Gera UUID automaticamente ao criar um novo usuário
        static::creating(function (self $user): void {
            $user->uuid = (string) Str::uuid();
        });
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Inclui token_version no payload JWT.
     * Qualquer token emitido antes de uma troca de senha terá uma versão menor
     * e será rejeitado pelo middleware ValidateTokenVersion.
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'token_version' => $this->token_version,
        ];
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
        'token_version',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
