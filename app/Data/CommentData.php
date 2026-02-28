<?php

namespace App\Data;

class CommentData
{
    public function __construct(
        public readonly string $body,
    ) {}
}
