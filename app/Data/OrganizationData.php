<?php

namespace App\Data;

class OrganizationData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $logo
    ) {}
}
