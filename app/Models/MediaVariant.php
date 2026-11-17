<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaVariant extends Model
{
    public $timestamps = false;
    protected $fillable = ['media_id', 'name', 'width', 'height', 'data'];
}
