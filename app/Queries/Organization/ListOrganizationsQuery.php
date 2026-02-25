<?php

namespace App\Queries\Organization;

class ListOrganizationsQuery
{
    public int $perPage = 10;

    public function __construct(
        public int $userId,
        public string $search = ''
    ) {}

    public function shouldPaginate(): bool
    {
        return true;
    }
}
