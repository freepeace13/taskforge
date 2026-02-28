<?php

namespace App\Enums;

enum Role: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Member = 'member';

    public static function values(): array
    {
        return array_map(fn ($role) => $role->value, self::cases());
    }

    public function can($permission)
    {
        /** @todo do the role permissions checking somewhere else? */
        return match ($permission) {
            'invite' => in_array($this, [self::Owner, self::Admin], true),
            default => false
        };
    }
}
