@php
    $inCompare = in_array($property->id, session('compare', []));
    $u = auth()->user();
    $fav = $u && $u->isTenant() ? $u->favorites->contains($property->id) : false;
@endphp
<article class="group card overflow-hidden hover:shadow-xl hover:border-brand/60 transition flex flex-col h-full">
    <div class="relative h-48 bg-slate-100 overflow-hidden">
        <a href="{{ route('properties.show', $property) }}" class="block h-full">
            <img src="{{ $property->cover_url }}" alt="{{ $property->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
        </a>
        <span class="absolute top-3 start-3 bg-ink/85 text-brand text-xs font-semibold px-2.5 py-1 rounded-lg">{{ $property->stage_label }}</span>
        @if ($property->discount_percent)
            <span class="absolute bottom-3 start-3 bg-emerald-500 text-white text-xs font-bold px-2.5 py-1 rounded-lg">وفّر {{ $property->discount_percent }}%</span>
        @endif
        @if ($property->status === 'rented')
            <span class="absolute inset-0 bg-ink/60 flex items-center justify-center text-white font-bold text-lg">تم التأجير</span>
        @endif
        <div class="absolute top-3 end-3 flex gap-2">
            @if (! $u)
                <a href="{{ route('login') }}" class="w-8 h-8 rounded-full bg-white/95 text-ink flex items-center justify-center shadow hover:scale-105 transition" title="سجّل دخولك لحفظ العقار"><i class="bi bi-heart"></i></a>
            @elseif ($u->isTenant())
                <form method="POST" action="{{ route('favorites.toggle', $property->id) }}">@csrf
                    <button class="w-8 h-8 rounded-full bg-white/95 flex items-center justify-center shadow hover:scale-105 transition {{ $fav ? 'text-rose-500' : 'text-ink' }}" title="المفضلة"><i class="bi {{ $fav ? 'bi-heart-fill' : 'bi-heart' }}"></i></button>
                </form>
            @endif
            <form method="POST" action="{{ route('compare.toggle', $property->id) }}">@csrf
                <button class="w-8 h-8 rounded-full flex items-center justify-center shadow hover:scale-105 transition {{ $inCompare ? 'bg-ink text-brand' : 'bg-white/95 text-ink' }}" title="قارن"><i class="bi bi-arrow-left-right"></i></button>
            </form>
        </div>
    </div>

    <div class="p-4 flex flex-col flex-1">
        <p class="text-xs text-slate-500 mb-1.5 flex items-center gap-1"><i class="bi bi-geo-alt-fill text-brand-dark"></i> {{ $property->city }}، {{ $property->governorate }}</p>
        <a href="{{ route('properties.show', $property) }}" class="font-bold text-ink hover:text-brand-dark line-clamp-2 min-h-[48px] mb-3">{{ $property->title }}</a>

        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500 mb-3">
            <span><i class="bi bi-building text-slate-400"></i> {{ $property->type_label }}</span>
            <span><i class="bi bi-arrows-fullscreen text-slate-400"></i> {{ $property->area_m2 }} م²</span>
            @if ($property->rooms)<span><i class="bi bi-door-closed text-slate-400"></i> {{ $property->rooms }} غرف</span>@endif
        </div>

        @php($s = $property->relationLoaded('finishingItems') ? $property->finishingSummary() : null)
        @if ($s && $s['total'])
            <div class="mb-3">
                <div class="flex justify-between text-[11px] text-slate-500 mb-1"><span>اكتمال التشطيب</span><span>{{ $s['done'] }}/{{ $s['total'] }} بند</span></div>
                <div class="h-1.5 rounded-full bg-slate-100 overflow-hidden flex dark:bg-slate-800">
                    <span class="bg-emerald-400" style="width: {{ $s['done'] / $s['total'] * 100 }}%"></span>
                    <span class="bg-amber-300" style="width: {{ $s['partial'] / $s['total'] * 100 }}%"></span>
                </div>
            </div>
        @endif

        <div class="mt-auto pt-3 border-t border-slate-100 flex items-end justify-between gap-2">
            <div>
                @if ($property->market_rent && $property->market_rent > $property->offered_rent)
                    <p class="text-[11px] text-slate-400 line-through">{{ egp($property->market_rent) }}</p>
                @endif
                <p class="font-extrabold text-ink text-lg leading-none">{{ egp($property->offered_rent) }}<span class="text-xs font-medium text-slate-500"> / شهر</span></p>
            </div>
            <span class="text-[11px] text-slate-500 text-end">{{ $property->min_lease_years }}–{{ $property->max_lease_years }} سنة<br>مدة العقد</span>
        </div>
    </div>
</article>
