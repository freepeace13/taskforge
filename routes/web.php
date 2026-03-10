<?php

use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Inertia\DashboardController;
use App\Http\Controllers\Inertia\Project\ProjectController;
use App\Http\Controllers\Inertia\Task\TaskController;
use App\Http\Controllers\User\AcceptInvitationController;
use Illuminate\Support\Facades\Route;

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
    Route::redirect('/', '/dashboard');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
});
