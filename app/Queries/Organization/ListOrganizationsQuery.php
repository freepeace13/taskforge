<?php

namespace App\Queries\Organization;

class ListOrganizationsQuery
{
    public function __construct(
        public int $userId,
        public string $search = '',
        public int $perPage = 10
    ) {}
}
