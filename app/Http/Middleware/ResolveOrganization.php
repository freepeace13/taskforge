<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use App\Models\OrganizationMember;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveOrganization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $orgSlug = (string) $request->route('org');
        $userId  = (int) $request->user()->id;

        $org = Organization::query()
            ->where('slug', $orgSlug)
            ->firstOrFail();

        $userId = (int) $request->user()->id;

        $isMember = OrganizationMember::query()
            ->where('organization_id', $org->id)
            ->where('user_id', $userId)
            ->exists();

        abort_unless($isMember, 403, 'You are not a member of this organization.');

        // Store current org in request attributes + container for easy access
        $request->attributes->set('tenant', $org);
        app()->instance('tenant', $org);

        return $next($request);
    }
}
