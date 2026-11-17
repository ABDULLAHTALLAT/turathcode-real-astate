<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyImage extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'path', 'is_cover', 'sort'];
    protected $casts = ['is_cover' => 'boolean'];

    protected static function booted(): void
    {
        // مسح ملفات الصورة من public/uploads أول ما السجل يتحذف
        static::deleted(fn (self $img) => app(\App\Services\ImageProcessor::class)->delete($img->path));
    }

    /** thumb للكروت، medium للصفحات، large للمعرض — الملفات في public/uploads/properties/{id}/ */
    public function variantUrl(string $variant = 'medium'): string
    {
        if (str_starts_with($this->path, 'http')) {
            return $this->path;
        }

        // صورة افتراضية قديمة (images/covers/x.svg)
        if (str_starts_with($this->path, 'images/')) {
            return asset($this->path);
        }

        return asset($this->path.'-'.$variant.'.webp');
    }

    public function getUrlAttribute(): string { return $this->variantUrl('medium'); }
    public function getThumbUrlAttribute(): string { return $this->variantUrl('thumb'); }
    public function getLargeUrlAttribute(): string { return $this->variantUrl('large'); }
}
