<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()->mixedCase()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                  => 'O nome é obrigatório.',
            'email.required'                 => 'O e-mail é obrigatório.',
            'email.unique'                   => 'Este e-mail já está em uso.',
            'password.required'              => 'A senha é obrigatória.',
            'password.confirmed'             => 'A confirmação de senha não corresponde.',
            'password.min'                   => 'A senha deve ter no mínimo 8 caracteres.',
            'password.letters'               => 'A senha deve conter ao menos uma letra.',
            'password.mixed'                 => 'A senha deve conter letras maiúsculas e minúsculas.',
            'password.numbers'               => 'A senha deve conter ao menos um número.',
            'password.symbols'               => 'A senha deve conter ao menos um símbolo.',
        ];
    }
}
