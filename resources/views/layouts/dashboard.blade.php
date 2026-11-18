@extends('layouts.app')
@php
    $u = auth()->user();
    $nav = match ($u->role) {
        'admin' => [
            ['dashboard', 'نظرة عامة', 'bi-speedometer2', 'dashboard'],
            ['admin.properties', 'العقارات والمراجعة', 'bi-buildings', 'admin.properties*'],
            ['admin.requests', 'الطلبات والعمولات', 'bi-cash-coin', 'admin.requests*'],
            ['admin.users', 'المستخدمين', 'bi-people', 'admin.users*'],
            ['admin.messages', 'رسائل التواصل', 'bi-envelope', 'admin.messages*'],
            ['admin.settings', 'إعدادات النسبة', 'bi-percent', 'admin.settings*'],
        ],
        'owner' => [
            ['dashboard', 'لوحتي', 'bi-speedometer2', 'dashboard'],
            ['owner.properties.index', 'عقاراتي', 'bi-buildings', 'owner.properties.index'],
            ['owner.properties.create', 'إضافة عقار', 'bi-building-add', 'owner.properties.create'],
            ['requests.index', 'الطلبات الواردة', 'bi-inbox', 'requests.*'],
            ['profile.edit', 'ملفي الشخصي', 'bi-person-gear', 'profile.*'],
        ],
        default => [
            ['dashboard', 'لوحتي', 'bi-speedometer2', 'dashboard'],
            ['requests.index', 'طلباتي', 'bi-inbox', 'requests.*'],
            ['favorites', 'المفضلة', 'bi-heart', 'favorites'],
            ['compare', 'المقارنة', 'bi-arrow-left-right', 'compare'],
            ['profile.edit', 'ملفي الشخصي', 'bi-person-gear', 'profile.*'],
        ],
    };
@endphp

@section('content')
<div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 grid lg:grid-cols-4 gap-8">
    <aside class="lg:col-span-1">
        <div class="card p-5 lg:sticky lg:top-20">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-700">
                <img src="{{ $u->avatar_url }}" class="w-12 h-12 rounded-full object-cover" alt="">
                <div class="min-w-0"><p class="font-bold text-ink truncate">{{ $u->name }}</p><span class="text-[11px] bg-brand-50 text-brand-dark font-bold px-2 py-0.5 rounded-full">{{ $u->role_label }}</span></div>
            </div>
            <nav class="mt-3 flex lg:flex-col gap-1 overflow-x-auto text-sm">
                @foreach ($nav as [$route, $label, $icon, $pattern])
                    <a href="{{ route($route) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl whitespace-nowrap transition {{ request()->routeIs($pattern) ? 'bg-ink text-white font-semibold dark:bg-brand dark:text-ink' : 'text-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        {{ $label }}
                    </a>
                @endforeach
                <form method="POST" action="{{ route('logout') }}" class="lg:mt-2 lg:pt-2 lg:border-t border-slate-100 dark:border-slate-700">@csrf
                    <button class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 whitespace-nowrap">خروج</button>
                </form>
            </nav>
        </div>
    </aside>
    <section class="lg:col-span-3 min-w-0 space-y-6">
        @yield('panel')
    </section>
</div>
@endsection
