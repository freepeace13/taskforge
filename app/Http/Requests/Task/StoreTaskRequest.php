<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
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
        $project = $this->route('project');
        $organizationId = $project->organization_id;

        return [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'priority' => ['nullable', 'string', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
            'assigned_to_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'member_ids' => ['sometimes', 'array'],
            'member_ids.*' => [
                'integer',
                Rule::exists('organization_user', 'user_id')
                    ->where('organization_id', $organizationId),
            ],
        ];
    }
}
