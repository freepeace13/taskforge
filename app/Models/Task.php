<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'assigned_to_user_id',
        'title',
        'description',
        'status', // todo / in_progress / done
        'priority', // low / medium / high
        'due_date',
        'completed_at',
    ];

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
}
