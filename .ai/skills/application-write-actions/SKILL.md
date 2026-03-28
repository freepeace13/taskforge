---
name: application-write-actions
description: Isolates writes and domain mutations in App\Actions with App\Contracts\Actions inbound ports (command-style handlers). Use when creating, updating, deleting, or performing any state-changing operation from HTTP controllers, jobs, or listeners.
---

# Application write actions (commands)

## When to use

- **Use this pattern** for **mutations**: create, update, delete, invites, role changes, task completion, archiving, etc.
- **Do not** perform these operations inline in controllers—delegate to an Action class behind a contract.
- **Pair with** `application-query-handlers` for reads (lists/fetches stay in `app/Queries/`).

## Structure

| Piece | Location | Role |
|-------|-----------|------|
| Action (handler) | `app/Actions/{Domain}/{Verb}{Entity}Action.php` | Implements policy checks (e.g. via `AuthorizesActions`), persists changes, returns models or void as appropriate. |
| Contract (inbound port) | `app/Contracts/Actions/{Domain}/{Verb}s{Entity}Action` or domain-specific verb interface | Declares the public method(s) the application calls (`create`, `update`, `delete`, …). |
| Binding | `AppServiceProvider::register()` | `$this->app->bind(Contract::class, ConcreteAction::class);` |

**Naming:** Follow existing code: interface `CreatesTaskAction` with method `create(...)`, class `CreateTaskAction` implements it. Import aliases in the provider (e.g. `CreatesTaskAction as CreatesTaskContract`) avoid name clashes.

## Controller usage

- Type-hint the **contract** in the method signature.
- Map validated requests to Data objects (or primitives) and call the action:

```php
public function store(StoreOrganizationRequest $request, CreatesOrganizationAction $action)
{
    $organization = $action->create(
        actor: $request->user(),
        data: new OrganizationData(name: $request->name),
    );

    return new OrganizationResource($organization);
}
```

## Rules

- One primary responsibility per action class; name by the business operation.
- Authorization belongs in the action (or invoked policies) using project conventions (e.g. `AuthorizesActions`).
- Form Requests validate HTTP input; Actions assume valid invariants for the operation or enforce domain rules.

## Testing

- **Each concrete action class** (`app/Actions/.../*Action.php`) **must have its own PHPUnit unit test class** (`tests/Unit/.../*ActionTest.php`), exercising the action’s behavior: authorization, persistence, return values, and failure paths.
- **When you add or change an action**, update that action’s unit test in the **same change**. Run the affected file via Sail, e.g. `vendor/bin/sail artisan test --compact tests/Unit/.../YourActionTest.php`.

## See also

- `application-query-handlers` — read-only database access via `app/Queries/` and `app/Contracts/Queries/`.
- `tenancy` — tenant scope for org/project-scoped mutations.
