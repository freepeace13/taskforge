<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    public function create(User $user, Project $project)
    {
        $organization = $project->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        return true;
    }

    public function update(User $user, Task $task)
    {
        $organization = $task->project->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        return true;
    }

    public function assign(User $user, Task $task, ?int $userId = null)
    {
        $organization = $task->project->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        if ($userId) {
            return $organization->members()
                ->where('users.id', $userId)
                ->exists();
        }

        return true;
    }

    public function reopen(User $user, Task $task)
    {
        $organization = $task->project->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        return true;
    }

    public function complete(User $user, Task $task)
    {
        $organization = $task->project->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        return true;
    }

    public function delete(User $user, Task $task)
    {
        $organization = $task->project->organization;

        if (! $user->belongsToOrganization($organization)) {
            return Response::denyAsNotFound();
        }

        return true;
    }
}
