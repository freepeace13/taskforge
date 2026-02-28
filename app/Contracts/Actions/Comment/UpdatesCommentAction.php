<?php

namespace App\Contracts\Actions\Comment;

use App\Data\CommentData;
use App\Models\Comment;
use App\Models\User;

interface UpdatesCommentAction
{
    public function update(User $actor, Comment $comment, CommentData $data): Comment;
}
