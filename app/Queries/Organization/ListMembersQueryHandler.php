<?php


namespace App\Queries\Organization;

use App\Models\Organization;
use Illuminate\Pagination\CursorPaginator;

class ListMembersQueryHandler
{
    public function handle(ListMembersQuery $query): CursorPaginator
    {
        $org = Organization::findOrFail($query->organizationId);

        return $org->members()
            ->when(filled($query->roles), fn ($q) => $q->wherePivotIn('role', $query->roles))
            ->when(filled($query->search), fn ($q) => $q->where('users.name', 'LIKE', '%'.$query->search.'%')
                ->orWhere('users.email', 'LIKE', '%'.$query->search.'%'))
            ->cursorPaginate($query->perPage);
    }
}
