<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Http\Requests\Workspace\StoreWorkspaceRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WorkspaceController extends Controller
{
    public function index(Request $request): Response
    {
        $organizations = $request->user()
            ->organizations()
            ->orderBy('name')
            ->get()
            ->map(fn (Organization $org) => [
                'id' => $org->id,
                'name' => $org->name,
                'slug' => $org->slug,
                'role' => $org->pivot->role->value,
            ])
            ->values()
            ->all();

        return Inertia::render('Workspaces', [
            'organizations' => $organizations,
        ]);
    }

    public function store(StoreWorkspaceRequest $request): RedirectResponse
    {
        $organization = Organization::query()->findOrFail($request->validated('organization_id'));

        $request->session()->put('tenant_id', $organization->id);

        return redirect()->route('projects.index', ['org' => $organization->slug]);
    }
}
