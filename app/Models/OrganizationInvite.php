<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationInvite extends Model
{
    protected $fillable = [
        'organization_id',
        'invited_by_user_id',
        'email',
        'role',
        'token',
        'expires_at',
        'accepted_at'
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
