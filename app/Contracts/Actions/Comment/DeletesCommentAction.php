<?php

namespace App\Contracts\Actions\Comment;

use App\Models\Comment;
use App\Models\User;

interface DeletesCommentAction
{
    public function delete(User $actor, Comment $comment);
}
