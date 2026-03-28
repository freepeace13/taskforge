<?php

namespace App\Contracts\Queries\Task;

use App\Queries\Task\ListTaskHubOrganizationsQuery;
use Illuminate\Support\Collection;

interface ListsTaskHubOrganizations
{
    /**
     * @return Collection<int, \App\Models\Organization>
     */
    public function handle(ListTaskHubOrganizationsQuery $query): Collection;
}
