<?php

namespace App\Contracts\Queries\Task;

use App\Queries\Task\ListTasksQuery;
use Illuminate\Pagination\LengthAwarePaginator;

interface ListsTasks
{
    public function handle(ListTasksQuery $query): LengthAwarePaginator;
}
