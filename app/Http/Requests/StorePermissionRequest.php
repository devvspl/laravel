<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
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
            'permission_name' => 'required|string|max:255',
            'group_id' => 'required|exists:permission_groups,id',
            'is_active' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'permission_name.required' => 'The permission name is required.',
            'group_id.required' => 'The group is required.',
            'is_active.required' => 'The active status is required.',
        ];
    }
}
