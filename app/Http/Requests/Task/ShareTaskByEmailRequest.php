<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class ShareTaskByEmailRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'permission' => ['required', 'in:view,edit,admin'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email is required',
            'email.email' => 'Please provide a valid email',
            'email.exists' => 'User with this email does not exist',
            'permission.required' => 'Permission is required',
            'permission.in' => 'Permission must be one of: view, edit, admin',
        ];
    }
}
