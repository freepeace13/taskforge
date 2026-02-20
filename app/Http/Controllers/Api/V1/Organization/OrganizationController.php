<?php

namespace App\Http\Controllers\Api\V1\Organization;

use App\Http\Controllers\Controller;
use Domains\Organization\Actions\CreateOrganizationAction;
use Domains\Organization\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $orgs = Organization::query()
            ->whereHas('members', fn ($q) => $q->where('users.id', $user->id))
            ->latest()
            ->paginate(10);

        return response()->json($orgs);
    }

    public function store(Request $request, CreateOrganizationAction $create)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
        ]);

        $user = $request->user();

        $org = $create->execute($user->id, $data['name']);

        return response()->json($org, 201);
    }

    public function show(Request $request, Organization $organization)
    {
        // Quick membership check (we’ll replace with Policy soon)
        $isMember = $organization->members()
            ->where('users.id', $request->user()->id)
            ->exists();

        abort_unless($isMember, 403);

        return response()->json($organization->load(['owner']));
    }
}
