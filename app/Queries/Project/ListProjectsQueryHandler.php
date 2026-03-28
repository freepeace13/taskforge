<?php

namespace App\Queries\Project;

use App\Contracts\Queries\Project\ListsProjects;
use App\Models\Organization;
use Illuminate\Pagination\LengthAwarePaginator;

class ListProjectsQueryHandler implements ListsProjects
{
    public function handle(ListProjectsQuery $query): LengthAwarePaginator
    {
        $organization = Organization::query()->findOrFail($query->organizationId);

        $builder = $organization->projects()->latest('id');

        if ($query->archivedFilter === 'active') {
            $builder->whereNull('archived_at');
        } elseif ($query->archivedFilter === 'archived') {
            $builder->whereNotNull('archived_at');
        }

        $perPage = $query->perPage ?? 15;

        return $builder->paginate($perPage);
    }
}
