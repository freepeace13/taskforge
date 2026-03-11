---
name: sso-auth-flow
description: Guides work on the SSO authentication client in this Taskforge app that integrates with the external auth server. Use when implementing, debugging, or extending SSO login redirects, callbacks, token handling, tenant-aware auth, or React/Inertia flows that depend on this auth server.
---

# SSO Authentication Flow (Client / Consumer)

## Purpose

This skill helps the agent work on the **SSO client side** in this Taskforge application, which:

- Relies on an external SSO auth server for login.
- Initiates redirects to the auth server and handles the callback.
- Stores and uses issued tokens to authenticate API calls.
- Loads user identity (and tenant context) from the auth server and exposes it to the UI.

Use this skill whenever tasks involve:

- Wiring the app’s login/logout to the external SSO server.
- Handling the OAuth/SSO callback (code exchange, state validation, error handling).
- Persisting and refreshing tokens for authenticated requests.
- Making authenticated calls to the auth server (e.g. `/me`, tenant info).
- Propagating user and tenant data into Inertia/React layouts and pages.

## Quick Start Checklist

When working on SSO-related client changes in this repo:

1. **Locate the existing integration**
   - Inspect `routes/web.php` for login, callback, and logout routes that hit SSO controllers (for example an `Auth\OAuthController`).
   - Inspect `app/Services/Auth` (e.g. `TechysavvyOAuthProvider`) for low-level calls to the SSO server.
   - Inspect middleware that enforces authentication or tenant context on routes.

2. **Understand the external server contract**
   - Identify which endpoints on the SSO server this app calls:
     - Authorization endpoint (for browser redirect).
     - Token endpoint (for exchanging codes).
     - User info / profile endpoints.
     - Any tenant/organization context endpoints.
   - Note the **expected request/response shapes**:
     - Parameters sent during redirect (client_id, redirect_uri, scopes, state, etc.).
     - Fields returned in token responses (access token, refresh token, expiry, token type).
     - Fields returned in user info responses (id, email, name, roles, tenants, etc.).

3. **Respect security & Laravel conventions**
   - Use Laravel’s HTTP clients, guards, and Socialite/OAuth abstractions already in the codebase rather than rolling custom crypto.
   - Keep credentials, client IDs, secrets, and SSO URLs in `.env` and read them via `config()` only.
   - Validate and verify `state`/CSRF values on callbacks.
   - Do not log secrets or full tokens.

4. **Plan for multi-tenant usage**
   - Treat tenant information from the SSO server as **first-class context** in this app.
   - Carry tenant identifiers (e.g. `tenant_id`, `tenant_slug`) alongside the authenticated user.
   - Avoid breaking existing consumers of user data when adding tenant awareness; prefer adding optional fields over changing existing ones.

## Typical Workflows

### 1. Wiring Login Redirect and Callback

1. **Login redirect**
   - Add or update a route (usually in `routes/web.php`) that:
     - Redirects the browser to the SSO server’s authorization endpoint.
     - Includes required parameters (client_id, redirect_uri, state, scopes).
     - Persists any `state`/nonce in the session for later validation.
2. **Callback handling**
   - Implement or update a controller (e.g. `Auth\OAuthController`) to:
     - Validate the incoming `state` and required query parameters.
     - Exchange the authorization code for tokens using the configured SSO client/service.
     - Store tokens in a secure and consistent place (session, DB, or token storage pattern already used in this app).
     - Resolve or create the local user record that corresponds to the remote identity.
     - Log the user into this Laravel app using the appropriate guard.
     - Redirect to a sensible post-login route (often the Inertia dashboard).

### 2. Token Storage and Usage

1. **Centralize token handling**
   - Use a dedicated service (e.g. `TechysavvyOAuthProvider`) or helper to:
     - Store access/refresh tokens.
     - Refresh tokens when they expire.
     - Attach tokens to outbound HTTP requests to the SSO server.
   - Avoid duplicating token logic directly in controllers.
2. **Secure persistence**
   - Store only what is needed (access token, refresh token, expiry, scopes).
   - Keep token storage in one consistent place (session vs database) and follow existing patterns in this repo.
3. **Downstream API calls**
   - When calling the SSO server for `/me`, tenants, or permissions:
     - Use Laravel’s HTTP client or a dedicated API client service.
     - Handle token expiration by refreshing or redirecting to login when needed.

### 3. Surfacing User and Tenant Data to Inertia/React

1. **Share auth data via Inertia**
   - Use Inertia’s shared props (typically in a service provider or middleware) to expose:
     - `auth.user`: the authenticated user’s identity from this app, enriched with SSO details where appropriate.
     - `auth.tenants` or similar: tenant list/metadata if provided by the SSO server.
     - `auth.currentTenant`: the currently selected tenant context.
2. **Update layouts and pages**
   - Update `AppLayout.tsx` and relevant pages (e.g. `Dashboard.tsx`) to:
     - Consume these shared props.
     - Show user identity and tenant selection UI consistent with the rest of the app.
3. **Keep client-side logic thin**
   - Perform most auth and tenant resolution server-side, then send the resulting data to the React/Inertia layer.
   - Avoid duplicating token or tenant logic in multiple React components; use hooks or shared utilities if needed.

## Design Guidelines

- **Follow existing app patterns**
  - Mirror how other auth-related flows are implemented in this repo.
  - Use controllers + services + middleware instead of putting logic directly in routes.
- **Keep the contract with the SSO server stable**
  - When the SSO server adds fields, consume them in a backward-compatible way.
  - Avoid changing the meaning of existing fields unless the SSO server contract changes.
- **Separation of concerns**
  - Controllers: orchestrate login, callback, logout, and redirection.
  - Services: encapsulate OAuth/HTTP details and token lifecycle.
  - Middleware: ensure requests have a valid authenticated user (and tenant) before hitting business logic.

## When Unsure

- Inspect the SSO auth server’s skill and documentation to understand its expectations, then adapt this client accordingly.
- Prefer small, well-scoped changes around:
  - Redirect and callback handling.
  - Token storage and refresh.
  - How user and tenant data are exposed to Inertia/React.
- If behavior differs between environments, first check `.env` and `config/services.php` for SSO-related settings before changing code.

