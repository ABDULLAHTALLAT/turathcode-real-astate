<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyImage extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'path', 'is_cover', 'sort'];
    protected $casts = ['is_cover' => 'boolean'];

    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->path, 'http')) {
            return $this->path;
        }

        // صور افتراضية داخل public/images
        return str_starts_with($this->path, 'images/') ? asset($this->path) : asset('storage/'.$this->path);
    }
}
