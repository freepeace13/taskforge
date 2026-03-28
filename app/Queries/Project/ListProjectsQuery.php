<?php

namespace App\Queries\Project;

class ListProjectsQuery
{
    /**
     * @param  'active'|'archived'  $archivedFilter
     */
    public function __construct(
        public int $organizationId,
        public string $archivedFilter = 'active',
        public ?int $perPage = null,
    ) {}
}
