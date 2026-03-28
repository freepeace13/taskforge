<?php

namespace App\Contracts\Queries\Project;

use App\Queries\Project\ListProjectsQuery;
use Illuminate\Pagination\LengthAwarePaginator;

interface ListsProjects
{
    public function handle(ListProjectsQuery $query): LengthAwarePaginator;
}
