<?php

namespace App\Contracts\Actions\Comment;

use App\Data\CommentData;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;

interface CreatesCommentAction
{
    public function create(User $actor, Task $task, CommentData $data): Comment;
}
