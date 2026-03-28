<?php

namespace App\Queries\Task;

use App\Contracts\Queries\Task\ListsTaskHubOrganizations;
use App\Models\User;
use Illuminate\Support\Collection;

class ListTaskHubOrganizationsQueryHandler implements ListsTaskHubOrganizations
{
    /**
     * @return Collection<int, \App\Models\Organization>
     */
    public function handle(ListTaskHubOrganizationsQuery $query): Collection
    {
        $user = User::findOrFail($query->userId);

        return $user->organizations()
            ->with(['projects' => fn ($q) => $q->whereNull('archived_at')->orderBy('name')])
            ->orderBy('name')
            ->get();
    }
}
