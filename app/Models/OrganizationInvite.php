<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;

class OrganizationInvite extends Model
{
    protected $fillable = [
        'organization_id',
        'invited_by_user_id',
        'email',
        'role',
        'token',
        'expires_at',
        'accepted_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => Role::class,
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    public function createTemporarySignedRoute($expires = null)
    {
        $expires = $expires ?? $this->expires_at ?? now()->addDays(7);

        return URL::temporarySignedRoute(
            name: 'invitations.accept',
            expiration: $expires,
            parameters: [
                'token' => $this->token,
                'email' => $this->email,
            ],
        );
    }

    public function scopePending(Builder $builder)
    {
        $builder->whereNull('accepted_at')
            ->where(function (Builder $builder) {
                $builder->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
