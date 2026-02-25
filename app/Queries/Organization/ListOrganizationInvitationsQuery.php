<?php

namespace App\Queries\Organization;

class ListOrganizationInvitationsQuery
{
    public function __construct(
        public int $organizationId,
        public ?string $status,
        public ?string $search,
        public int $perPage = 10,
    ) {}
}
