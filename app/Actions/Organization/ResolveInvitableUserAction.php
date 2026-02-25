<?php

namespace App\Actions\Organization;

use App\Models\User;
use Illuminate\Support\Str;

class ResolveInvitableUserAction
{
    public function resolve(string $email): User
    {
        $user = User::query()
            ->where('email', $email)
            ->first();

        if (! is_null($user)) {
            return $user;
        }

        $localPart = Str::before($email, '@');
        $normalizedName = Str::of($localPart)
            ->replace(['.', '_', '-'], ' ')
            ->title()
            ->trim()
            ->value();

        $name = $normalizedName !== '' ? $normalizedName : 'Invited User';

        return User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => null,
        ]);
    }
}
