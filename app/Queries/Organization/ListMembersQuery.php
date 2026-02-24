<?php

namespace App\Queries\Organization;

class ListMembersQuery
{
    public function __construct(
        public int $organizationId,
        public string $search = '',
        public array $roles = [],
        public int $perPage = 10
    ) {}
}
