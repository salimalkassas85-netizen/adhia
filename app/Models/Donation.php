<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Donation extends Model
{
    protected $fillable = [
        'code',
        'donor_name',
        'donor_phone',
        'donor_area_id',
        'target_area_id',
        'donation_scope',
        'donation_type',
        'amount',
        'meat_kg',
        'pickup_address',
        'latitude',
        'longitude',
        'location_accuracy',
        'status',
        'pickup_agent_id',
        'pickup_assigned_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'meat_kg' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'pickup_assigned_at' => 'datetime',
        ];
    }

    public function donorArea(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'donor_area_id');
    }

    public function targetArea(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'target_area_id');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(Allocation::class);
    }

    public function pickupAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pickup_agent_id');
    }

    public function statusLogs(): MorphMany
    {
        return $this->morphMany(StatusLog::class, 'loggable');
    }

    public function pickupMapsUrl(): ?string
    {
        return $this->latitude && $this->longitude
            ? "https://www.openstreetmap.org/?mlat={$this->latitude}&mlon={$this->longitude}#map=17/{$this->latitude}/{$this->longitude}"
            : null;
    }
}
