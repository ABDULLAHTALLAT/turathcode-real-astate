{{-- صف طلب: $r = RentalRequest, $as = owner|tenant --}}
<a href="{{ route('requests.show', $r) }}" class="flex items-center gap-4 p-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
    <img src="{{ $r->property->cover_url }}" class="w-16 h-16 rounded-xl object-cover bg-slate-100 shrink-0" alt="">
    <div class="min-w-0 flex-1">
        <p class="font-bold text-ink truncate">{{ $r->property->title }}</p>
        <p class="text-xs text-slate-500 mt-0.5">{{ $r->code }} · {{ $as === 'owner' ? 'من: '.$r->tenant->name : 'المالك: '.\Illuminate\Support\Str::before($r->owner->name, ' ') }} · {{ egp($r->proposed_rent) }} / شهر · {{ $r->lease_years }} سنة</p>
        <p class="text-[11px] text-slate-400 mt-0.5">{{ $r->created_at->diffForHumans() }}</p>
    </div>
    <div class="text-end shrink-0">
        @include('partials.badge', ['color' => $r->status_color, 'text' => $r->status_label])
        @if ($r->canRespond(auth()->user()))<p class="text-[11px] font-bold text-brand-dark mt-1.5">⚡ مطلوب ردك</p>@endif
    </div>
</a>
