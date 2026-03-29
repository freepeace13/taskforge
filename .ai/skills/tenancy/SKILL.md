---
name: tenancy
description: Documents how Taskforge resolves multi-tenant scope via TenantContext, WebTenancyMiddleware, and ApiTenancyMiddleware (Inertia org routes, API x-tenant-id, session), org membership checks, route model binding, and tests. Use when implementing or debugging tenant-scoped routes, APIs, policies, actions, Organization/Project/Task resolution, session tenant switching, or any work that requires tenancy context.
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

## Web and API tenancy middleware — resolution order

Shared logic lives in `app/Http/Middleware/Concerns/ResolvesTenantContext.php` (membership checks, binding `TenantContext`).

- **`WebTenancyMiddleware`** (`app/Http/Middleware/WebTenancyMiddleware.php`) — Inertia / session-backed requests. Registered on the `web` middleware group in `bootstrap/app.php`.
- **`ApiTenancyMiddleware`** (`app/Http/Middleware/ApiTenancyMiddleware.php`) — API routes in `routes/api.php` (with `auth:techysavvy`). Does **not** read session `tenant_id` for fallback and does **not** write `tenant_id` to the session when resolving from URL parameters.

For both, the pipeline is:

1. **No user** → pass through without binding.
2. **`TenantContext` already bound** → pass through (idempotent).
3. **Route has `org` parameter** (Inertia web and some API routes, e.g. `orgs/{org:slug}`):
   - Value may be a raw slug **or** an `Organization` model depending on pipeline order; the middleware resolves the organization by slug when needed.
   - If the slug does not match any organization → **404 Not Found**.
   - If the user is **not** a member of that organization (`OrganizationMember` lookup) → **404** (via `firstOrFail`).
   - Otherwise → bind `TenantContext` for that organization. **Web only:** persist `tenant_id` in the session (`organization_id`).
4. **Route has `project` parameter** (when value is a raw id, not yet a `Project` model):
   - If the project does not exist → pass through **without** binding (no tenant).
   - If the user is **not** a member of the project’s organization (`User::belongsToOrganization` via `HasOrganizations`) → **403 Forbidden**.
   - Otherwise → bind `TenantContext` for that organization. **Web only:** persist `tenant_id` in the session (`organization_id`).
5. **Otherwise** — resolve tenant id from:
   - **`WebTenancyMiddleware`:** `x-tenant-id` header, else session key `tenant_id`.
   - **`ApiTenancyMiddleware`:** `x-tenant-id` header only.
   - If still null → pass through without binding.
6. **Load org and membership** (header/session path):
   - `Organization::findOrFail($tenantId)`
   - `OrganizationMember` for `(organization_id, user_id)` → **`firstOrFail`** (missing membership → **404**).
   - Bind `TenantContext` with the member’s `role`.

### Practical implications

- **Browser / Inertia:** Organization-scoped pages live under **`/orgs/{org:slug}/…`** (e.g. `/orgs/{org}/projects`, nested project/task routes). The first `org` segment establishes tenant context and updates the session. **Shared Inertia props** (`HandleInertiaRequests`) expose `tenantOrganization` when `TenantContext` is bound so navigation (e.g. sidebar “Projects”) can link into the current org without guessing URLs.
- **API / clients:** Send **`x-tenant-id`** (organization id) on org-scoped requests; authenticate with the API guard as configured (`routes/api.php` applies `ApiTenancyMiddleware` with `auth:techysavvy`). Many routes use **`/api/v1/...`** with tenant scope from the header (and middleware); some routes include `org` in the path.
- **403 vs 404:** Forbidden when the user is authenticated but not allowed on a **known** project path (legacy project-only resolution); not-found when resolving tenant by id/header/session/org slug and there is no membership record (or unknown org slug).

## Middleware ordering

`bootstrap/app.php` prepends **`WebTenancyMiddleware`** and **`ApiTenancyMiddleware`** **before** `SubstituteBindings` in the middleware priority list so tenant resolution can run with route parameters (including raw `project` ids) before or alongside binding, matching the middleware’s handling of both id and model.

**Web stack:** `WebTenancyMiddleware` is registered **before** `HandleInertiaRequests` so shared Inertia data can safely depend on `TenantContext` (e.g. `tenantOrganization`).

## Route model binding and `org`

`AppServiceProvider::boot()` registers custom route bindings for `project`, `invite`, `task`, and `comment`. They constrain models by organization using:

- `$route->parameter('org')` when present, **or**
- `app()->bound(TenantContext::class) ? app(TenantContext::class)->organization : null`

So when routes use `{org}` in the path (Inertia web), binding scopes to that org; when `org` is absent, **`TenantContext` must already be set** for scoped resolution—otherwise behavior falls through to slug/id rules as implemented per binding.

When adding new bindings or nested resources, follow the same **org-first, then tenant context** pattern to avoid cross-tenant leaks.

## Policies and actions

Policies (e.g. `ProjectPolicy`, `TaskPolicy`) use `$user->belongsToOrganization($organization)` for authorization. Tenancy middleware ensures the request’s **selected** organization matches membership for header/session resolution; org-prefixed web routes additionally resolve tenant from the URL segment before controllers run.

## Testing

`tests/Concerns/InteractsWithTenant.php`:

- `setTenantContext($user, $organization, $role)` — binds `TenantContext` in the container for unit/feature tests.
- `actingAsInOrganization(...)` — sets API-style headers (`Authorization` for the test guard, `x-tenant-id`) **and** tenant context.
- `createOrganizationWithMember(...)` — factory helper for org + owner membership.
- `forgetTenantContext()` — clears a previously bound `TenantContext` in the container (use in a single test when switching users or tenant headers after `setTenantContext` / `actingAsInOrganization`).

Use these instead of reimplementing tenant binding in each test.

For **Inertia feature tests**, pass **`org` => `$organization->slug`** in `route()` for `projects.*` and `projects.tasks.*` (or rely on session + `tenant_id` after visiting an org-scoped URL).

## Frontend

Use **`route('name', { org: organization.slug, ... })`** for project-scoped URLs. `tenantOrganization` in shared props (when present) supplies the current org slug for shell navigation (e.g. sidebar link to `projects.index`).

If you add more shared tenant props, keep them in sync with server-side checks—**never** rely on the client alone for authorization.

## Checklist for tenancy-related changes

1. Confirm whether the request must have `TenantContext` bound; if yes, ensure the route is covered by middleware resolution (org path, project path, `x-tenant-id`, or session `tenant_id` after a tenant visit).
2. Scope queries and route parameters to the current organization using bindings, policies, or explicit `tenant()->organization` where appropriate.
3. For tests, use `InteractsWithTenant` helpers and pass `org` for web routes named under `orgs/{org}`.
4. Avoid calling `tenant()` on routes where middleware may skip binding (guest routes, tenant-less dashboard paths).

## Related

- **SSO / org sync:** `sso-auth-flow` skill when work also involves OAuth and syncing organizations from the auth server (`SyncAuthTenantsForUserAction`, etc.).
