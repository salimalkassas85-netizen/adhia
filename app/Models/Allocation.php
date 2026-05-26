<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Allocation extends Model
{
    protected $fillable = ['donation_id', 'beneficiary_request_id', 'area_id', 'status'];

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function beneficiaryRequest(): BelongsTo
    {
        return $this->belongsTo(BeneficiaryRequest::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
}
