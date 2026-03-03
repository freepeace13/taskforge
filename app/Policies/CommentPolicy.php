<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    public function viewAny(User $user, Task $task)
    {
        return $this->create($user, $task);
    }

    public function create(User $user, Task $task)
    {
        $organization = $task->project->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound('You are not a member of this organization.');
        }

        return true;
    }

    public function update(User $user, Comment $comment)
    {
        $task = $comment->task;
        $organization = $task->project->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        if ($comment->user->isNot($user)) {
            return Response::deny('You are not allowed to update this comment.');
        }

        return true;
    }

    public function delete(User $user, Comment $comment)
    {
        $task = $comment->task;
        $organization = $task->project->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        // Owner can delete any member's comment
        if ($comment->user->isNot($user)) {
            if ($user->organizationRole($organization) === Role::Owner) {
                return true;
            }

            return Response::deny('You are not allowed to delete this comment.');
        }

        return true;
    }
}
