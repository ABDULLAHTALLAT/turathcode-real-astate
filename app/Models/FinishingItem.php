<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinishingItem extends Model
{
    use HasFactory;

    protected $table = 'property_finishing_items';
    protected $fillable = ['property_id', 'item_key', 'status', 'note'];

    public function getLabelAttribute(): string
    {
        return catalog('finishing_items')[$this->item_key]['label'] ?? $this->item_key;
    }
}
