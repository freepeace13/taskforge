<?php

namespace App\Actions\Comment;

use App\Contracts\Actions\Comment\DeletesCommentAction as DeletesCommentContract;
use App\Models\Comment;
use App\Models\User;

class DeleteCommentAction implements DeletesCommentContract
{
    public function delete(User $actor, Comment $comment)
    {
        $project = $comment->task->project;

        if (!$actor->belongsToOrganization($project->organization)) {
            throw new \Exception('You are not a member of this organization.');
        }

        if ($actor->isNot($comment->user)) {
            throw new \Exception('You cannot delete someone else comment!');
        }

        $comment->delete();
    }
}
