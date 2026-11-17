<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'avatar', 'governorate', 'city', 'bio', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'email_verified_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isOwner(): bool { return $this->role === 'owner'; }
    public function isTenant(): bool { return $this->role === 'tenant'; }

    public function getRoleLabelAttribute(): string
    {
        return catalog('roles')[$this->role] ?? $this->role;
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset($this->avatar.'-thumb.webp')
            : 'https://ui-avatars.com/api/?background=6bbce9&color=0b2447&bold=true&size=200&name='.urlencode($this->name);
    }

    public function profile(): HasOne { return $this->hasOne(TenantProfile::class); }
    public function properties(): HasMany { return $this->hasMany(Property::class, 'owner_id'); }
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'favorites')->withTimestamps();
    }
    /** الطلبات التي أرسلها المستأجر */
    public function sentRequests(): HasMany { return $this->hasMany(RentalRequest::class, 'tenant_id'); }
    /** الطلبات الواردة على عقارات المالك */
    public function receivedRequests(): HasMany { return $this->hasMany(RentalRequest::class, 'owner_id'); }
}
