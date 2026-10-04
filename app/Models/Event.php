<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'title', 'slug', 'description', 'status', 'starts_at', 'ends_at'
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function tiers(): HasMany
    {
        return $this->hasMany(TicketTier::class);
    }

    // Query Scope: Filter only publicly purchasable events
    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published')->where('starts_at', '>', now());
    }
}