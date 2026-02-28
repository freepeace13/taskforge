<?php

namespace App\Data;

class ProjectData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description
    ) {}
}
