<?php

namespace App\Http\Middleware;

use App\Data\TenantContext;
use App\Http\Middleware\Concerns\ResolvesTenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Establishes {@see TenantContext} for stateless API requests.
 *
 * Resolution order: `org` route parameter (e.g. `orgs/{org:slug}`), `project` route parameter,
 * then `x-tenant-id` only (no session fallback or session writes).
 */
class ApiTenancyMiddleware
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

        if ($this->resolveFromOrgRouteParameter($request, $user, writeSessionWhenResolved: false)) {
            return $next($request);
        }

        if ($this->resolveFromProjectRouteParameter($request, $user, writeSessionWhenResolved: false)) {
            return $next($request);
        }

        if ($this->resolveFromTenantIdentifier($request, $user, fallbackToSession: false)) {
            return $next($request);
        }

        return $next($request);
    }
}
