<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token'    => ['required', 'string'],
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required'     => 'O token de redefinição é obrigatório.',
            'email.required'     => 'O e-mail é obrigatório.',
            'email.email'        => 'Informe um e-mail válido.',
            'password.required'  => 'A nova senha é obrigatória.',
            'password.confirmed' => 'A confirmação de senha não corresponde.',
            'password.min'     => 'A senha deve ter no mínimo 8 caracteres.',
            'password.letters' => 'A senha deve conter ao menos uma letra.',
            'password.mixed'   => 'A senha deve conter letras maiúsculas e minúsculas.',
            'password.numbers' => 'A senha deve conter ao menos um número.',
        ];
    }
}
