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
        return Inertia::render('Dashboard', [
            'organization' => [
                'slug' => $org->slug,
                'name' => $org->name,
            ],
        ]);
    }
}
