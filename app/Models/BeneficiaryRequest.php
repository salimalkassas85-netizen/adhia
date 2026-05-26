<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class BeneficiaryRequest extends Model
{
    protected $fillable = [
        'code',
        'first_name',
        'phone',
        'area_id',
        'family_members_count',
        'has_children',
        'has_elderly',
        'full_address',
        'landmark',
        'latitude',
        'longitude',
        'location_accuracy',
        'status',
        'assigned_agent_id',
        'approved_at',
        'assigned_at',
        'delivered_at',
        'admin_notes',
        'agent_notes',
    ];

    protected function casts(): array
    {
        return [
            'has_children' => 'boolean',
            'has_elderly' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'approved_at' => 'datetime',
            'assigned_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    public function statusLogs(): MorphMany
    {
        return $this->morphMany(StatusLog::class, 'loggable');
    }

    public function mapsUrl(): string
    {
        return "https://www.openstreetmap.org/?mlat={$this->latitude}&mlon={$this->longitude}#map=17/{$this->latitude}/{$this->longitude}";
    }
}
