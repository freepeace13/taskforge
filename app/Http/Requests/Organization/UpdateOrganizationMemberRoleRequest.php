<?php

namespace App\Http\Requests\Organization;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrganizationMemberRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'role' => ['required', Rule::in(Role::values())],
        ];
    }
}
