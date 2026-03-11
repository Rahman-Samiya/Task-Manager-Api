<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'status' => ['sometimes', 'in:todo,in_progress,in_review,done'],
            'is_completed' => ['nullable', 'boolean'],
            'deadline' => ['nullable', 'date', 'after:today'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.max' => 'Title must not exceed 255 characters',
            'priority.in' => 'Priority must be one of: low, medium, high',
            'status.in' => 'Status must be one of: todo, in_progress, in_review, done',
            'deadline.date' => 'Deadline must be a valid date',
            'deadline.after' => 'Deadline must be a future date',
        ];
    }
}
