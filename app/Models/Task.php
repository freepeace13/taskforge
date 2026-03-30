<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function getRouteKeyName(): string
    {
        return 'key';
    }

    protected $fillable = [
        'project_id',
        'key',
        'assigned_to_user_id',
        'title',
        'description',
        'status', // todo / in_progress / done
        'priority', // low / medium / high
        'due_date',
        'completed_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (Task $task): void {
            if (! blank($task->key)) {
                return;
            }

            if (blank($task->project_id)) {
                return;
            }

            DB::transaction(function () use ($task): void {
                /** @var Project $project */
                $project = Project::query()
                    ->whereKey($task->project_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $next = (int) $project->task_sequence + 1;
                $project->forceFill(['task_sequence' => $next])->save();

                $task->key = sprintf('%s-%02d', $project->abbrev, $next);
            });
        });
    }

    public function reopen(): self
    {
        $this->status = 'todo';
        $this->completed_at = null;

        return $this;
    }

    public function complete(): self
    {
        $this->status = 'done';
        $this->completed_at = now();

        return $this;
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Organization members assigned to this task (collaborators).
     *
     * @return BelongsToMany<User, $this>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_user');
    }
}
