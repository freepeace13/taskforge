<?php

namespace App\Queries\Task;

use App\Contracts\Queries\Task\ListsTasks;
use App\Models\Project;
use Illuminate\Pagination\LengthAwarePaginator;

class ListTasksQueryHandler implements ListsTasks
{
    public function handle(ListTasksQuery $query): LengthAwarePaginator
    {
        $perPage = $query->perPage ?? 15;

        return Project::query()
            ->findOrFail($query->projectId)
            ->tasks()
            ->with('members')
            ->latest('id')
            ->paginate($perPage);
    }
}
