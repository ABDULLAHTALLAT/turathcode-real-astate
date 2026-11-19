@extends('layouts.dashboard')
@section('title', 'لوحة الأدمن')
@php $c = $a['charts']; $q = $a['queues']; @endphp
@push('head')
<style>
    .viz-root { --c1:#2a78d6; --c2:#eb6834; --c3:#1baf7a; --c-muted:#a3a8b0; --v-ink:#0b2447; --v-sub:#5b6676; --v-grid:#e8ecf1; --v-surface:#fff; }
    html.dark .viz-root { --c1:#3987e5; --c2:#d95926; --c3:#199e70; --c-muted:#6b7280; --v-ink:#f1f5f9; --v-sub:#a5b0c0; --v-grid:#24304a; --v-surface:#111a2e; }
    .kpi:hover { border-color: #6bbce9; }
    .seg a { padding:.4rem .9rem; font-size:.78rem; font-weight:600; border-radius:.6rem; color:#64748b; }
    .seg a.on { background:#0b2447; color:#fff; } html.dark .seg a.on { background:#6bbce9; color:#0b2447; }
    .delta-up { color:#047857; } .delta-down { color:#be123c; } html.dark .delta-up { color:#34d399; } html.dark .delta-down { color:#fb7185; }
</style>
@endpush
@section('panel')
<div class="viz-root space-y-6">

    {{-- العنوان + المدى + التصدير --}}
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div><p class="eyebrow">ADMIN</p><h1 class="text-xl font-extrabold text-ink mt-1">لوحة إدارة المنصة</h1>
            <p class="text-xs text-slate-500 mt-1">آخر {{ $a['days'] }} يوم مقارنة بالفترة اللي قبلها · {{ now()->format('Y/m/d') }}</p></div>
        <div class="flex flex-wrap items-center gap-3 no-print">
            <div class="seg card !rounded-xl p-1 flex gap-1">
                @foreach ($ranges as $r)<a href="{{ route('dashboard', ['range' => $r]) }}" class="{{ $a['days'] === $r ? 'on' : '' }}">{{ $r === 365 ? 'سنة' : $r.' يوم' }}</a>@endforeach
            </div>
            <a href="{{ route('admin.requests.export') }}" class="btn btn-ghost !min-h-[38px] !py-1.5 !text-xs">تصدير الطلبات CSV</a>
        </div>
    </div>

    {{-- شريط الانتباه --}}
    <div class="grid sm:grid-cols-3 gap-3">
        <a href="{{ route('admin.properties', ['status' => 'pending']) }}" class="rounded-2xl p-4 hero-bg text-white flex items-center justify-between gap-3">
            <div><p class="text-xs text-white/60">عقارات بانتظار المراجعة</p><p class="text-2xl font-extrabold mt-1">{{ $q['pendingProps'] }}</p></div><span class="text-xs font-semibold text-brand">راجع ←</span></a>
        <a href="{{ route('admin.requests', ['status' => 'accepted']) }}" class="rounded-2xl p-4 hero-bg text-white flex items-center justify-between gap-3">
            <div><p class="text-xs text-white/60">اتفاقات بانتظار السداد · {{ $q['awaitingPay'] }}</p><p class="text-2xl font-extrabold mt-1 text-brand">{{ egp($q['awaitingAmount']) }}</p></div><span class="text-xs font-semibold text-brand">حصّل ←</span></a>
        <a href="{{ route('admin.messages') }}" class="rounded-2xl p-4 hero-bg text-white flex items-center justify-between gap-3">
            <div><p class="text-xs text-white/60">رسائل غير مقروءة</p><p class="text-2xl font-extrabold mt-1">{{ $q['unread'] }}</p></div><span class="text-xs font-semibold text-brand">افتح ←</span></a>
    </div>

    {{-- مؤشرات الأداء --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach ($a['kpis'] as $k)
            <div class="card kpi p-4">
                <p class="text-xs text-slate-500">{{ $k['label'] }}</p>
                <div class="flex items-baseline gap-2 mt-2 flex-wrap">
                    <p class="text-xl font-extrabold text-ink">{{ !empty($k['money']) ? egp($k['value']) : number_format($k['value'], is_float($k['value']) ? 1 : 0).($k['suffix'] ?? '') }}</p>
                    @if (!is_null($k['delta']))<span class="text-[11px] font-bold {{ $k['delta'] >= 0 ? 'delta-up' : 'delta-down' }}">{{ $k['delta'] >= 0 ? '▲' : '▼' }} {{ abs($k['delta']) }}%</span>@endif
                </div>
                <p class="text-[11px] text-slate-400 mt-1">{{ $k['hint'] }}</p>
                @if ($k['spark'])<div class="mt-2" data-spark='@json($k['spark'])'></div>@endif
            </div>
        @endforeach
    </div>

    {{-- النمو + المستخدمين --}}
    <div class="grid grid-cols-[minmax(0,1fr)] xl:grid-cols-3 gap-4">
        @include('partials.viz-card', ['key' => 'growth', 'title' => 'نمو المستخدمين', 'note' => 'تسجيلات جديدة من ملّاك ومستأجرين', 'labels' => $c['growth']['labels'], 'series' => ['ملّاك' => $c['growth']['owners'], 'مستأجرين' => $c['growth']['tenants']], 'class' => 'xl:col-span-2'])
        @include('partials.viz-card', ['key' => 'users', 'title' => 'توزيع المستخدمين', 'note' => 'إجمالي الحسابات', 'labels' => $c['users']['labels'], 'series' => ['العدد' => $c['users']['values']]])
    </div>

    {{-- الأرباح + القمع --}}
    <div class="grid grid-cols-[minmax(0,1fr)] xl:grid-cols-3 gap-4">
        @include('partials.viz-card', ['key' => 'revenue', 'title' => 'أرباح المنصة', 'note' => 'نسبة المحصّل والمستحق حسب تاريخ الاتفاق · الإجمالي المحصّل '.egp($a['kpis'][0]['value']), 'labels' => $c['revenue']['labels'], 'series' => ['محصّل' => $c['revenue']['collected'], 'مستحق' => $c['revenue']['pending']], 'money' => true, 'class' => 'xl:col-span-2'])
        @include('partials.viz-card', ['key' => 'funnel', 'title' => 'حالات الطلبات', 'note' => 'طلبات الفترة حسب الحالة', 'labels' => $c['funnel']['labels'], 'series' => ['طلبات' => $c['funnel']['values']]])
    </div>

    {{-- الطلبات + أيام الأسبوع --}}
    <div class="grid grid-cols-[minmax(0,1fr)] xl:grid-cols-3 gap-4">
        @include('partials.viz-card', ['key' => 'requests', 'title' => 'طلبات مقابل اتفاقات', 'note' => 'كام طلب اتحوّل لاتفاق فعلي', 'labels' => $c['requests']['labels'], 'series' => ['طلبات جديدة' => $c['requests']['created'], 'اتفاقات تمت' => $c['requests']['deals']], 'class' => 'xl:col-span-2'])
        @include('partials.viz-card', ['key' => 'weekday', 'title' => 'الطلبات حسب اليوم', 'note' => 'أنسب يوم لمتابعة الطلبات', 'labels' => $c['weekday']['labels'], 'series' => ['طلبات' => $c['weekday']['values']]])
    </div>

    {{-- العقارات --}}
    <div class="grid grid-cols-[minmax(0,1fr)] xl:grid-cols-3 gap-4">
        @include('partials.viz-card', ['key' => 'rent', 'title' => 'إيجار السوق مقابل المعروض', 'note' => 'متوسط الإيجار الشهري حسب مرحلة التشطيب (متوسط خصم '.$a['totals']['avgDiscount'].'%)', 'labels' => $c['rent']['labels'], 'series' => ['إيجار السوق' => $c['rent']['market'], 'المعروض' => $c['rent']['offered']], 'money' => true, 'class' => 'xl:col-span-2'])
        @include('partials.viz-card', ['key' => 'stages', 'title' => 'العقارات حسب التشطيب', 'labels' => $c['stages']['labels'], 'series' => ['عقارات' => $c['stages']['values']]])
    </div>
    <div class="grid grid-cols-[minmax(0,1fr)] lg:grid-cols-2 xl:grid-cols-4 gap-4">
        @include('partials.viz-card', ['key' => 'govs', 'title' => 'أكتر المحافظات', 'note' => 'عقارات منشورة ومؤجَّرة', 'labels' => $c['govs']['labels'], 'series' => ['عقارات' => $c['govs']['values']], 'h' => 260])
        @include('partials.viz-card', ['key' => 'types', 'title' => 'أنواع العقارات', 'labels' => $c['types']['labels'], 'series' => ['عقارات' => $c['types']['values']], 'h' => 260])
        @include('partials.viz-card', ['key' => 'status', 'title' => 'حالة العقارات', 'labels' => $c['status']['labels'], 'series' => ['عقارات' => $c['status']['values']], 'h' => 260])
        @include('partials.viz-card', ['key' => 'purposes', 'title' => 'غرض الإيجار', 'note' => 'من طلبات الفترة', 'labels' => $c['purposes']['labels'], 'series' => ['طلبات' => $c['purposes']['values']], 'h' => 260])
    </div>

    {{-- جداول: أفضل الملّاك + الأكثر مشاهدة + النشاط --}}
    <div class="grid grid-cols-[minmax(0,1fr)] xl:grid-cols-3 gap-4">
        <section class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700"><h3 class="font-bold text-ink text-sm">أفضل الملّاك</h3><p class="text-xs text-slate-500">حسب قيمة العقود المكتملة</p></div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @foreach ($a['topOwners'] as $o)
                    <div class="px-5 py-3 flex items-center gap-3 text-sm"><img src="{{ $o->avatar_url }}" class="w-9 h-9 rounded-full object-cover" alt="">
                        <div class="min-w-0 flex-1"><p class="font-semibold text-ink truncate">{{ $o->name }}</p><p class="text-[11px] text-slate-400">{{ $o->properties_count }} عقار · {{ $o->rented_count }} مؤجَّر</p></div>
                        <span class="text-xs font-bold text-ink">{{ egp($o->contracts_value) }}</span></div>
                @endforeach
            </div>
        </section>
        <section class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700"><h3 class="font-bold text-ink text-sm">الأكثر مشاهدة</h3><p class="text-xs text-slate-500">إجمالي المشاهدات {{ number_format($a['totals']['views']) }}</p></div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @foreach ($a['topViewed'] as $p)
                    <a href="{{ route('properties.show', $p) }}" class="px-5 py-3 flex items-center gap-3 text-sm hover:bg-slate-50 dark:hover:bg-slate-800"><img src="{{ $p->cover_thumb }}" class="w-11 h-11 rounded-lg object-cover" alt="">
                        <div class="min-w-0 flex-1"><p class="font-semibold text-ink truncate">{{ $p->title }}</p><p class="text-[11px] text-slate-400">{{ $p->governorate }}</p></div>
                        <span class="text-xs font-bold text-ink">{{ number_format($p->views) }}</span></a>
                @endforeach
            </div>
        </section>
        <section class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700"><h3 class="font-bold text-ink text-sm">آخر النشاط</h3><p class="text-xs text-slate-500">كل جديد على المنصة</p></div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700 max-h-[22rem] overflow-auto">
                @foreach ($a['activity'] as $e)
                    <a href="{{ $e['url'] }}" class="px-5 py-3 block text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
                        <div class="flex justify-between gap-2"><span class="text-[11px] font-bold text-brand-dark">{{ $e['type'] }}</span><span class="text-[11px] text-slate-400">{{ $e['at']->diffForHumans() }}</span></div>
                        <p class="font-semibold text-ink truncate">{{ $e['title'] }}</p><p class="text-[11px] text-slate-400 truncate">{{ $e['meta'] }}</p></a>
                @endforeach
            </div>
        </section>
    </div>

    {{-- قوائم التنفيذ --}}
    <div class="grid grid-cols-[minmax(0,1fr)] xl:grid-cols-2 gap-4">
        <section class="card overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700"><h3 class="font-bold text-ink text-sm">اتفاقات بانتظار سداد النسبة</h3><a href="{{ route('admin.requests', ['status' => 'accepted']) }}" class="text-xs font-semibold text-brand-dark">الكل ←</a></div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($a['awaitingPayment'] as $r)
                    <div class="p-4 space-y-2">
                        <div><p class="font-bold text-ink text-sm truncate">{{ $r->property->title }} <span class="text-xs text-slate-400">{{ $r->code }}</span></p><p class="text-xs text-slate-500">إجمالي العقد {{ egp($r->total_value) }}</p></div>
                        <div class="flex flex-wrap gap-2 text-xs">
                            @foreach (['owner' => 'المالك', 'tenant' => 'المستأجر'] as $party => $lbl)
                                @if ($r->{$party.'_commission_paid_at'})<span class="bg-emerald-100 text-emerald-700 font-bold px-3 py-1.5 rounded-lg">✓ {{ $lbl }}: {{ egp($r->{$party.'_commission'}) }}</span>
                                @else<form method="POST" action="{{ route('admin.requests.paid', $r) }}">@csrf<input type="hidden" name="party" value="{{ $party }}"><button class="btn btn-ghost !min-h-[34px] !py-1 !text-xs" onclick="return confirm('تأكيد استلام نسبة {{ $lbl }}؟')">استلمت من {{ $lbl }} {{ egp($r->{$party.'_commission'}) }}</button></form>@endif
                            @endforeach
                        </div>
                    </div>
                @empty <div class="p-8 text-center text-sm text-slate-500">مفيش اتفاقات معلقة.</div> @endforelse
            </div>
        </section>
        <section class="card overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700"><h3 class="font-bold text-ink text-sm">عقارات بانتظار المراجعة</h3><a href="{{ route('admin.properties', ['status' => 'pending']) }}" class="text-xs font-semibold text-brand-dark">الكل ←</a></div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($a['pendingProperties'] as $p)
                    <div class="p-4 flex items-center gap-4">
                        <img src="{{ $p->cover_thumb }}" class="w-14 h-14 rounded-xl object-cover shrink-0" alt="">
                        <div class="min-w-0 flex-1"><a href="{{ route('properties.show', $p) }}" class="font-bold text-ink hover:text-brand-dark truncate block text-sm">{{ $p->title }}</a><p class="text-xs text-slate-500 truncate">{{ $p->owner->name }} · {{ $p->city }} · {{ egp($p->offered_rent) }}</p></div>
                        <form method="POST" action="{{ route('admin.properties.approve', $p) }}">@csrf<button class="btn btn-brand !min-h-[34px] !py-1 !text-xs">نشر</button></form>
                    </div>
                @empty <div class="p-8 text-center text-sm text-slate-500">مفيش عقارات معلقة.</div> @endforelse
            </div>
        </section>
    </div>
</div>
<script type="application/json" id="adminData">{!! json_encode(['charts' => $c], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
@endsection
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js" referrerpolicy="no-referrer"></script>
<script src="{{ asset('js/admin-charts.js') }}"></script>
@endpush
