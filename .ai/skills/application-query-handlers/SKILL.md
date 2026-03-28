---
name: application-query-handlers
description: Isolates read-side database work in App\Queries using a Query DTO plus QueryHandler, with an App\Contracts\Queries inbound port. Use when listing, loading, filtering, paginating, or any controller/API code that reads from the database without mutating domain state.
---

# Application query handlers (read model)

## When to use

- **Use this pattern** for any **read** path: lists, single-record fetches backed by queries, search, filters, pagination, reports that only select data.
- **Do not** put Eloquent/query builder logic directly in controllers for these cases—extract it into `app/Queries/`.
- **Pair with** `application-write-actions` for creates/updates/deletes (mutations live in `app/Actions/`).

## Structure

| Piece | Location | Role |
|-------|-----------|------|
| Query (DTO) | `app/Queries/{Domain}/{Verb}{Entity}Query.php` | Input: IDs, filters, pagination flags, search strings. Optional helpers like `shouldPaginate()`. |
| Handler | `app/Queries/{Domain}/{Verb}{Entity}QueryHandler.php` | Implements the contract; executes the query (Eloquent/builder). |
| Contract (inbound port) | `app/Contracts/Queries/{Domain}/{Verb}s{Entity}` (e.g. `ListsOrganizations`) | Inbound port: `handle(ListOrganizationsQuery $query)` with an explicit return type. |
| Binding | `AppServiceProvider::register()` | Bind contract → concrete handler. |

**Naming:** Mirror existing queries such as `ListOrganizationsQuery` + `ListOrganizationsQueryHandler`. Contract names should read as capabilities (e.g. `ListsOrganizations` with `handle(ListOrganizationsQuery $query)`), avoiding the same class name as the DTO.

## Controller usage

- Type-hint the **contract**, not the concrete handler.
- Build the query DTO from the request (and tenant context), then delegate:

```php
public function index(Request $request, ListsOrganizations $queryPort)
{
    $result = $queryPort->handle(new ListOrganizationsQuery(
        userId: $request->user()->id,
        search: $request->string('q', null),
    ));

    return OrganizationResource::collection($result);
}
```

## Rules

- Handlers return whatever the presentation layer needs (e.g. paginator, `Collection`, single model)—keep return types explicit where practical.
- **No** writes or side effects that change domain state (no `create`/`update`/`delete` here—use Actions).
- Reuse tenancy and authorization patterns from the `tenancy` skill; pass IDs into the query DTO when the handler must scope by org/project/user.

## Testing

- **Every query handler class must have a dedicated PHPUnit unit test** under `tests/Unit/` (e.g. `tests/Unit/Queries/ListTasksQueryHandlerTest.php` for `ListTasksQueryHandler`). The test must cover scoping, filters, pagination, and return shapes relevant to that handler—using factories and the database as needed for read paths.
- **When you add or change a query handler** (or its query DTO in ways that affect behavior), update its unit test in the **same change**. Run the affected test file via Sail, e.g. `vendor/bin/sail artisan test --compact tests/Unit/Queries/ListTasksQueryHandlerTest.php`.

## Legacy

Some handlers may exist without a `Contracts\Queries\*` interface yet. When adding or refactoring a read path, **add the contract and binding** so controllers always depend on the port.

## See also

- `application-write-actions` — mutations via `app/Actions/` and `app/Contracts/Actions/`.
