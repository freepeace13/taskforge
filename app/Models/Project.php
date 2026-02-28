<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'archived_at',
    ];

    public function archive(): self
    {
        $this->archived_at = now();

        return $this;
    }

    public function restore(): self
    {
        $this->archived_at = null;

        return $this;
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
