<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'organization_id',
        'actor_user_id',
        'project_id',
        'task_id',
        'event',
        'meta',
    ];
}
