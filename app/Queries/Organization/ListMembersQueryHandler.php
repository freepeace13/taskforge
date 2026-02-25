<?php


namespace App\Queries\Organization;

use App\Enums\Role;
use App\Models\Organization;
use Illuminate\Pagination\CursorPaginator;

class ListMembersQueryHandler
{
    public function handle(ListMembersQuery $query)
    {
        $roles = explode(',', $query->roles ?? implode(',', Role::values()));

        $org = Organization::findOrFail($query->organizationId);

        $builder = $org->members()
            ->whereIn('organization_user.role', $roles)
            ->when(filled($query->search), fn ($q) => $q->where('users.name', 'LIKE', '%'.$query->search.'%')
                ->orWhere('users.email', 'LIKE', '%'.$query->search.'%'));

        if ($query->shouldPaginate()) {
            return $builder->cursorPaginate($query->perPage);
        }

        return $builder->get();
    }
}
