<?php

namespace App\Http\Middleware;

use App\Data\TenantContext;
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
        $org = $request->route('org');
        $user = $request->user();

        $member = OrganizationMember::query()
            ->where('organization_id', $org->id)
            ->where('user_id', $user->id)
            ->first();

        abort_if(is_null($member), 403, 'You are not a member of this organization.');

        $tenant = new TenantContext(
            userId: $user->id,
            organizationId: $org->id,
            role: $member->role
        );

        app()->instance(TenantContext::class, $tenant);

        return $next($request);
    }
}
