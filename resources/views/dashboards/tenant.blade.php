@extends('layouts.dashboard')
@section('title', 'لوحة المستأجر')
@section('panel')
@php($fields = [$user->phone, $user->email, $user->avatar, $user->governorate, $profile?->marital_status, $profile?->purpose, $profile?->budget_max, $profile?->job])
@php($pct = (int) round(count(array_filter($fields)) / count($fields) * 100))

<div class="card p-6 flex flex-col sm:flex-row gap-5 items-start sm:items-center">
    <img src="{{ $user->avatar_url }}" class="w-20 h-20 rounded-2xl object-cover" alt="">
    <div class="flex-1 min-w-0">
        <h1 class="text-xl font-extrabold text-ink">أهلاً {{ \Illuminate\Support\Str::before($user->name, ' ') }} 👋</h1>
        <div class="flex flex-wrap gap-x-5 gap-y-1 text-sm text-slate-500 mt-1.5">
            <span dir="ltr"><i class="bi bi-telephone text-brand-dark"></i> {{ $user->phone }}</span>
            <span><i class="bi bi-envelope text-brand-dark"></i> {{ $user->email }}</span>
            @if ($profile?->marital_status)<span><i class="bi bi-people text-brand-dark"></i> {{ catalog('marital_statuses')[$profile->marital_status] }}</span>@endif
            @if ($profile?->purpose)<span><i class="bi bi-bullseye text-brand-dark"></i> {{ catalog('purposes')[$profile->purpose] }}</span>@endif
        </div>
        <div class="mt-3 max-w-sm"><div class="flex justify-between text-[11px] text-slate-500 mb-1"><span>اكتمال الملف الشخصي</span><span>{{ $pct }}%</span></div>
            <div class="h-2 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden"><span class="block h-full brand-gradient" style="width: {{ $pct }}%"></span></div></div>
    </div>
    <a href="{{ route('profile.edit') }}" class="btn btn-ghost"><i class="bi bi-pencil"></i> تعديل</a>
</div>

@if ($waiting)
    <div class="rounded-2xl bg-violet-50 border border-violet-200 text-violet-800 p-4 text-sm flex items-center gap-3 dark:bg-violet-950/30 dark:border-violet-900 dark:text-violet-200"><i class="bi bi-bell-fill"></i> عندك {{ $waiting }} عرض مضاد من المالك في انتظار ردك. <a href="{{ route('requests.index') }}" class="font-bold underline">راجعها</a></div>
@endif

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @include('partials.stat', ['label' => 'كل طلباتي', 'value' => $counts['total'], 'icon' => 'bi-inbox'])
    @include('partials.stat', ['label' => 'قيد التفاوض', 'value' => $counts['open'], 'icon' => 'bi-chat-left-dots'])
    @include('partials.stat', ['label' => 'اتفاقات', 'value' => $counts['accepted'], 'icon' => 'bi-patch-check'])
    @include('partials.stat', ['label' => 'المفضلة', 'value' => $counts['favorites'], 'icon' => 'bi-heart'])
</div>

<section class="card overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700"><h2 class="font-bold text-ink">آخر طلباتي</h2><a href="{{ route('requests.index') }}" class="text-xs font-semibold text-brand-dark">الكل ←</a></div>
    <div class="divide-y divide-slate-100">
        @forelse ($recent as $r) @include('partials.request-row', ['as' => 'tenant']) @empty
            <div class="p-10 text-center text-sm text-slate-500">لسه ما قدمتش على أي عقار. <a href="{{ route('properties.index') }}" class="text-brand-dark font-bold">ابدأ البحث</a></div>
        @endforelse
    </div>
</section>

@if ($favorites->count())
<section>
    <div class="flex items-center justify-between mb-4"><h2 class="font-bold text-ink">من مفضلتي</h2><a href="{{ route('favorites') }}" class="text-xs font-semibold text-brand-dark">الكل ←</a></div>
    <div class="grid sm:grid-cols-2 gap-5">@foreach ($favorites->take(2) as $property) @include('partials.property-card') @endforeach</div>
</section>
@endif
@endsection
