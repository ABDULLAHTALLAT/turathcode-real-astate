@extends('layouts.dashboard')
@section('title', 'لوحة الأدمن')
@section('panel')
<div><h1 class="text-xl font-extrabold text-ink">لوحة إدارة المنصة</h1><p class="text-sm text-slate-500">أرباحك من نسبة المنصة وكل ما يحتاج مراجعة.</p></div>

<div class="grid sm:grid-cols-3 gap-4">
    <div class="rounded-2xl hero-bg text-white p-5"><p class="text-xs text-white/60">أرباح محصّلة</p><p class="text-2xl font-extrabold text-brand mt-2">{{ egp($earnings['collected']) }}</p></div>
    <div class="card p-5"><p class="text-xs text-slate-500">أرباح مستحقة (في انتظار السداد)</p><p class="text-2xl font-extrabold text-ink mt-2">{{ egp($earnings['expected']) }}</p></div>
    <div class="card p-5"><p class="text-xs text-slate-500">قيمة العقود المكتملة</p><p class="text-2xl font-extrabold text-ink mt-2">{{ egp($earnings['contracts']) }}</p></div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @include('partials.stat', ['label' => 'المستخدمين', 'value' => $counts['users'], 'icon' => 'bi-people', 'hint' => $counts['owners'].' مالك · '.$counts['tenants'].' مستأجر'])
    @include('partials.stat', ['label' => 'عقارات منشورة', 'value' => $counts['published'], 'icon' => 'bi-buildings', 'hint' => $counts['rented'].' تم تأجيره'])
    @include('partials.stat', ['label' => 'بانتظار المراجعة', 'value' => $counts['pendingProps'], 'icon' => 'bi-hourglass-split'])
    @include('partials.stat', ['label' => 'طلبات مفتوحة', 'value' => $counts['openRequests'], 'icon' => 'bi-chat-left-dots', 'hint' => $counts['awaitingPay'].' بانتظار سداد النسبة'])
</div>

<section class="card overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700"><h2 class="font-bold text-ink">اتفاقات بانتظار سداد النسبة</h2><a href="{{ route('admin.requests', ['status' => 'accepted']) }}" class="text-xs font-semibold text-brand-dark">الكل ←</a></div>
    <div class="divide-y divide-slate-100">
        @forelse ($awaitingPayment as $r)
            <div class="p-4 flex flex-wrap items-center gap-3 justify-between">
                <div class="min-w-0"><p class="font-bold text-ink truncate">{{ $r->property->title }} <span class="text-xs text-slate-400">{{ $r->code }}</span></p>
                    <p class="text-xs text-slate-500 mt-0.5">إجمالي العقد {{ egp($r->total_value) }}</p></div>
                <div class="flex gap-2 text-xs">
                    @foreach (['owner' => 'المالك', 'tenant' => 'المستأجر'] as $party => $lbl)
                        @if ($r->{$party.'_commission_paid_at'})<span class="bg-emerald-100 text-emerald-700 font-bold px-3 py-1.5 rounded-lg">✓ {{ $lbl }}: {{ egp($r->{$party.'_commission'}) }}</span>
                        @else<form method="POST" action="{{ route('admin.requests.paid', $r) }}">@csrf<input type="hidden" name="party" value="{{ $party }}"><button class="btn btn-ghost !py-1.5 !text-xs" onclick="return confirm('تأكيد استلام نسبة {{ $lbl }}؟')">استلمت من {{ $lbl }} {{ egp($r->{$party.'_commission'}) }}</button></form>@endif
                    @endforeach
                </div>
            </div>
        @empty <div class="p-8 text-center text-sm text-slate-500">مفيش اتفاقات معلقة.</div> @endforelse
    </div>
</section>

<section class="card overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700"><h2 class="font-bold text-ink">عقارات بانتظار المراجعة</h2><a href="{{ route('admin.properties', ['status' => 'pending']) }}" class="text-xs font-semibold text-brand-dark">الكل ←</a></div>
    <div class="divide-y divide-slate-100">
        @forelse ($pendingProperties as $p)
            <div class="p-4 flex items-center gap-4">
                <img src="{{ $p->cover_url }}" class="w-16 h-16 rounded-xl object-cover shrink-0" alt="">
                <div class="min-w-0 flex-1"><a href="{{ route('properties.show', $p) }}" class="font-bold text-ink hover:text-brand-dark truncate block">{{ $p->title }}</a><p class="text-xs text-slate-500">{{ $p->owner->name }} · {{ $p->city }} · {{ egp($p->offered_rent) }}</p></div>
                <form method="POST" action="{{ route('admin.properties.approve', $p) }}">@csrf<button class="btn btn-brand !py-1.5 !text-xs">نشر</button></form>
            </div>
        @empty <div class="p-8 text-center text-sm text-slate-500">مفيش عقارات معلقة.</div> @endforelse
    </div>
</section>

<section class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between"><h2 class="font-bold text-ink">أحدث المستخدمين</h2><a href="{{ route('admin.users') }}" class="text-xs font-semibold text-brand-dark">الكل ←</a></div>
    <div class="divide-y divide-slate-100">
        @foreach ($latestUsers as $x)
            <div class="px-5 py-3 flex items-center gap-3 text-sm"><img src="{{ $x->avatar_url }}" class="w-9 h-9 rounded-full object-cover" alt=""><span class="font-semibold text-ink">{{ $x->name }}</span><span class="text-xs text-slate-400">{{ $x->role_label }}</span><span class="ms-auto text-xs text-slate-400">{{ $x->created_at->diffForHumans() }}</span></div>
        @endforeach
    </div>
</section>
@endsection
