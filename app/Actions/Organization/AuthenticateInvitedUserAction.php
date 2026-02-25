<?php

namespace App\Actions\Organization;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateInvitedUserAction
{
    public function authenticate(Request $request, User $user): void
    {
        Auth::guard('web')->login($user);
        $request->session()->regenerate();
    }
}
