<?php

namespace App\Actions\Task;

use App\Contracts\Actions\Task\DeletesTaskAction as DeletesTaskContract;
use App\Models\Task;
use App\Models\User;
use App\Support\AuthorizesActions;

class DeleteTaskAction implements DeletesTaskContract
{
    use AuthorizesActions;

    public function delete(User $actor, Task $task): void
    {
        $this->authorizeForUser($actor, 'delete', $task);

        $task->delete();
    }
}
