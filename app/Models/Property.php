<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id', 'title', 'slug', 'property_type', 'governorate', 'city', 'district', 'street',
        'building_number', 'floor_number', 'unit_number', 'landmark', 'latitude', 'longitude',
        'area_m2', 'rooms', 'bathrooms', 'floors_count', 'finishing_stage', 'finishing_notes', 'description',
        'utilities', 'allowed_purposes', 'vacant_since_years', 'market_rent', 'offered_rent',
        'min_lease_years', 'max_lease_years', 'rent_free_months', 'annual_increase_percent', 'finishing_by',
        'status', 'reject_reason', 'is_featured', 'views', 'terms_accepted_at', 'approved_at',
    ];

    protected $casts = [
        'utilities' => 'array',
        'allowed_purposes' => 'array',
        'is_featured' => 'boolean',
        'terms_accepted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Property $p) {
            $p->slug = $p->slug ?: static::uniqueSlug($p->title);
        });

        // لما العقار يتحذف نهائيًا نمسح فولدر صوره كله
        static::forceDeleted(fn (Property $p) => \Illuminate\Support\Facades\File::deleteDirectory(public_path('uploads/properties/'.$p->id)));
    }

    public static function uniqueSlug(string $title): string
    {
        $base = Str::slug($title, '-', 'ar') ?: 'property';
        $slug = $base;
        $i = 1;
        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.(++$i);
        }

        return $slug;
    }

    // ---------- العلاقات ----------
    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'owner_id'); }
    public function images(): HasMany { return $this->hasMany(PropertyImage::class)->orderByDesc('is_cover')->orderBy('sort'); }
    public function finishingItems(): HasMany { return $this->hasMany(FinishingItem::class); }
    public function requests(): HasMany { return $this->hasMany(RentalRequest::class); }

    // ---------- Scopes ----------
    public function scopePublished(Builder $q): Builder { return $q->where('status', 'approved'); }

    public function scopeFilter(Builder $q, array $f): Builder
    {
        return $q
            ->when($f['q'] ?? null, fn ($q, $v) => $q->where(fn ($w) => $w
                ->where('title', 'like', "%$v%")->orWhere('city', 'like', "%$v%")
                ->orWhere('district', 'like', "%$v%")->orWhere('governorate', 'like', "%$v%")))
            ->when($f['governorate'] ?? null, fn ($q, $v) => $q->where('governorate', $v))
            ->when($f['type'] ?? null, fn ($q, $v) => $q->where('property_type', $v))
            ->when($f['stage'] ?? null, fn ($q, $v) => $q->where('finishing_stage', $v))
            ->when($f['purpose'] ?? null, fn ($q, $v) => $q->whereJsonContains('allowed_purposes', $v))
            ->when($f['min_price'] ?? null, fn ($q, $v) => $q->where('offered_rent', '>=', (int) $v))
            ->when($f['max_price'] ?? null, fn ($q, $v) => $q->where('offered_rent', '<=', (int) $v))
            ->when($f['min_area'] ?? null, fn ($q, $v) => $q->where('area_m2', '>=', (int) $v))
            ->when($f['years'] ?? null, fn ($q, $v) => $q->where('min_lease_years', '<=', (int) $v)->where('max_lease_years', '>=', (int) $v));
    }

    // ---------- Accessors ----------
    public function getTypeLabelAttribute(): string { return catalog('property_types')[$this->property_type] ?? $this->property_type; }
    public function getStageLabelAttribute(): string { return catalog('finishing_stages')[$this->finishing_stage]['label'] ?? $this->finishing_stage; }
    public function getStageLevelAttribute(): int { return catalog('finishing_stages')[$this->finishing_stage]['level'] ?? 1; }
    public function getFinishingByLabelAttribute(): string { return catalog('finishing_by')[$this->finishing_by] ?? ''; }
    public function getStatusLabelAttribute(): string { return catalog('property_statuses')[$this->status]['label'] ?? $this->status; }
    public function getStatusColorAttribute(): string { return catalog('property_statuses')[$this->status]['color'] ?? 'slate'; }

    public function coverUrl(string $variant = 'medium'): string
    {
        $img = $this->relationLoaded('images') ? $this->images->first() : $this->images()->first();

        if ($img) {
            return $img->variantUrl($variant);
        }

        $fallback = 'images/covers/'.$this->property_type.'.svg';

        return asset(file_exists(public_path($fallback)) ? $fallback : 'images/placeholder.svg');
    }

    public function getCoverUrlAttribute(): string { return $this->coverUrl('medium'); }
    public function getCoverThumbAttribute(): string { return $this->coverUrl('thumb'); }

    /** العنوان العام (بدون رقم العقار/الشقة) — يظهر لأي زائر */
    public function getPublicAddressAttribute(): string
    {
        return collect([$this->street, $this->district, $this->city, $this->governorate])->filter()->implode('، ');
    }

    /** العنوان الكامل بالتفصيل — يظهر فقط بعد اكتمال الاتفاق وسداد نسبة المنصة */
    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->building_number ? 'عقار رقم '.$this->building_number : null,
            $this->floor_number ? 'الدور '.$this->floor_number : null,
            $this->unit_number ? 'شقة/وحدة '.$this->unit_number : null,
            $this->landmark ? '(بجوار '.$this->landmark.')' : null,
            $this->public_address,
        ])->filter()->implode(' - ');
    }

    /** نسبة الخصم عن سعر السوق */
    public function getDiscountPercentAttribute(): ?int
    {
        if (! $this->market_rent || $this->market_rent <= $this->offered_rent) {
            return null;
        }

        return (int) round((1 - $this->offered_rent / $this->market_rent) * 100);
    }

    /** إجمالي ما يوفره المستأجر خلال أقصى مدة */
    public function savingsFor(int $years): int
    {
        return $this->market_rent ? max(0, ($this->market_rent - $this->offered_rent) * 12 * $years) : 0;
    }

    /** ملخص بنود التشطيب: تم/جزئي/ناقص */
    public function finishingSummary(): array
    {
        $items = $this->relationLoaded('finishingItems') ? $this->finishingItems : $this->finishingItems()->get();

        return [
            'done' => $items->where('status', 'done')->count(),
            'partial' => $items->where('status', 'partial')->count(),
            'missing' => $items->where('status', 'missing')->count(),
            'total' => $items->where('status', '!=', 'na')->count(),
        ];
    }

    public function isFavoritedBy(?User $user): bool
    {
        return $user ? $user->favorites()->where('property_id', $this->id)->exists() : false;
    }
}
