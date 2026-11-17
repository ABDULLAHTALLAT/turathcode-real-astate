<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalRequest extends Model
{
    protected $fillable = [
        'code', 'property_id', 'tenant_id', 'owner_id', 'purpose', 'marital_status', 'occupants', 'start_date', 'message',
        'proposed_rent', 'lease_years', 'last_offer_by', 'status', 'total_value',
        'owner_commission_percent', 'tenant_commission_percent', 'owner_commission', 'tenant_commission',
        'owner_commission_paid_at', 'tenant_commission_paid_at', 'tenant_terms_accepted_at',
        'accepted_at', 'completed_at', 'admin_note',
    ];

    protected $casts = [
        'start_date' => 'date',
        'owner_commission_paid_at' => 'datetime',
        'tenant_commission_paid_at' => 'datetime',
        'tenant_terms_accepted_at' => 'datetime',
        'accepted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::created(function (RentalRequest $r) {
            if (! $r->code) {
                $r->forceFill(['code' => 'BF-'.str_pad((string) $r->id, 6, '0', STR_PAD_LEFT)])->saveQuietly();
            }
        });
    }

    public function property(): BelongsTo { return $this->belongsTo(Property::class)->withTrashed(); }
    public function tenant(): BelongsTo { return $this->belongsTo(User::class, 'tenant_id'); }
    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'owner_id'); }

    public function getStatusLabelAttribute(): string { return catalog('request_statuses')[$this->status]['label'] ?? $this->status; }
    public function getStatusColorAttribute(): string { return catalog('request_statuses')[$this->status]['color'] ?? 'slate'; }
    public function getPurposeLabelAttribute(): string { return catalog('purposes')[$this->purpose] ?? $this->purpose; }
    public function getMaritalLabelAttribute(): string { return catalog('marital_statuses')[$this->marital_status] ?? '—'; }

    /** إجمالي قيمة العقد حسب العرض الحالي */
    public function currentTotal(): int
    {
        return (int) $this->proposed_rent * 12 * (int) $this->lease_years;
    }

    /** الدور على مين في الرد؟ (الطرف اللي ما قدمش آخر عرض) */
    public function awaitingParty(): ?string
    {
        if (! in_array($this->status, ['pending', 'negotiating'], true)) {
            return null;
        }

        return $this->last_offer_by === 'tenant' ? 'owner' : 'tenant';
    }

    public function canRespond(User $user): bool
    {
        return $this->awaitingParty() === 'owner' && $user->id === $this->owner_id
            || $this->awaitingParty() === 'tenant' && $user->id === $this->tenant_id;
    }

    /**
     * الاتفاق على الشروط: نحسب إجمالي العقد ونسبة المنصة من الطرفين ونثبّتها.
     * بيانات التواصل الكاملة ما بتظهرش غير بعد سداد النسبة من الطرفين.
     */
    public function accept(): void
    {
        $total = $this->currentTotal();
        $op = Setting::commission('owner');
        $tp = Setting::commission('tenant');

        $this->update([
            'status' => 'accepted',
            'total_value' => $total,
            'owner_commission_percent' => $op,
            'tenant_commission_percent' => $tp,
            'owner_commission' => (int) round($total * $op / 100),
            'tenant_commission' => (int) round($total * $tp / 100),
            'accepted_at' => now(),
        ]);
    }

    /** مصروفات المنصة الإجمالية (حقك) */
    public function getPlatformEarningsAttribute(): int
    {
        return (int) $this->owner_commission + (int) $this->tenant_commission;
    }

    public function getPaidEarningsAttribute(): int
    {
        return ($this->owner_commission_paid_at ? (int) $this->owner_commission : 0)
            + ($this->tenant_commission_paid_at ? (int) $this->tenant_commission : 0);
    }

    /** أدمن يسجل سداد نسبة طرف؛ لما الاتنين يسددوا يكتمل الاتفاق ويتأجر العقار */
    public function markPaid(string $party): void
    {
        $this->forceFill([$party.'_commission_paid_at' => now()])->save();

        if ($this->owner_commission_paid_at && $this->tenant_commission_paid_at) {
            $this->update(['status' => 'completed', 'completed_at' => now()]);
            $this->property()->update(['status' => 'rented']);
            // باقي الطلبات المفتوحة على نفس العقار تتقفل
            static::where('property_id', $this->property_id)->where('id', '!=', $this->id)
                ->whereIn('status', ['pending', 'negotiating'])->update(['status' => 'cancelled']);
        }
    }

    /** هل بيانات التواصل مسموح تظهر؟ */
    public function contactsReleased(): bool
    {
        return $this->status === 'completed';
    }
}
