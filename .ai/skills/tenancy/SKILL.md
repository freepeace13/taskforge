---
name: tenancy
description: Documents how Taskforge resolves multi-tenant scope via TenantContext and TenancyMiddleware (project routes, x-tenant-id, session), org membership checks, route model binding, and tests. Use when implementing or debugging tenant-scoped routes, APIs, policies, actions, Organization/Project/Task resolution, session tenant switching, or any work that requires tenancy context.
---

# Tenancy (Taskforge)

## Purpose

Taskforge is **organization-scoped**: an authenticated user acts within at most one **current organization** per request, represented by `App\Data\TenantContext`. This skill describes how that context is established, authorized, and consumed—so new code stays consistent and secure.

## Core type: `TenantContext`

Defined in `app/Data/TenantContext.php`:

- `user` — `App\Models\User`
- `organization` — `App\Models\Organization`
- `role` — `App\Enums\Role` (from `organization_members`)

The container may or may not have `TenantContext` bound. Code that **requires** a tenant must only run on routes where middleware has resolved it, or must validate scope explicitly.

**Helper:** `tenant()` in `app/Support/helpers.php` resolves `app(TenantContext::class)`. Only use when you know the binding exists (otherwise the container will throw).

## `TenancyMiddleware` — resolution order

Implementation: `app/Http/Middleware/TenancyMiddleware.php`.

1. **No user** → pass through without binding.
2. **`TenantContext` already bound** → pass through (idempotent).
3. **Route has `project` parameter** (web):
   - Value may be a raw id **or** a `Project` model depending on pipeline order; the middleware loads the project by id when needed.
   - If the project does not exist → pass through **without** binding (no tenant).
   - If the user is **not** a member of the project’s organization (`User::belongsToOrganization` via `HasOrganizations`) → **403 Forbidden**.
   - Otherwise → bind `TenantContext` for that organization and **persist `tenant_id` in the session** (`organization_id`).
4. **Otherwise** — resolve tenant id from:
   - `x-tenant-id` header, else
   - session key `tenant_id`
   - If still null → pass through without binding.
5. **Load org and membership:**
   - `Organization::findOrFail($tenantId)`
   - `OrganizationMember` for `(organization_id, user_id)` → **`firstOrFail`** (missing membership → **404**).
   - Bind `TenantContext` with the member’s `role`.

### Practical implications

- **Browser / Inertia:** Visiting `projects/{project}/…` sets tenant from the project and remembers it in the session for subsequent requests that do not include `{project}`.
- **API / clients:** Send **`x-tenant-id`** (organization id) on org-scoped requests when session is not used; authenticate with the API guard as configured (`routes/api.php` applies `TenancyMiddleware` with `auth:techysavvy`).
- **403 vs 404:** Forbidden when the user is authenticated but not allowed on a **known** project path; not-found when resolving tenant by id/header/session and there is no membership record.

## Middleware ordering

`bootstrap/app.php` prepends `TenancyMiddleware` **before** `SubstituteBindings` in the middleware priority list so tenant resolution can run with route parameters (including raw `project` ids) before or alongside binding, matching the middleware’s handling of both id and model.

## Route model binding and `org`

`AppServiceProvider::boot()` registers custom route bindings for `project`, `invite`, `task`, and `comment`. They constrain models by organization using:

- `$route->parameter('org')` when present, **or**
- `app()->bound(TenantContext::class) ? app(TenantContext::class)->organization : null`

So when routes use `{org:slug}` (or equivalent), binding scopes to that org; when `org` is absent, **`TenantContext` must already be set** for scoped resolution—otherwise behavior falls through to slug/id rules as implemented per binding.

When adding new bindings or nested resources, follow the same **org-first, then tenant context** pattern to avoid cross-tenant leaks.

## Policies and actions

Policies (e.g. `ProjectPolicy`, `TaskPolicy`) use `$user->belongsToOrganization($organization)` for authorization. Tenancy middleware ensures the request’s **selected** organization matches membership for header/session resolution; project-prefixed web routes additionally check access to the project’s org before binding.

## Testing

`tests/Concerns/InteractsWithTenant.php`:

- `setTenantContext($user, $organization, $role)` — binds `TenantContext` in the container for unit/feature tests.
- `actingAsInOrganization(...)` — sets API-style headers (`Authorization` for the test guard, `x-tenant-id`) **and** tenant context.
- `createOrganizationWithMember(...)` — factory helper for org + owner membership.

Use these instead of reimplementing tenant binding in each test.

## Frontend

There is **no** global Inertia prop for full `TenantContext` by default (see `HandleInertiaRequests`). UI that needs organization context typically receives it from **page props** for each route or derives navigation from URLs (e.g. project-scoped task routes). If you add shared tenant props, keep them in sync with server-side checks—**never** rely on the client alone for authorization.

## Checklist for tenancy-related changes

1. Confirm whether the request must have `TenantContext` bound; if yes, ensure the route is covered by middleware resolution (project path, `x-tenant-id`, or session `tenant_id` after a project visit).
2. Scope queries and route parameters to the current organization using bindings, policies, or explicit `tenant()->organization` where appropriate.
3. For tests, use `InteractsWithTenant` helpers.
4. Avoid calling `tenant()` on routes where middleware may skip binding (guest routes, tenant-less dashboard paths).

## Related

- **SSO / org sync:** `sso-auth-flow` skill when work also involves OAuth and syncing organizations from the auth server (`SyncAuthTenantsForUserAction`, etc.).
