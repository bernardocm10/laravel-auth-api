<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password:api'],
            'password'         => ['required', 'confirmed', 'different:current_password', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required'         => 'A senha atual é obrigatória.',
            'current_password.current_password'  => 'A senha atual está incorreta.',
            'password.required'                  => 'A nova senha é obrigatória.',
            'password.confirmed'                 => 'A confirmação de senha não corresponde.',
            'password.different'                 => 'A nova senha deve ser diferente da senha atual.',
            'password.min'     => 'A senha deve ter no mínimo 8 caracteres.',
            'password.letters' => 'A senha deve conter ao menos uma letra.',
            'password.mixed'   => 'A senha deve conter letras maiúsculas e minúsculas.',
            'password.numbers' => 'A senha deve conter ao menos um número.',
        ];
    }
}
