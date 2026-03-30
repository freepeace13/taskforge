<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        if ($this->has('priority')) {
            $merge['priority'] = $this->priority === '' ? null : $this->priority;
        }

        if ($this->has('due_date')) {
            $merge['due_date'] = $this->due_date === '' ? null : $this->due_date;
        }

        if ($this->has('description')) {
            $merge['description'] = $this->description === '' ? null : $this->description;
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $project = $this->route('project');
        $organizationId = $project->organization_id;

        return [
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'description' => ['sometimes', 'nullable', 'string'],
            'priority' => ['sometimes', 'nullable', 'string', 'in:low,medium,high'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'status' => ['sometimes', 'string', 'in:todo,in_progress,done'],
            'redirect_to_board' => ['sometimes', 'boolean'],
            'redirect_back' => ['sometimes', 'boolean'],
            'member_ids' => ['sometimes', 'array'],
            'member_ids.*' => [
                'integer',
                Rule::exists('organization_user', 'user_id')
                    ->where('organization_id', $organizationId),
            ],
        ];
    }
}
