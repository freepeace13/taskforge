---
name: frontend-feature-composition
description: >-
  Guides Taskforge React front-end work: reuse shared feature UI before building new UI, split bloated pages into small components, domain-style features with context or containers and pages injecting state, hooks for state, TypeScript placement, recommended React patterns, and tests for forms and interactive components. Use when building or refactoring pages, features, hooks, or tests in resources/js.
---

# Frontend Feature Composition (Taskforge)

## When to Use

Apply alongside [frontend-folder-structure](../frontend-folder-structure/SKILL.md) and [react-components-guidelines](../react-components-guidelines/SKILL.md) whenever you add or change UI under `resources/js/pages`, `resources/js/features`, or shared components.

## 1. Reuse Before You Build

- **Search the shared feature first** for buttons, inputs, layout primitives, and patterns (`resources/js/features/shared/**`, including UI barrels such as `@/features/shared/ui`).
- **Prefer public barrels** over deep imports when a component already exists.
- Only add new shared primitives when multiple features need the same behavior or visual contract; otherwise keep building blocks inside the owning feature folder.

## 2. Keep Pages Thin; Compose Small Components

- **Avoid bloated pages:** long, nested JSX and huge style blocks belong in smaller components. **Split by responsibility** (headers, form sections, lists, empty states, dialogs), not by arbitrary line count.
- **Where to place extracted pieces:**
  - **Reusable across all features (shared UI):** `resources/js/features/shared/**` — for example primitives and patterns under `features/shared/ui` and related barrels (`@/features/shared/ui`). This is the default home for new cross-feature UI.
  - **Reusable only within one domain/feature:** `resources/js/features/<domain>/components/**` (or the feature’s established subfolders), colocated with that feature’s hooks and types.
  - **`resources/js/components/**`:** use for **app-wide, framework-oriented** pieces that are not domain UI — for example error boundaries, suspense fallbacks, or similar wiring helpers. Prefer **shared UI** for reusable visual/domain building blocks; keep `components/` for cross-cutting React/framework infrastructure and legacy pieces not yet migrated into `features/shared`.

## 3. Feature as Domain; Pages Own Framework Wiring

- Treat **`resources/js/features/<domain>`** as a **domain module**: it should not assume Inertia, Ziggy, or routing details.
- **Pages** (`resources/js/pages/**`) are the route layer: they read Inertia props, call `router` / `route()`, and pass **data and callbacks** into feature UI.
- When **props become wide or deeply drilled**, use a **feature-defined context** or a **container component** whose contract is props/callbacks—still **provided or wrapped from the page** with framework-free values. See props-vs-context rules in [frontend-folder-structure](../frontend-folder-structure/SKILL.md).

## 4. TypeScript: Feature vs Shared

- **Shared contracts** (props for primitives, shared DTO shapes): `features/shared/**` or dedicated `types.ts` next to shared barrels.
- **Feature-only types**: colocate under that feature (`features/<domain>/.../types.ts` or next to the component). Do not import feature-private types from other features unless you are intentionally sharing via a public module.

## 5. Hooks

- Use **custom hooks** (`useX`) for non-trivial local state, derived state, subscriptions, and reusable behavior tied to a component or small tree.
- **Colocate** hooks with the component folder when they are not broadly reused; promote to `features/<domain>/hooks` or `features/shared` when multiple feature components need the same hook.
- Keep hooks **free of Inertia/Ziggy** when they live under `features/*`; pass framework-derived values in as arguments or from thin page-level wrappers.

## 6. React Patterns (Default Stance)

- Prefer **composition** over inheritance; favor **controlled** or clearly **single-owner** state for forms.
- Use **stable callbacks** (`useCallback`) where memoization prevents unnecessary child work; avoid premature optimization.
- Align lists and forms with existing project patterns (e.g. `useForm` from Inertia on pages, local state or feature hooks inside pure UI).
- For accessibility, match existing primitives from `features/shared` before inventing new patterns.

## 7. Testing Interactive UI

- **Add or extend tests** for **forms** (submit, validation messages, disabled states, optimistic flows) and **other interactive components** (toggles, dialogs, keyboard behavior) using the project’s frontend test setup under `resources/js/test` and colocated `*.test.tsx` next to components when that is the project convention.
- Prefer **user-centric assertions** (labels, roles) consistent with existing tests in the repo.
- After behavioral changes, run the **minimal** relevant test command (for example `vendor/bin/sail npm run test` with a file or pattern filter for Vitest).

## Quick Checklist

- [ ] Checked `features/shared` (and barrels) for existing UI.
- [ ] Page is composed of small components; no single file doing everything.
- [ ] New shared vs feature-local placement matches boundaries above.
- [ ] No `router` / `route()` / Inertia hooks inside `features/*` implementation files.
- [ ] Complex state uses hooks and/or feature context with page-injected providers.
- [ ] Types live in the right shared vs feature scope.
- [ ] Forms and interactive pieces have test coverage when behavior is non-trivial.
