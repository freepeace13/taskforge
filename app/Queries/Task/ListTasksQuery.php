<?php

namespace App\Queries\Task;

class ListTasksQuery
{
    public function __construct(
        public int $projectId,
        public ?int $perPage = null,
    ) {}
}
