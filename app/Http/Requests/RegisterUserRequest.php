<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:50', 'unique:users,name'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8),
                'regex:/^(?=(?:.*[A-Z]){2})(?=(?:.*\d){2})(?=.*[^A-Za-z0-9]).+$/',
            ],
        ];
    }

    /**
     * Get the validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'username.unique' => 'This username is already registered.',
            'email.unique' => 'This email is already registered.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.regex' => 'The password must contain at least 2 uppercase letters, 2 numbers, and 1 special character.',
        ];
    }
}
