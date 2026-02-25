<?php

use App\Http\Controllers\User\InvitationController;
use Illuminate\Support\Facades\Route;

Route::get('invitations/{token}/accept', [InvitationController::class, 'accept'])
    ->middleware(['guest', 'signed'])
    ->name('invitations.accept');
