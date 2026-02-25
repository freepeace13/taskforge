---
name: transactional-action-pattern
description: A disciplined architecture pattern that isolates state mutations into testable, transaction-safe Action classes with SOLID compliance and strict separation of concerns.
---

# Transactional Action Pattern (TAP)

## Overview

The Transactional Action Pattern (TAP) enforces that all database mutations and complex business operations are encapsulated within dedicated Action classes.

This ensures:

* Clear separation of concerns
* SOLID compliance
* Testability
* Reusability
* Transaction safety
* Framework-independent business logic

---

# Core Principles

## 1. Database Mutations Must Use Actions

Any operation that:

* Modifies the database
* Affects multiple models
* Requires transactions
* Triggers side effects (events, notifications, external APIs)
* Contains non-trivial business logic

Must be implemented inside a dedicated Action class.

---

## 2. Separation of Responsibilities

### Action Class Responsibilities

An Action class:

* Executes business logic
* Handles database transactions
* Coordinates repositories or services
* Throws domain exceptions when necessary
* Remains framework-light

### Action Class Must NOT

* Validate input
* Perform authorization
* Read directly from HTTP Request
* Call `auth()` or `Gate`
* Contain HTTP concerns

---

# Architecture Structure

```
app/
 └── Actions/
      └── Projects/
           └── CreateProjectAction.php
```

---

# Example Implementation

## Data Transfer Object (DTO)

```php
namespace App\Data;

class CreateProjectData
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly int $organizationId,
    ) {}
}
```

---

## Action Class

```php
namespace App\Actions\Projects;

use App\Data\CreateProjectData;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class CreateProjectAction
{
    public function execute(CreateProjectData $data): Project
    {
        return DB::transaction(function () use ($data) {
            return Project::create([
                'name' => $data->name,
                'description' => $data->description,
                'organization_id' => $data->organizationId,
            ]);
        });
    }
}
```

---

# SOLID Compliance

## Single Responsibility Principle

One Action = One business operation.

---

## Open/Closed Principle

Behavior can be extended using:

* Events
* Decorators
* Additional injected services

---

## Liskov Substitution Principle

Actions can implement interfaces:

```php
interface CreateProjectActionInterface
{
    public function execute(CreateProjectData $data): Project;
}
```

---

## Interface Segregation Principle

Each Action is focused and does not expose unnecessary methods.

---

## Dependency Inversion Principle

External systems are injected:

```php
class CreateProjectAction
{
    public function __construct(
        private ExternalSyncService $syncService
    ) {}

    public function execute(CreateProjectData $data): Project
    {
        return DB::transaction(function () use ($data) {
            $project = Project::create([...]);

            $this->syncService->syncProject($project);

            return $project;
        });
    }
}
```

---

# Validation and Authorization

Validation and authorization are performed outside the Action.

## Form Request

```php
class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Project::class);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
```

---

## Controller

```php
class ProjectController
{
    public function store(
        StoreProjectRequest $request,
        CreateProjectAction $action
    ) {
        $data = new CreateProjectData(
            name: $request->name,
            description: $request->description,
            organizationId: $request->user()->organization_id
        );

        $project = $action->execute($data);

        return response()->json($project);
    }
}
```

---

# Unit Testing Requirements

Each Action must include:

* Success test
* Failure test
* Transaction rollback test (if applicable)

---

## Success Test Example

```php
it('creates a project successfully', function () {
    $action = app(CreateProjectAction::class);

    $data = new CreateProjectData(
        name: 'Test Project',
        description: 'Description',
        organizationId: 1
    );

    $project = $action->execute($data);

    expect($project)->toBeInstanceOf(Project::class);

    $this->assertDatabaseHas('projects', [
        'name' => 'Test Project',
    ]);
});
```

---

## Failure Test Example

```php
it('fails when business rule is violated', function () {
    $action = app(CreateProjectAction::class);

    $data = new CreateProjectData(
        name: '',
        description: 'Description',
        organizationId: 1
    );

    $this->expectException(Exception::class);

    $action->execute($data);
});
```

---

## Rollback Test Example

```php
it('rolls back transaction if external service fails', function () {

    $syncService = Mockery::mock(ExternalSyncService::class);
    $syncService->shouldReceive('syncProject')
        ->andThrow(new Exception('Sync failed'));

    $action = new CreateProjectAction($syncService);

    $data = new CreateProjectData(
        name: 'Rollback Test',
        description: 'Description',
        organizationId: 1
    );

    try {
        $action->execute($data);
    } catch (Exception $e) {}

    $this->assertDatabaseMissing('projects', [
        'name' => 'Rollback Test'
    ]);
});
```

---

# Internal Checklist

Before merging an Action:

* [ ] Does it mutate state?
* [ ] Is validation outside the Action?
* [ ] Is authorization outside the Action?
* [ ] Are dependencies injected?
* [ ] Is it transaction-safe?
* [ ] Is it unit-tested (success + failure)?
* [ ] Can it be reused outside HTTP?
