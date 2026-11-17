<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyImage extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'media_id', 'path', 'is_cover', 'sort'];
    protected $casts = ['is_cover' => 'boolean'];

    public function media(): BelongsTo { return $this->belongsTo(Media::class); }

    /** thumb للكروت، medium للصفحات، large للمعرض */
    public function variantUrl(string $variant = 'medium'): string
    {
        if ($this->media_id) {
            $uuid = $this->relationLoaded('media') ? $this->media?->uuid : Media::whereKey($this->media_id)->value('uuid');
            if ($uuid) {
                return route('media.show', ['media' => $uuid, 'variant' => $variant]);
            }
        }

        // صور قديمة / افتراضية
        if (str_starts_with($this->path, 'http')) {
            return $this->path;
        }

        return str_starts_with($this->path, 'images/') ? asset($this->path) : asset('storage/'.$this->path);
    }

    public function getUrlAttribute(): string { return $this->variantUrl('medium'); }
    public function getThumbUrlAttribute(): string { return $this->variantUrl('thumb'); }
    public function getLargeUrlAttribute(): string { return $this->variantUrl('large'); }
}
