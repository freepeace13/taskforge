<?php

namespace App\Actions\Comment;

use App\Contracts\Actions\Comment\DeletesCommentAction as DeletesCommentContract;
use App\Models\Comment;
use App\Models\User;
use App\Support\AuthorizesActions;

class DeleteCommentAction implements DeletesCommentContract
{
    use AuthorizesActions;

    public function delete(User $actor, Comment $comment)
    {
        $this->authorizeForUser($actor, 'delete', $comment);

        $comment->delete();
    }
}
