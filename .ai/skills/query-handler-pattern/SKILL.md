---
name: query-handler-pattern
description: Structured read-side pattern using Query Objects and Query Handlers for complex data retrieval with optional pagination support.
---

# Query Handler Pattern

A structured read-side pattern for handling complex data retrieval using **Query Objects** and **Query Handlers**.

This pattern is intended for scenarios where data fetching becomes complex — such as multiple filters, sorting, searching, joins, aggregates, or conditional pagination.

It is conceptually similar to the `transactional-action-pattern`, but focused on **reading data instead of modifying it**.

---

## 🎯 When To Use

Use this pattern when:

- A query has multiple filters (status, date range, search, etc.)
- Sorting and dynamic ordering are required
- Conditional joins or relationships are involved
- Pagination needs to be toggleable
- You want to avoid fat controllers
- You want clear separation of read logic

Avoid using this pattern for simple `Model::all()` type reads.

---

## 🧠 Core Concepts

### 1️⃣ Query Object

A simple DTO-like class that:

- Holds filters and parameters
- Defines whether results should be paginated
- Does NOT execute database logic

Example responsibilities:
- Search term
- Filters (status, date range, etc.)
- Sorting
- Pagination toggle

---

### 2️⃣ Query Handler

Responsible for:

- Building the query
- Applying filters
- Applying sorting
- Returning:
  - Collection / array (default)
  - Paginated result (if query->paginated is true)

The handler performs execution logic, not validation or authorization.

---

## 📂 Folder Structure (Option C)

All queries are stored under:

```

app/
└── Queries/
    └── Tasks/
        └── ListTasksQuery.php
        └── ListTasksHandler.php

Group by domain/module inside `app/Queries`.

---

## 🏗 Example Structure

### ListTasksQuery.php

```php
class ListTasksQuery
{
    public function __construct(
        public ?string $search = null,
        public ?string $status = null,
        public ?string $sortBy = 'created_at',
        public string $direction = 'desc',
        public bool $paginated = false,
        public int $perPage = 15,
    ) {}
}
````

---

### ListTasksHandler.php

```php
class ListTasksHandler
{
    public function handle(ListTasksQuery $query)
    {
        $builder = Task::query();

        if ($query->search) {
            $builder->where('title', 'like', "%{$query->search}%");
        }

        if ($query->status) {
            $builder->where('status', $query->status);
        }

        $builder->orderBy($query->sortBy, $query->direction);

        if ($query->paginated) {
            return $builder->paginate($query->perPage);
        }

        return $builder->get();
    }
}
```

---

## 🧩 Design Rules

* Queries are **read-only**
* No validation logic inside query classes
* No authorization inside handlers
* Controllers resolve query objects and pass them to handlers
* Handlers may use repositories if needed
* Query objects are lightweight and immutable-friendly

---

## 🔬 Testing Strategy

Each handler must have:

* ✅ Success tests (filters work, sorting works)
* ✅ Pagination tests (returns paginator when enabled)
* ❌ Failing edge-case tests (invalid sort, empty filters, etc.)

---

## 🚀 Benefits

* Clear separation of read vs write concerns
* Predictable structure
* Scalable filtering logic
* Easier unit testing
* Cleaner controllers
* Symmetry with Action Pattern

---

## 🆚 Comparison with Action Pattern

| Pattern                      | Purpose                                         |
| ---------------------------- | ----------------------------------------------- |
| Transactional Action Pattern | Handles write operations (create/update/delete) |
| Query Handler Pattern        | Handles complex read operations                 |

---
