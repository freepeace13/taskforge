<?php

namespace App\Http\Middleware;

use App\Data\TenantContext;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Project;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenancyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Resolves {@see TenantContext} from (in order): project in the current route (web),
     * `x-tenant-id` header, then session `tenant_id`.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || app()->bound(TenantContext::class)) {
            return $next($request);
        }

        $projectParam = $request->route('project');
        if ($projectParam !== null && ! $projectParam instanceof Project) {
            $project = Project::query()->find($projectParam);
            if ($project === null) {
                return $next($request);
            }

            if (! $user->belongsToOrganization($project->organization)) {
                abort(Response::HTTP_FORBIDDEN);
            }

            $this->bindTenantContext($user, $project->organization);
            if ($request->hasSession()) {
                $request->session()->put('tenant_id', $project->organization_id);
            }

            return $next($request);
        }

        $tenantId = $request->headers->get('x-tenant-id');

        if ($tenantId === null && $request->hasSession()) {
            $tenantId = $request->session()->get('tenant_id');
        }

        if ($tenantId === null) {
            return $next($request);
        }

        $organization = Organization::findOrFail($tenantId);

        $member = OrganizationMember::query()
            ->where('organization_id', $organization->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $context = new TenantContext(
            user: $user,
            organization: $organization,
            role: $member->role
        );

        app()->instance(TenantContext::class, $context);

        return $next($request);
    }

    private function bindTenantContext(User $user, Organization $organization): void
    {
        $member = OrganizationMember::query()
            ->where('organization_id', $organization->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        app()->instance(TenantContext::class, new TenantContext(
            user: $user,
            organization: $organization,
            role: $member->role
        ));
    }
}
