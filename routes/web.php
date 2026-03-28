<?php

use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Inertia\DashboardController;
use App\Http\Controllers\Inertia\Project\ProjectController;
use App\Http\Controllers\Inertia\Task\TaskController;
use App\Http\Controllers\User\AcceptInvitationController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('site.home');
Route::view('/features', 'pages.features')->name('site.features');
Route::view('/preview', 'pages.preview')->name('site.preview');
Route::view('/pricing', 'pages.pricing')->name('site.pricing');

Route::get('login', [OAuthController::class, 'redirect'])
    ->middleware(['guest'])
    ->name('login');

Route::get('auth/callback', [OAuthController::class, 'callback'])
    ->middleware(['guest'])
    ->name('auth.callback');

Route::post('logout', [OAuthController::class, 'logout'])
    ->middleware(['auth'])
    ->name('logout');

Route::get('invitations/{token}/accept', AcceptInvitationController::class)
    ->middleware(['signed'])
    ->name('invitations.accept');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::patch('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::get('/tasks', [TaskController::class, 'hub'])->name('tasks.hub');

    Route::prefix('projects/{project}')->group(function () {
        Route::get('tasks', [TaskController::class, 'index'])->name('projects.tasks.index');
        Route::get('tasks/create', [TaskController::class, 'create'])->name('projects.tasks.create');
        Route::post('tasks', [TaskController::class, 'store'])->name('projects.tasks.store');
        Route::get('tasks/{task}/edit', [TaskController::class, 'edit'])->name('projects.tasks.edit');
        Route::get('tasks/{task}', [TaskController::class, 'show'])->name('projects.tasks.show');
        Route::patch('tasks/{task}', [TaskController::class, 'update'])->name('projects.tasks.update');
        Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('projects.tasks.destroy');
    });
});
