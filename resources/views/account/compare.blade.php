@extends('layouts.app')
@section('title', 'مقارنة العقارات')
@section('content')
<section class="bg-ink text-white"><div class="max-w-7xl mx-auto px-4 py-10 flex items-center justify-between"><h1 class="text-3xl font-extrabold">مقارنة العقارات</h1>
    @if ($properties->count())<form method="POST" action="{{ route('compare.clear') }}">@csrf<button class="btn btn-ghost !bg-transparent !text-white !border-white/30">مسح المقارنة</button></form>@endif</div></section>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
@if ($properties->isEmpty())
    <div class="card p-12 text-center text-sm text-slate-500"><i class="bi bi-arrow-left-right text-4xl text-slate-300"></i><p class="mt-3">اضغط على أيقونة المقارنة في أي عقار عشان تضيفه هنا (حتى 4 عقارات). <a href="{{ route('properties.index') }}" class="text-brand-dark font-bold">تصفح العقارات</a></p></div>
@else
    @php
        $rows = [
            'السعر المعروض / شهر' => fn ($p) => egp($p->offered_rent),
            'سعر السوق / شهر' => fn ($p) => egp($p->market_rent),
            'نسبة التوفير' => fn ($p) => $p->discount_percent ? $p->discount_percent.'%' : '—',
            'النوع' => fn ($p) => $p->type_label,
            'المساحة' => fn ($p) => $p->area_m2.' م²',
            'الغرف / الحمامات' => fn ($p) => ($p->rooms ?? '—').' / '.($p->bathrooms ?? '—'),
            'مرحلة التشطيب' => fn ($p) => $p->stage_label,
            'بنود مكتملة' => fn ($p) => ($s = $p->finishingSummary())['total'] ? $s['done'].' من '.$s['total'] : '—',
            'بنود ناقصة' => fn ($p) => ($s = $p->finishingSummary())['total'] ? $s['missing'] : '—',
            'التشطيب على' => fn ($p) => $p->finishing_by_label,
            'مدة العقد' => fn ($p) => $p->min_lease_years.' - '.$p->max_lease_years.' سنة',
            'فترة السماح' => fn ($p) => $p->rent_free_months ? $p->rent_free_months.' شهر' : '—',
            'الموقع' => fn ($p) => $p->city.'، '.$p->governorate,
        ];
    @endphp
    <div class="overflow-x-auto"><table class="w-full min-w-[640px] text-sm border-separate border-spacing-0">
        <thead><tr><th class="w-40"></th>
            @foreach ($properties as $p)<th class="p-3 align-top font-normal"><a href="{{ route('properties.show', $p) }}" class="block card overflow-hidden text-start"><img src="{{ $p->cover_url }}" class="h-28 w-full object-cover" alt=""><span class="block p-3 font-bold text-ink text-sm line-clamp-2">{{ $p->title }}</span></a>
                <form method="POST" action="{{ route('compare.toggle', $p->id) }}" class="mt-2">@csrf<button class="text-xs text-rose-500 hover:underline">إزالة</button></form></th>@endforeach</tr></thead>
        <tbody>@foreach ($rows as $label => $fn)
            <tr class="{{ $loop->even ? 'bg-slate-50 dark:bg-slate-800/40' : '' }}"><td class="p-3 text-xs font-bold text-slate-500">{{ $label }}</td>
                @foreach ($properties as $p)<td class="p-3 font-semibold text-ink">{{ $fn($p) }}</td>@endforeach</tr>
        @endforeach</tbody>
    </table></div>
@endif
</div>
@endsection
