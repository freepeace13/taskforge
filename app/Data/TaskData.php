<?php

namespace App\Data;

use App\Models\Task;
use Carbon\CarbonInterface;

class TaskData
{
    /**
     * @param  list<int>|null  $memberIds
     */
    public function __construct(
        public readonly string $title,
        public readonly ?string $description = null,
        public readonly ?string $priority = null,
        public readonly ?string $dueDate = null,
        public readonly ?string $status = null,
        public readonly ?array $memberIds = null,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function mergeForUpdate(Task $task, array $validated): self
    {
        $dueDate = array_key_exists('due_date', $validated)
            ? $validated['due_date']
            : self::formatDueDateFromTask($task);

        return new self(
            title: $validated['title'] ?? $task->title,
            description: array_key_exists('description', $validated) ? $validated['description'] : $task->description,
            priority: array_key_exists('priority', $validated) ? $validated['priority'] : $task->priority,
            dueDate: $dueDate,
            status: $validated['status'] ?? $task->status,
            memberIds: array_key_exists('member_ids', $validated) ? $validated['member_ids'] : null,
        );
    }

    private static function formatDueDateFromTask(Task $task): ?string
    {
        $dueDate = $task->due_date;

        if ($dueDate === null) {
            return null;
        }

        if ($dueDate instanceof CarbonInterface) {
            return $dueDate->format('Y-m-d');
        }

        return (string) $dueDate;
    }
}
