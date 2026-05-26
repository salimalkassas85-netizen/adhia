<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'area_id',
        'pledge_accepted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'pledge_accepted_at' => 'datetime',
        ];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function assignedRequests(): HasMany
    {
        return $this->hasMany(BeneficiaryRequest::class, 'assigned_agent_id');
    }

    public function assignedDonations(): HasMany
    {
        return $this->hasMany(Donation::class, 'assigned_agent_id');
    }

    public function adminNotifications(): HasMany
    {
        return $this->hasMany(AdminNotification::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSuperAdmin(): bool
    {
        return $this->isAdmin() && $this->area_id === null;
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function roleLabel(): string
    {
        if ($this->isSuperAdmin()) {
            return 'سوبر أدمن';
        }

        if ($this->isAdmin()) {
            return 'أدمن منطقة';
        }

        return 'مستخدم';
    }
}
