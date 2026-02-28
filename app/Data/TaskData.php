<?php

namespace App\Data;

class TaskData
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description = null,
        public readonly ?string $priority = null,
        public readonly ?string $dueDate = null,
    ) {}
}
