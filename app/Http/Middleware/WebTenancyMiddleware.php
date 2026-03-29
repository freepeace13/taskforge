<?php

namespace App\Http\Middleware;

use App\Data\TenantContext;
use App\Http\Middleware\Concerns\ResolvesTenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Establishes {@see TenantContext} for Inertia / session-backed requests.
 *
 * Resolution order: `org` route segment, `project` route segment, then `x-tenant-id` or session `tenant_id`.
 * Persists the selected organization id in the session when resolved from URL parameters.
 */
class WebTenancyMiddleware
{
    use ResolvesTenantContext;

    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || app()->bound(TenantContext::class)) {
            return $next($request);
        }

        if ($this->resolveFromOrgRouteParameter($request, $user, writeSessionWhenResolved: true)) {
            return $next($request);
        }

        if ($this->resolveFromProjectRouteParameter($request, $user, writeSessionWhenResolved: true)) {
            return $next($request);
        }

        if ($this->resolveFromTenantIdentifier($request, $user, fallbackToSession: true)) {
            return $next($request);
        }

        return $next($request);
    }
}
