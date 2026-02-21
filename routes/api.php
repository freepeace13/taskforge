<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\CurrentUserController;
use App\Http\Controllers\Api\V1\Organization\InviteController;
use App\Http\Controllers\Api\V1\Organization\MemberController;
use App\Http\Controllers\Api\V1\Organization\OrganizationController;
use App\Http\Controllers\Api\V1\Project\ArchiveController;
use App\Http\Controllers\Api\V1\Project\ProjectController;
use App\Http\Controllers\Api\V1\Task\AssigneeController;
use App\Http\Controllers\Api\V1\Comment\CommentController;
use App\Http\Controllers\Api\V1\Task\StateController;
use App\Http\Controllers\Api\V1\Task\TaskController;
use App\Http\Controllers\V1\ActivityLogController;
use App\Http\Middleware\ResolveOrganization;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'v1',
    'middleware' => []
], function () {
    Route::post('register', RegisterController::class);
    Route::post('login', LoginController::class);

    Route::group([
        'middleware' => 'auth:sanctum',
    ], function () {
        Route::get('me', CurrentUserController::class);

        Route::get('orgs', [OrganizationController::class, 'index']);
        Route::post('orgs', [OrganizationController::class, 'store']);
        Route::get('orgs/{org}', [OrganizationController::class, 'show']);
        Route::patch('orgs/{org}', [OrganizationController::class, 'update']);
        Route::delete('orgs/{org}', [OrganizationController::class, 'destroy']);

        Route::group([
            'prefix' => 'orgs/{org}',
            'middleware' => [ResolveOrganization::class]
        ], function () {
            Route::get('activity', [ActivityLogController::class, 'index']);
            Route::get('projects/{project}/activity', [ActivityLogController::class, 'projects']);
            Route::get('tasks/{task}/activity', [ActivityLogController::class, 'tasks']);

            Route::get('members', [MemberController::class, 'index']);
            Route::post('members', [MemberController::class, 'store']);
            Route::patch('members/{user}', [MemberController::class, 'update']);
            Route::delete('members/{user}', [MemberController::class, 'destroy']);

            Route::get('invites', [InviteController::class, 'index']);
            Route::post('invites', [InviteController::class, 'store']);
            Route::post('invites/{invite}/accept', [InviteController::class, 'accept']);
            Route::delete('invites/{invite}', [InviteController::class, 'destroy']);

            Route::get('projects', [ProjectController::class, 'index']);
            Route::post('projects', [ProjectController::class, 'store']);
            Route::get('projects/{project}', [ProjectController::class, 'show']);
            Route::patch('projects/{project}', [ProjectController::class, 'update']);
            Route::delete('projects/{project}', [ProjectController::class, 'destroy']);

            Route::get('projects/{project}/archived', [ArchiveController::class, 'index']);
            Route::post('projects/{project}/archive', [ArchiveController::class, 'archive']);
            Route::post('projects/{project}/restore', [ArchiveController::class, 'restore']);

            Route::group([
                'prefix' => 'projects/{project}'
            ], function () {
                Route::get('tasks', [TaskController::class, 'index']);
                Route::post('tasks', [TaskController::class, 'store']);
                Route::get('tasks/{task}', [TaskController::class, 'show']);
                Route::patch('tasks/{task}', [TaskController::class, 'update']);
                Route::delete('tasks/{task}', [TaskController::class, 'destroy']);

                Route::group([
                    'prefix' => 'tasks/{task}'
                ], function () {
                    Route::post('complete', [StateController::class, 'complete']);
                    Route::post('reopen', [StateController::class, 'reopen']);
                    Route::post('assign', [AssigneeController::class, 'assign']);
                    Route::post('unassign', [AssigneeController::class, 'unassign']);

                    Route::get('comments', [CommentController::class, 'index']);
                    Route::post('comments', [CommentController::class, 'store']);
                    Route::patch('comments/{comment}', [CommentController::class, 'update']);
                    Route::delete('comments/{comment}', [CommentController::class, 'destroy']);
                });
            });
        });
    });
});
