<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Middleware\ResolveOrganization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'v1',
    'middleware' => []
], function () {
    Route::post('register', '');
    Route::post('login', LoginController::class);

    Route::group([
        'middleware' => 'auth:sanctum'
    ], function () {
        Route::post('logout', '');
        Route::get('me', '');

        Route::get('orgs', '');
        Route::post('orgs', '');
        Route::get('orgs/{org}', '');
        Route::patch('orgs/{org}', '');
        Route::delete('orgs/{org}', '');

        Route::group([
            'prefix' => 'orgs/{org}',
            'middleware' => [ResolveOrganization::class]
        ], function () {
            Route::get('activity', '');
            Route::get('projects/{project}/activity', '');
            Route::get('tasks/{task}/activity', '');

            Route::get('members', '');
            Route::post('members', '');
            Route::patch('members/{user}', '');
            Route::delete('members/{user}', '');

            Route::get('invites', '');
            Route::post('invites', '');
            Route::post('invites/{invite}/accept', '');
            Route::delete('invites/{invite}', '');

            Route::get('projects', '');
            Route::post('projects', '');

            Route::get('projects/{project}', '');
            Route::patch('projects/{project}', '');
            Route::delete('projects/{project}', '');
            Route::post('projects/{project}/archive', '');
            Route::post('projects/{project}/restore', '');

            Route::group([
                'prefiox' => 'projects/{project}'
            ], function () {
                Route::get('tasks', '');
                Route::post('tasks', '');
                Route::get('tasks/{task}', '');
                Route::patch('tasks/{task}', '');
                Route::delete('tasks/{task}', '');

                Route::post('tasks/{task}/complete', '');
                Route::post('tasks/{task}/reopen', '');
                Route::post('tasks/{task}/assign', '');
                Route::post('tasks/{task}/unassign', '');

                Route::get('tasks/{task}/comments', '');
                Route::post('tasks/{task}/comments', '');
                Route::patch('tasks/{task}/comments/{comment}', '');
                Route::delete('tasks/{task}/comments/{comment}', '');
            });
        });
    });
});
