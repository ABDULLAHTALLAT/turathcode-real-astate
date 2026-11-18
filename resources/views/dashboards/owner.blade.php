@extends('layouts.dashboard')
@section('title', 'لوحة المالك')
@section('panel')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div><h1 class="text-xl font-extrabold text-ink">أهلاً {{ \Illuminate\Support\Str::before($user->name, ' ') }} 👋</h1><p class="text-sm text-slate-500">لوحة تحكم المالك</p></div>
    <a href="{{ route('owner.properties.create') }}" class="btn btn-brand">أضف عقار</a>
</div>

@if ($needReply)
    <div class="rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 p-4 text-sm flex items-center gap-3 dark:bg-amber-950/30 dark:border-amber-900 dark:text-amber-200">عندك {{ $needReply }} طلب مطلوب ردك. <a href="{{ route('requests.index') }}" class="font-bold underline">راجعها دلوقتي</a></div>
@endif

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
    @include('partials.stat', ['label' => 'عقاراتي', 'value' => $counts['properties'], 'icon' => 'bi-buildings', 'hint' => $counts['published'].' منشور · '.$counts['pending'].' قيد المراجعة'])
    @include('partials.stat', ['label' => 'تم تأجيره', 'value' => $counts['rented'], 'icon' => 'bi-key'])
    @include('partials.stat', ['label' => 'مشاهدات عقاراتي', 'value' => number_format($counts['views']), 'icon' => 'bi-eye'])
    @include('partials.stat', ['label' => 'الطلبات الواردة', 'value' => $counts['requests'], 'icon' => 'bi-inbox'])
    @include('partials.stat', ['label' => 'قيمة عقودي المكتملة', 'value' => egp($contractsValue), 'icon' => 'bi-file-earmark-check'])
    @include('partials.stat', ['label' => 'نسبة المنصة (اتفاقاتك)', 'value' => egp($platformFees), 'icon' => 'bi-percent'])
</div>

<section class="card overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700"><h2 class="font-bold text-ink">أحدث الطلبات</h2><a href="{{ route('requests.index') }}" class="text-xs font-semibold text-brand-dark">الكل ←</a></div>
    <div class="divide-y divide-slate-100">
        @forelse ($recent as $r) @include('partials.request-row', ['as' => 'owner']) @empty
            <div class="p-10 text-center text-sm text-slate-500">لسه مفيش طلبات على عقاراتك.</div>
        @endforelse
    </div>
</section>

<section>
    <div class="flex items-center justify-between mb-4"><h2 class="font-bold text-ink">عقاراتي</h2><a href="{{ route('owner.properties.index') }}" class="text-xs font-semibold text-brand-dark">إدارة الكل ←</a></div>
    @if ($myProperties->isEmpty())
        <div class="card p-10 text-center text-sm text-slate-500">ما أضفتش عقارات لسه. <a href="{{ route('owner.properties.create') }}" class="text-brand-dark font-bold">أضف أول عقار</a></div>
    @else
        <div class="grid sm:grid-cols-2 gap-5">@foreach ($myProperties->take(2) as $property) @include('partials.property-card') @endforeach</div>
    @endif
</section>
@endsection
