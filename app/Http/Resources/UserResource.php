<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // UUID público em vez do ID numérico do banco — evita IDOR e enumeração de usuários
            'id'             => $this->uuid,
            'name'           => $this->name,
            'email'          => $this->email,
            'email_verified' => $this->hasVerifiedEmail(),
        ];
    }
}
