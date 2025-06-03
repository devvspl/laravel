<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Adjust authorization logic as needed
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $userId = $this->route('users'); // Assuming route param name is 'user'

        return [
            'name' => 'required|string|max:255',
            'role_id' => 'required|array',
            'role_id.*' => 'string', // or 'integer' if roles IDs are integer type
            'is_active' => 'required|boolean',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'User name is required.',
            'role_id.required' => 'At least one role must be selected.',
            'role_id.array' => 'Roles must be an array.',
            'role_id.*.string' => 'Each role must be a valid string.',
            'is_active.required' => 'User status must be selected.',
            'is_active.boolean' => 'User status must be true or false.',
        ];
    }
}
