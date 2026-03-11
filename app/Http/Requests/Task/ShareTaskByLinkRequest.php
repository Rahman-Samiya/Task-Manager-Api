<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class ShareTaskByLinkRequest extends FormRequest
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
            'permission' => ['required', 'in:view,edit'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'permission.required' => 'Permission is required',
            'permission.in' => 'Permission must be one of: view, edit',
        ];
    }
}
