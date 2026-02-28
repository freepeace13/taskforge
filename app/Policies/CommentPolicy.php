<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    public function create(User $user, Task $task)
    {
        $organization = $task->project->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
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

        return $task->user->is($user);
    }

    public function delete(User $user, Comment $comment)
    {
        $task = $comment->task;
        $organization = $task->project->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        // Owner can delete any member's comment
        if ($task->user->isNot($user)) {
            return $user->organizationRole($organization) === Role::Owner;
        }

        return true;
    }
}
