<?php

namespace App\Queries\Organization;

use App\Models\User;
use Illuminate\Pagination\CursorPaginator;

class ListOrganizationsQueryHandler
{
    public function handle(ListOrganizationsQuery $query)
    {
        $user = User::findOrFail($query->userId);

        $builder = $user->organizations()
            ->when(filled($query->search), fn ($q) => $q->where('organizations.name', 'LIKE', '%'.$query->search.'%'));

        if ($query->shouldPaginate()) {
            return $builder->cursorPaginate($query->perPage);
        }

        return $builder->get();
    }
}
