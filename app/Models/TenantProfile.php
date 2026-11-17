<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantProfile extends Model
{
    protected $fillable = [
        'user_id', 'marital_status', 'family_members', 'purpose', 'job',
        'budget_min', 'budget_max', 'preferred_governorate', 'preferred_lease_years',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
