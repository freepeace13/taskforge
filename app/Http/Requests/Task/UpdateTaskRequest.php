<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'priority' => $this->priority === '' ? null : $this->priority,
            'due_date' => $this->due_date === '' ? null : $this->due_date,
            'description' => $this->description === '' ? null : $this->description,
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'description' => ['sometimes', 'nullable', 'string'],
            'priority' => ['sometimes', 'nullable', 'string', 'in:low,medium,high'],
            'due_date' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
