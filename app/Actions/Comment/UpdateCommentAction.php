<?php

namespace App\Actions\Comment;

use App\Contracts\Actions\Comment\UpdatesCommentAction as UpdatesCommentContract;
use App\Data\CommentData;
use App\Models\Comment;
use App\Models\User;
use App\Support\AuthorizesActions;

class UpdateCommentAction implements UpdatesCommentContract
{
    use AuthorizesActions;

    public function update(User $actor, Comment $comment, CommentData $data): Comment
    {
        $this->authorizeForUser($actor, 'update', $comment);

        $comment->update(['body' => $data->body]);

        return $comment->refresh();
    }
}
