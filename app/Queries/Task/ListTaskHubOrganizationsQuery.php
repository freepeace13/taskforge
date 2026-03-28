<?php

namespace App\Queries\Task;

class ListTaskHubOrganizationsQuery
{
    public function __construct(
        public int $userId,
    ) {}
}
