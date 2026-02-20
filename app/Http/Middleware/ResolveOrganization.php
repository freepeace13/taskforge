<?php

namespace App\Http\Middleware;

use Closure;
use Domains\Organization\Contracts\OrganizationContextResolver;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveOrganization
{
    public function __construct(
        private readonly OrganizationContextResolver $resolver
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $orgSlug = (string) $request->route('org');
        $userId  = (int) $request->user()->id;

        try {
            $ctx = $this->resolver->resolveForUser($orgSlug, $userId);
        } catch (ModelNotFoundException) {
            abort(404, 'Organization not found.');
        } catch (AuthorizationException $e) {
            abort(403, $e->getMessage());
        }

        // Store full context (org + role) for downstream usage
        $request->attributes->set('tenant', $ctx);
        app()->instance('tenant', $ctx);

        return $next($request);
    }
}
