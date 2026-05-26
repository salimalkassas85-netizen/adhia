<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $fillable = ['name', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function beneficiaryRequests(): HasMany
    {
        return $this->hasMany(BeneficiaryRequest::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'target_area_id');
    }
}
