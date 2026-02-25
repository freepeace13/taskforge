<?php

namespace App\Queries\Organization;

class ListMembersQuery
{
    public int $perPage = 10;

    public function __construct(
        public int $organizationId,
        public ?string $search,
        public ?string $roles,
    ) {}

    public function shouldPaginate(): bool
    {
        return true;
    }
}
