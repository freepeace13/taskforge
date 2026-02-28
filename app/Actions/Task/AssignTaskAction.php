<?php

namespace App\Actions\Task;

use App\Contracts\Actions\Task\AssignsTaskAction as AssignsTaskContract;
use App\Models\OrganizationMember;
use App\Models\Task;
use App\Models\User;
use Exception;

class AssignTaskAction implements AssignsTaskContract
{
    public function assign(User $actor, Task $task, ?int $userId = null): Task
    {
        $project = $task->project;

        if (!$actor->belongsToOrganization($project->organization)) {
            throw new \Exception('You are not belong to this organization.');
        }

        $this->ensureAssigneeIsMember(
            organizationId: $project->organization_id,
            userId: $userId,
        );

        $task->assigned_to_user_id = $userId;
        $task->save();

        return $task;
    }

    protected function ensureAssigneeIsMember(int $organizationId, int $userId): void
    {
        $isMember = OrganizationMember::query()
            ->where('organization_id', $organizationId)
            ->where('user_id', $userId)
            ->exists();

        throw_unless($isMember, Exception::class, 'Assignee must be a member of the organization.');
    }
}
