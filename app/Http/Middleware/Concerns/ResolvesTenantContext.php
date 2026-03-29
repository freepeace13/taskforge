<?php

namespace App\Http\Middleware\Concerns;

use App\Data\TenantContext;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

trait ResolvesTenantContext
{
    protected function bindTenantContext(User $user, Organization $organization): void
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

    /**
     * Resolve tenant from `org` route parameter (slug or bound model).
     *
     * @param  bool  $writeSessionWhenResolved  When true, persist `tenant_id` for browser sessions.
     */
    protected function resolveFromOrgRouteParameter(Request $request, User $user, bool $writeSessionWhenResolved): bool
    {
        $orgParam = $request->route('org');
        if ($orgParam === null) {
            return false;
        }

        $organization = $orgParam instanceof Organization
            ? $orgParam
            : Organization::query()->where('slug', $orgParam)->first();

        if ($organization === null) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $this->bindTenantContext($user, $organization);

        if ($writeSessionWhenResolved && $request->hasSession()) {
            $request->session()->put('tenant_id', $organization->id);
        }

        return true;
    }

    /**
     * Resolve tenant from `project` route parameter when not already a Project model.
     *
     * @param  bool  $writeSessionWhenResolved  When true, persist `tenant_id` for browser sessions.
     */
    protected function resolveFromProjectRouteParameter(Request $request, User $user, bool $writeSessionWhenResolved): bool
    {
        $projectParam = $request->route('project');
        if ($projectParam === null || $projectParam instanceof Project) {
            return false;
        }

        $project = Project::query()->find($projectParam);
        if ($project === null) {
            return false;
        }

        if (! $user->belongsToOrganization($project->organization)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $this->bindTenantContext($user, $project->organization);

        if ($writeSessionWhenResolved && $request->hasSession()) {
            $request->session()->put('tenant_id', $project->organization_id);
        }

        return true;
    }

    /**
     * Resolve tenant from `x-tenant-id` and optionally session `tenant_id`.
     */
    protected function resolveFromTenantIdentifier(Request $request, User $user, bool $fallbackToSession): bool
    {
        $tenantId = $request->headers->get('x-tenant-id');

        if ($tenantId === null && $fallbackToSession && $request->hasSession()) {
            $tenantId = $request->session()->get('tenant_id');
        }

        if ($tenantId === null) {
            return false;
        }

        $organization = Organization::findOrFail($tenantId);
        $this->bindTenantContext($user, $organization);

        return true;
    }
}
