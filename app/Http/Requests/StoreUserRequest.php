<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|array',
            'role_id.*' => 'exists:roles,id', // Ensure each role_id exists in the roles table
            'is_active' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'User name is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'Email is already taken.',
            'password.required' => 'Password is required.',
            'role_id.required' => 'At least one role must be selected.',
            'role_id.*.exists' => 'One or more selected roles are invalid.',
            'is_active.required' => 'User status must be selected.',
        ];
    }
}
