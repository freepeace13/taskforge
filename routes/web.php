<?php

use App\Http\Controllers\User\AcceptInvitationController;
use Illuminate\Support\Facades\Route;

Route::get('invitations/{token}/accept', AcceptInvitationController::class)
    ->middleware(['signed'])
    ->name('invitations.accept');
