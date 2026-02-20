<?php

namespace Domains\Project\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'assigned_to_user_id',
        'title',
        'description',
        'status', // todo / in_progress / done
        'priority', // low / medium / high
        'due_date',
        'completed_at'
    ];
}
