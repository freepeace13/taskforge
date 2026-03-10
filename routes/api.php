<?php

use App\Http\Controllers\Api\V1\ActivityLogController;
use App\Http\Controllers\Api\V1\Comment\CommentController;
use App\Http\Controllers\Api\V1\CurrentUserController;
use App\Http\Controllers\Api\V1\Organization\InvitationController;
use App\Http\Controllers\Api\V1\Organization\MemberController;
use App\Http\Controllers\Api\V1\Organization\OrganizationController;
use App\Http\Controllers\Api\V1\Project\ArchiveController;
use App\Http\Controllers\Api\V1\Project\ProjectController;
use App\Http\Controllers\Api\V1\Task\AssigneeController;
use App\Http\Controllers\Api\V1\Task\StateController;
use App\Http\Controllers\Api\V1\Task\TaskController;
use App\Http\Middleware\TenancyMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware(['techysavvy', TenancyMiddleware::class])->group(function () {
        Route::get('me', CurrentUserController::class)->name('api.v1.me');

        Route::get('orgs', [OrganizationController::class, 'index'])->name('api.v1.orgs.index');
        Route::post('orgs', [OrganizationController::class, 'store'])->name('api.v1.orgs.store');
        Route::patch('orgs/{org:slug}', [OrganizationController::class, 'update'])->name('api.v1.orgs.update');

        Route::get('', [OrganizationController::class, 'show'])->name('api.v1.orgs.show');
        Route::delete('', [OrganizationController::class, 'destroy'])->name('api.v1.orgs.destroy');
        Route::get('activity', [ActivityLogController::class, 'index'])->name('api.v1.orgs.activity.index');

        Route::get('members', [MemberController::class, 'index'])->name('api.v1.orgs.members.index');
        // Route::post('members', [MemberController::class, 'store']);
        Route::patch('members/{user}', [MemberController::class, 'update'])->name('api.v1.orgs.members.update');
        Route::delete('members/{user}', [MemberController::class, 'destroy'])->name('api.v1.orgs.members.destroy');

        Route::get('invitations', [InvitationController::class, 'index'])->name('api.v1.orgs.invitations.index');
        Route::post('invitations', [InvitationController::class, 'store'])->name('api.v1.orgs.invitations.store');
        Route::delete('invitations/{invite}', [InvitationController::class, 'destroy'])->name('api.v1.orgs.invitations.destroy');

        Route::get('projects', [ProjectController::class, 'index'])->name('api.v1.orgs.projects.index');
        Route::post('projects', [ProjectController::class, 'store'])->name('api.v1.orgs.projects.store');
        Route::get('projects/{project}', [ProjectController::class, 'show'])->name('api.v1.orgs.projects.show');
        Route::patch('projects/{project}', [ProjectController::class, 'update'])->name('api.v1.orgs.projects.update');
        Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('api.v1.orgs.projects.destroy');

        Route::get('projects/{project}/archived', [ArchiveController::class, 'index'])->name('api.v1.orgs.projects.archived.index');
        Route::post('projects/{project}/archive', [ArchiveController::class, 'archive'])->name('api.v1.orgs.projects.archive');
        Route::post('projects/{project}/restore', [ArchiveController::class, 'restore'])->name('api.v1.orgs.projects.restore');
        Route::get('projects/{project}/activity', [ActivityLogController::class, 'projects'])->name('api.v1.orgs.projects.activity.index');

        Route::group([
            'prefix' => 'projects/{project}',
        ], function () {
            Route::get('tasks', [TaskController::class, 'index'])->name('api.v1.orgs.projects.tasks.index');
            Route::post('tasks', [TaskController::class, 'store'])->name('api.v1.orgs.projects.tasks.store');
            Route::get('tasks/{task}', [TaskController::class, 'show'])->name('api.v1.orgs.projects.tasks.show');
            Route::patch('tasks/{task}', [TaskController::class, 'update'])->name('api.v1.orgs.projects.tasks.update');
            Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('api.v1.orgs.projects.tasks.destroy');
            Route::get('tasks/{task}/activity', [ActivityLogController::class, 'tasks'])->name('api.v1.orgs.projects.tasks.activity.index');

            Route::group([
                'prefix' => 'tasks/{task}',
            ], function () {
                Route::post('complete', [StateController::class, 'complete'])->name('api.v1.orgs.projects.tasks.complete');
                Route::post('reopen', [StateController::class, 'reopen'])->name('api.v1.orgs.projects.tasks.reopen');
                Route::post('assign', [AssigneeController::class, 'assign'])->name('api.v1.orgs.projects.tasks.assign');
                Route::post('unassign', [AssigneeController::class, 'unassign'])->name('api.v1.orgs.projects.tasks.unassign');

                Route::get('comments', [CommentController::class, 'index'])->name('api.v1.orgs.projects.tasks.comments.index');
                Route::post('comments', [CommentController::class, 'store'])->name('api.v1.orgs.projects.tasks.comments.store');
                Route::patch('comments/{comment}', [CommentController::class, 'update'])->name('api.v1.orgs.projects.tasks.comments.update');
                Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('api.v1.orgs.projects.tasks.comments.destroy');
            });
        });
    });
});
