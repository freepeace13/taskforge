<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'owner_id',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invites(): HasMany
    {
        return $this->hasMany(OrganizationInvite::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(OrganizationMember::class)
            ->withPivot('role');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
