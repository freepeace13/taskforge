<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Organization $org, Request $request)
    {
        /** @var array{projects: int, openTasks: int, completed: int, overdue: int} $stats */
        $stats = [
            'projects' => 12,
            'openTasks' => 34,
            'completed' => 128,
            'overdue' => 3,
        ];

        return Inertia::render('Dashboard', [
            'organization' => [
                'slug' => $org->slug,
                'name' => $org->name,
            ],
            'stats' => $stats,
        ]);
    }
}
