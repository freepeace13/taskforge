<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;

class ActivityLogController extends Controller
{
    public function index(Request $request, Organization $org)
    {
        $logs = ActivityLog::query()
            ->where('organization_id', $org->id)
            ->paginate(10);

        return response()->json($logs);
    }

    public function projects(Request $request, Organization $org, Project $project)
    {
        abort_unless($project->organization->is($org), 403, 'Not your organization project.');

        $logs = ActivityLog::query()
            ->where('organization_id', $org->id)
            ->where('project_id', $project->id)
            ->paginate(10);

        return response()->json($logs);
    }

    public function tasks(Request $request, Organization $org, Task $task)
    {
        abort_unless($task->project->organization->is($org), 403, 'Not under your organization project tasks.');

        $logs = ActivityLog::query()
            ->where('organization_id', $org->id)
            ->where('task_id', $task->id)
            ->paginate(10);

        return response()->json($logs);
    }
}
