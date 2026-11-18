@php
    $inCompare = in_array($property->id, session('compare', []));
    $u = auth()->user();
    $fav = $u && $u->isTenant() ? $u->favorites->contains($property->id) : false;
    $pill = 'h-8 px-3 rounded-full text-xs font-semibold shadow transition hover:scale-105';
@endphp
<article class="group card overflow-hidden hover:shadow-2xl hover:border-brand/60 transition flex flex-col h-full">
    <div class="relative h-60 sm:h-64 bg-slate-100 overflow-hidden">
        <a href="{{ route('properties.show', $property) }}" class="block h-full">
            <img src="{{ $property->coverUrl('medium') }}" alt="{{ $property->title }}" loading="lazy" decoding="async" width="960" height="640" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
            <span class="absolute inset-0 bg-gradient-to-t from-black/45 via-transparent to-black/10"></span>
        </a>
        <span class="absolute top-4 start-4 bg-ink/90 text-brand text-xs font-semibold px-3 py-1.5 rounded-full">{{ $property->stage_label }}</span>
        @if ($property->is_featured)
            <span class="absolute bottom-4 end-4 bg-amber-400 text-ink text-xs font-bold px-3 py-1.5 rounded-full">مميز</span>
        @endif
        @if ($property->discount_percent)
            <span class="absolute bottom-4 start-4 bg-emerald-500 text-white text-xs font-bold px-3 py-1.5 rounded-full">وفّر {{ $property->discount_percent }}%</span>
        @endif
        @if ($property->status === 'rented')
            <span class="absolute inset-0 bg-ink/60 flex items-center justify-center text-white font-bold text-xl">تم التأجير</span>
        @endif
        <div class="absolute top-4 end-4 flex gap-2">
            @if (! $u)
                <a href="{{ route('login') }}" class="{{ $pill }} bg-white/95 text-ink inline-flex items-center" title="سجّل دخولك لحفظ العقار">حفظ</a>
            @elseif ($u->isTenant())
                <form method="POST" action="{{ route('favorites.toggle', $property->id) }}">@csrf
                    <button class="{{ $pill }} {{ $fav ? 'bg-rose-500 text-white' : 'bg-white/95 text-ink' }}" aria-pressed="{{ $fav ? 'true' : 'false' }}">{{ $fav ? 'محفوظ' : 'حفظ' }}</button>
                </form>
            @endif
            <form method="POST" action="{{ route('compare.toggle', $property->id) }}">@csrf
                <button class="{{ $pill }} {{ $inCompare ? 'bg-ink text-brand' : 'bg-white/95 text-ink' }}" aria-pressed="{{ $inCompare ? 'true' : 'false' }}">{{ $inCompare ? 'في المقارنة' : 'قارن' }}</button>
            </form>
        </div>
    </div>

    <div class="p-6 flex flex-col flex-1">
        <p class="text-sm text-slate-500 mb-2">{{ $property->city }}، {{ $property->governorate }}</p>
        <a href="{{ route('properties.show', $property) }}" class="font-bold text-ink text-lg leading-snug hover:text-brand-dark line-clamp-2 min-h-[56px] mb-4">{{ $property->title }}</a>

        <dl class="grid grid-cols-3 gap-3 text-center border-y border-slate-100 py-3 mb-4 text-sm">
            <div><dt class="text-[11px] text-slate-400">النوع</dt><dd class="font-semibold text-ink mt-0.5">{{ $property->type_label }}</dd></div>
            <div><dt class="text-[11px] text-slate-400">المساحة</dt><dd class="font-semibold text-ink mt-0.5">{{ $property->area_m2 }} م²</dd></div>
            <div><dt class="text-[11px] text-slate-400">الغرف</dt><dd class="font-semibold text-ink mt-0.5">{{ $property->rooms ?: '—' }}</dd></div>
        </dl>

        @if ($property->vacant_since_years)
            <p class="text-xs text-amber-600 mb-3">معطل من {{ $property->vacant_since_years }} {{ $property->vacant_since_years > 10 ? 'سنة' : 'سنين' }}</p>
        @endif

        @php($s = $property->relationLoaded('finishingItems') ? $property->finishingSummary() : null)
        @if ($s && $s['total'])
            <div class="mb-4">
                <div class="flex justify-between text-xs text-slate-500 mb-1.5"><span>اكتمال التشطيب</span><span>{{ $s['done'] }}/{{ $s['total'] }} بند</span></div>
                <div class="h-1.5 rounded-full bg-slate-100 overflow-hidden flex dark:bg-slate-800">
                    <span class="bg-emerald-400" style="width: {{ $s['done'] / $s['total'] * 100 }}%"></span>
                    <span class="bg-amber-300" style="width: {{ $s['partial'] / $s['total'] * 100 }}%"></span>
                </div>
            </div>
        @endif

        <div class="mt-auto pt-2 flex items-end justify-between gap-2">
            <div>
                @if ($property->market_rent && $property->market_rent > $property->offered_rent)
                    <p class="text-xs text-slate-400 line-through">{{ egp($property->market_rent) }}</p>
                @endif
                <p class="font-extrabold text-ink text-2xl leading-none">{{ egp($property->offered_rent) }}<span class="text-sm font-medium text-slate-500"> / شهر</span></p>
            </div>
            <span class="text-xs text-slate-500 text-end leading-5">{{ $property->min_lease_years }}–{{ $property->max_lease_years }} سنة<br>مدة العقد</span>
        </div>
    </div>
</article>
