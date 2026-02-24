<?php

namespace App\Queries\Organization;

use App\Models\User;
use Illuminate\Pagination\CursorPaginator;

class ListOrganizationsQueryHandler
{
    public function handle(ListOrganizationsQuery $query): CursorPaginator
    {
        $user = User::findOrFail($query->userId);

        return $user->organizations()
            ->when(filled($query->search), fn ($q) => $q->where('organizations.name', 'LIKE', '%'.$query->search.'%'))
            ->cursorPaginate($query->perPage);
    }
}
