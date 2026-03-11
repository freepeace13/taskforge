---
name: react-components-guidelines
description: Provides conventions and best practices for React components under resources/js/Components, including folder structure, barrel files, imports, props, state management, styling, and testing. Use when creating, modifying, or reviewing React components in this project.
---

# React Components Guidelines

## When to Use This Skill

Use these rules whenever:

- You create or rename React components under `resources/js/Components`.
- You add or update imports that reference components in `resources/js/Components`.
- You add tests, hooks, or utilities that are specific to a single component.

## Folder & Import Conventions

- **One folder per component**: Each React component under `resources/js/Components` must live in a folder that matches the component name. For example:
  - `resources/js/Components/Button/Button.tsx`
  - `resources/js/Components/Card/Card.tsx`
  - `resources/js/Components/StatusBadge/StatusBadge.tsx`

- **Barrel entry file**: Each component folder must expose a single public entry file (`index.ts` or `index.tsx`) that re-exports the default component and any public types. For example:

```ts
// resources/js/Components/Button/index.ts
export { default } from './Button';
export type { ButtonProps } from './Button';
```

- **Import from the folder root**: Always import components from the folder root, not from the implementation file. For example:
  - ✅ `import Button from '@/Components/Button';`
  - ❌ `import Button from '@/Components/Button/Button';`

- **Colocate component-specific code**: Place tests, hooks, and utilities next to the component inside its folder. For example:
  - `resources/js/Components/Button/Button.test.tsx`
  - `resources/js/Components/Button/useButtonAnalytics.ts`
  - `resources/js/Components/Button/types.ts`

- **No new flat components**: Do not create new top-level `.tsx` files directly inside `resources/js/Components`. Always create a folder for the component and put the main component file and any related code inside that folder.

## Component Design Guidelines

- **Single responsibility**: Each component should do one thing well (e.g. `Button`, `Card`, `StatusBadge`) rather than mixing unrelated concerns.
- **Props first**: Prefer taking data and callbacks via typed props over reaching into global state or context unless there is an existing shared pattern.
- **Typed props**: Define a `Props` type or interface (for example `ButtonProps`) in the component file or a colocated `types.ts`, and export it via the barrel if it is part of the public API.
- **Avoid unnecessary state**: Derive values from props when possible; only add local state for UI-specific behavior (open/closed, active tab, etc.).
- **Composition over configuration**: Prefer composing components (children, render props, slots) over large prop bags of booleans that create many modes.

## Styling & Markup

- **Consistent Tailwind usage**: Follow existing Tailwind class patterns in sibling components for spacing, colors, typography, and responsive behavior.
- **Semantic HTML**: Use semantic elements (`button`, `a`, `ul`, `nav`, `section`, etc.) that match the component’s role.
- **Accessibility**:
  - Ensure interactive elements are keyboard accessible.
  - Use `aria-*` attributes where needed and follow patterns used in existing components.

## Testing & Reuse

- **Colocated tests**: When you add or change non-trivial behavior, create or update a colocated test file (for example `Button/Button.test.tsx`).
- **Reusable logic in hooks**: If behavior is reused across multiple components, extract it into a colocated hook (for example `useButtonAnalytics.ts`) or a shared hook following existing project patterns.
- **Public vs internal API**: Only re-export types and helpers from the barrel that are intended to be used outside the component’s folder; keep internal utilities file-local where possible.

## Implementation Notes

- When introducing a new component, always:
  - Create a folder named after the component.
  - Put the main component implementation file inside that folder.
  - Add an `index.ts` (or `index.tsx`) barrel file as the public entry point.
  - Update any imports to reference the folder root.

