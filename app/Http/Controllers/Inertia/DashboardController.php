<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'projects' => 12,
                'openTasks' => 48,
                'completed' => 103,
                'overdue' => 5,
            ],
        ]);
    }
}
