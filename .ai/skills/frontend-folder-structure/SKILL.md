---
name: frontend-folder-structure
description: Defines Taskforge frontend folder structure rules for `resources/js`, including page container ownership, feature module boundaries, lowercase directory naming, and props-vs-context decisions. Use when organizing, moving, or reviewing frontend files and imports.
---

# Frontend Folder Structure (Taskforge)

## Purpose

Use this skill to keep frontend architecture consistent while working in `resources/js`.

## Canonical Top-Level Structure

Use lowercase directory names at the top level:

- `resources/js/app.tsx`
- `resources/js/pages/*`
- `resources/js/features/*`
- `resources/js/components/*` (legacy/shared not yet migrated)
- `resources/js/test/*`

Do not introduce top-level `Pages`, `Layouts`, or `Components` directories.

## Ownership Boundaries

### `resources/js/pages/*`

- Route entry components for Inertia.
- Page-level state source of truth.
- Page-level orchestration and composition.
- Top-most route-facing framework layer (Inertia props, page-specific framework hooks, page orchestration).
- Must not be passive re-export-only files.

### `resources/js/features/*`

- Reusable feature UI, hooks, types, helpers, and feature-local state abstractions.
- Organize by domain (for example `layout`, `dashboard`, `projects`, `tasks`, `shared`).
- Keep boundaries clear; avoid cross-feature leakage unless explicitly shared.
- Must stay framework-agnostic: no Inertia router usage, no Ziggy route access, and no page-framework coupling inside feature modules.
- Feature modules may own feature-local UI state/actions and expose framework-agnostic extension points via typed props/callbacks/context contracts.

### `resources/js/app.tsx`

- Inertia bootstrap and resolver composition root.
- Allowed place for framework wiring used by many pages (for example default layout setup and injected framework callbacks/values).
- May pass framework-derived props/actions into feature modules; do not move framework imports into feature code.

### `resources/js/components/*`

- Transitional area for components not yet migrated into `features/shared`.
- Do not place new feature-specific logic here.

## Import Conventions

- Prefer feature-root imports and barrels:
  - `@/features/shared/ui`
  - `@/features/layout/components/AppHeader`
- Avoid deep implementation-path imports when a public barrel exists.
- Keep page imports readable and explicit about ownership.

## Props vs Context Rule

- Default to props for straightforward parent-child communication.
- Switch to context when prop passing becomes inefficient:
  - deep prop drilling
  - wide fan-out of shared state/actions
  - tightly coupled state across sibling branches
- Context must be defined in the owning feature:
  - good: `resources/js/features/<feature>/context/*`
  - avoid: page-scoped ad hoc contexts for reusable feature state
- Pages/app layer provide framework-derived state/actions into feature-defined providers; features define the context contract and consume it.

## Good vs Avoid Examples

- Good:
  - `resources/js/features/layout/AppLayout.tsx` owns sidebar/modal/theme UI state and accepts `onLogoutRequest` as a prop.
  - `resources/js/app.tsx` imports Inertia/Ziggy and injects `navItems`, user props, and logout callback into `AppLayout`.
  - `resources/js/features/layout/context/LayoutContext.tsx` defines the feature context contract consumed by layout components.
- Avoid:
  - Importing `router` from `@inertiajs/react` directly inside `resources/js/features/*`.
  - Calling `route(...)` from `ziggy-js` inside feature components/hooks.
  - Defining reusable feature context under `resources/js/pages/*`.

## Good vs Avoid Examples

- Good:
  - `resources/js/features/layout/AppLayout.tsx` owns sidebar/modal/theme UI state and accepts `onLogoutRequest` as a prop.
  - `resources/js/app.tsx` imports Inertia/Ziggy and injects `navItems`, user props, and logout callback into `AppLayout`.
  - `resources/js/features/layout/context/LayoutContext.tsx` defines the feature context contract consumed by layout components.
- Avoid:
  - Importing `router` from `@inertiajs/react` directly inside `resources/js/features/*`.
  - Calling `route(...)` from `ziggy-js` inside feature components/hooks.
  - Defining reusable feature context under `resources/js/pages/*`.

## Inertia Resolver Constraint

`resources/js/app.tsx` resolves pages from `./pages/**/*.tsx`.

Therefore routed page entry files must remain under `resources/js/pages/*`.

## Migration Checklist

When moving files or reviewing architecture:

1. Confirm top-level lowercase paths.
2. Confirm routed components stay in `pages/*`.
3. Confirm page state/orchestration remains in page containers.
4. Confirm framework wiring is kept in `pages/*` or `app.tsx`, then injected into features as props/actions.
5. Confirm reusable logic is moved into `features/*`.
6. Confirm context placement is feature-scoped (if used).
7. Update imports and barrels.
8. Run frontend tests/build after structural edits.

