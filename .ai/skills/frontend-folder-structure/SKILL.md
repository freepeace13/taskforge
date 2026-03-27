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
- Must not be passive re-export-only files.

### `resources/js/features/*`

- Reusable feature UI, hooks, types, helpers, and feature-local state abstractions.
- Organize by domain (for example `layout`, `dashboard`, `projects`, `tasks`, `shared`).
- Keep boundaries clear; avoid cross-feature leakage unless explicitly shared.

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

## Inertia Resolver Constraint

`resources/js/app.tsx` resolves pages from `./pages/**/*.tsx`.

Therefore routed page entry files must remain under `resources/js/pages/*`.

## Migration Checklist

When moving files or reviewing architecture:

1. Confirm top-level lowercase paths.
2. Confirm routed components stay in `pages/*`.
3. Confirm page state/orchestration remains in page containers.
4. Confirm reusable logic is moved into `features/*`.
5. Confirm context placement is feature-scoped (if used).
6. Update imports and barrels.
7. Run frontend tests/build after structural edits.

