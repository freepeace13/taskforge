<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $fillable = [
        'organization_id',
        'name',
        'slug',
        'abbrev',
        'logo',
        'description',
        'settings',
        'task_sequence',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'archived_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Project $project): void {
            if (blank($project->abbrev)) {
                $project->abbrev = self::generateUniqueAbbrev(
                    organizationId: (int) $project->organization_id,
                    name: (string) $project->name,
                );
            }

            if (blank($project->slug)) {
                $project->slug = self::generateUniqueSlug(
                    organizationId: (int) $project->organization_id,
                    name: (string) $project->name,
                );
            }
        });
    }

    private static function generateUniqueSlug(int $organizationId, string $name): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'project';
        }

        $slug = $base;
        $suffix = 2;

        while (self::query()->where('organization_id', $organizationId)->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private static function generateUniqueAbbrev(int $organizationId, string $name): string
    {
        $normalized = preg_replace('/[^A-Za-z0-9 ]+/', ' ', $name) ?? $name;
        $words = array_values(array_filter(preg_split('/\s+/', trim($normalized)) ?: []));

        if (count($words) >= 2) {
            $base = strtoupper(substr($words[0], 0, 1).substr($words[1], 0, 1));
        } else {
            $base = strtoupper(substr($words[0] ?? 'PRJ', 0, 3));
        }

        $base = preg_replace('/[^A-Z0-9]/', '', $base) ?? 'PRJ';
        $base = substr($base, 0, 3);
        if ($base === '') {
            $base = 'PRJ';
        }

        $abbrev = $base;
        $suffix = 2;

        while (self::query()->where('organization_id', $organizationId)->where('abbrev', $abbrev)->exists()) {
            $suffixString = (string) $suffix;
            $abbrev = substr($base, 0, max(0, 3 - strlen($suffixString))).$suffixString;
            $suffix++;
        }

        return $abbrev;
    }

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

    /**
     * Fill missing slug/abbrev for rows created before those columns existed.
     */
    public function fillMissingIdentity(): void
    {
        $dirty = false;

        if (blank($this->abbrev)) {
            $this->abbrev = self::generateUniqueAbbrev(
                organizationId: (int) $this->organization_id,
                name: (string) $this->name,
            );
            $dirty = true;
        }

        if (blank($this->slug)) {
            $this->slug = self::generateUniqueSlug(
                organizationId: (int) $this->organization_id,
                name: (string) $this->name,
            );
            $dirty = true;
        }

        if ($dirty) {
            $this->save();
        }
    }

    /**
     * Assign keys to tasks that are missing them (e.g. legacy rows).
     */
    public function backfillMissingTaskKeys(): void
    {
        $this->fillMissingIdentity();
        $this->refresh();

        $abbrev = (string) $this->abbrev;

        $maxSeq = (int) $this->task_sequence;

        foreach ($this->tasks()->whereNotNull('key')->get() as $task) {
            if (preg_match('/-(\d+)$/', (string) $task->key, $matches) === 1) {
                $maxSeq = max($maxSeq, (int) $matches[1]);
            }
        }

        $sequence = $maxSeq;

        foreach ($this->tasks()->whereNull('key')->orderBy('id')->get() as $task) {
            $sequence++;
            $task->forceFill([
                'key' => sprintf('%s-%02d', $abbrev, $sequence),
            ])->saveQuietly();
        }

        $this->forceFill(['task_sequence' => $sequence])->save();
    }
}
