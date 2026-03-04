<?php

namespace App\Http\Middleware;

use App\Data\TenantContext;
use App\Models\Organization;
use App\Models\OrganizationMember;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenancyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $tenantId = $request->headers->get('x-tenant-id');

        if ($user && $tenantId) {
            $organization = Organization::findOrFail($tenantId);

            $member = OrganizationMember::query()
                ->where('organization_id', $organization->id)
                ->where('user_id', $request->user()?->id)
                ->firstOrFail();

            $context = new TenantContext(
                user: $user,
                organization: $organization,
                role: $member->role
            );

            app()->instance(TenantContext::class, $context);
        }

        return $next($request);
    }
}
