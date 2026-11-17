<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Media extends Model
{
    protected $table = 'media';
    protected $fillable = ['uuid', 'user_id', 'sha256', 'mime', 'width', 'height', 'bytes', 'alt'];

    public function variants(): HasMany { return $this->hasMany(MediaVariant::class); }

    public function url(string $variant = 'medium'): string
    {
        return route('media.show', ['media' => $this->uuid, 'variant' => $variant]);
    }
}
