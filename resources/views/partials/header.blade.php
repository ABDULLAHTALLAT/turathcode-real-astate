@php
    $links = [
        ['home', __('الرئيسية'), request()->routeIs('home')],
        ['properties.index', __('العقارات'), request()->routeIs('properties.*')],
        ['how', __('كيف تعمل المنصة'), request()->routeIs('how')],
        ['about', __('من نحن'), request()->routeIs('about')],
        ['contact', __('تواصل معنا'), request()->routeIs('contact')],
    ];
    $user = auth()->user();
    $cmpCount = count(session('compare', []));
    $pill = 'h-11 px-5 rounded-full border border-white/25 hover:border-brand hover:text-brand inline-flex items-center gap-2 text-[15px] font-semibold transition';
@endphp

<header id="topHeader" class="relative bg-ink text-white z-40">
    <div class="hidden lg:block border-b border-white/10">
        <div class="max-w-screen-2xl mx-auto px-6 lg:px-10 h-11 flex items-center justify-between text-xs text-white/75">
            <div class="flex items-center gap-8">
                <a href="tel:{{ preg_replace('/\s+/', '', config('site.phone')) }}" class="hover:text-brand inline-flex items-center gap-2" dir="ltr"><x-icon name="phone" class="w-4 h-4" />{{ config('site.phone') }}</a>
                <a href="mailto:{{ config('site.email') }}" class="hover:text-brand inline-flex items-center gap-2"><x-icon name="mail" class="w-4 h-4" />{{ config('site.email') }}</a>
            </div>
            <span class="text-white/55">{{ __('منصة لإحياء العقارات المعطلة في مصر') }}</span>
        </div>
    </div>

    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-10 h-20 flex items-center justify-between gap-6">
        @include('partials.logo')

        <nav class="hidden lg:flex items-center gap-9 text-[15px] font-semibold" aria-label="{{ __('الرئيسية') }}">
            @foreach ($links as [$route, $label, $active])
                <a href="{{ route($route) }}" class="relative py-2 transition {{ $active ? 'text-brand' : 'text-white/90 hover:text-brand' }}" @if($active) aria-current="page" @endif>
                    {{ $label }}
                    @if ($active)<span class="absolute inset-x-0 -bottom-0.5 h-0.5 bg-brand rounded"></span>@endif
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-1.5">
            <a href="{{ route('lang', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" hreflang="{{ app()->getLocale() === 'ar' ? 'en' : 'ar' }}" class="hidden sm:inline-flex items-center gap-1.5 h-10 px-3 rounded-full text-[15px] font-semibold text-white/90 hover:text-brand hover:bg-white/10 transition" aria-label="{{ app()->getLocale() === 'ar' ? 'Switch to English' : 'التبديل للعربية' }}">{{ app()->getLocale() === 'ar' ? 'English' : 'عربي' }}<x-icon name="globe" class="w-[18px] h-[18px]" stroke="1.6" /></a>
            <button type="button" data-theme-toggle class="nb" aria-label="{{ __('تبديل الوضع الليلي') }}" title="{{ __('تبديل الوضع الليلي') }}"><span class="nb-sun"><x-icon name="sun" class="w-[21px] h-[21px]" stroke="1.6" /></span><span class="nb-moon"><x-icon name="moon" class="w-[21px] h-[21px]" stroke="1.6" /></span></button>
            <a href="{{ $user && $user->isTenant() ? route('favorites') : route('login') }}" class="nb hidden sm:inline-flex" aria-label="{{ __('المفضلة') }}" title="{{ __('المفضلة') }}"><x-icon name="heart" class="w-[21px] h-[21px]" stroke="1.6" /></a>
            <a href="{{ route('compare') }}" class="nb" aria-label="{{ __('المقارنة') }}" title="{{ __('المقارنة') }}"><x-icon name="compare" class="w-[21px] h-[21px]" stroke="1.6" />@if ($cmpCount)<span class="nb-badge">{{ $cmpCount }}</span>@endif</a>
            @auth
                <a href="{{ route('dashboard') }}" class="hidden sm:flex items-center gap-2 ms-1 ps-1 pe-4 h-10 rounded-full bg-white/10 hover:bg-white/20 transition" title="{{ __('لوحتي') }}">
                    <img src="{{ $user->avatar_url }}" class="w-8 h-8 rounded-full object-cover" alt="">
                    <span class="text-sm font-semibold max-w-[110px] truncate">{{ $user->name }}</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">@csrf
                    <button class="nb hover:!text-rose-300" aria-label="{{ __('خروج') }}" title="{{ __('خروج') }}"><x-icon name="log-out" class="w-[21px] h-[21px]" stroke="1.6" /></button>
                </form>
            @else
                <a href="{{ route('login') }}" class="nb hidden sm:inline-flex" aria-label="{{ __('دخول') }}" title="{{ __('دخول') }}"><x-icon name="user" class="w-[21px] h-[21px]" stroke="1.6" /></a>
                <a href="{{ route('register') }}" class="hidden sm:inline-flex btn btn-brand !h-10 !min-h-0 !px-5 ms-1">{{ __('سجّل مجاناً') }}</a>
            @endauth
            <button type="button" data-menu-toggle aria-expanded="false" aria-controls="mobileDrawer" class="lg:hidden h-11 px-4 rounded-full border border-white/30 text-brand text-sm font-semibold inline-flex items-center gap-2" aria-label="{{ __('القائمة') }}"><x-icon name="menu" class="w-5 h-5" stroke="2" />{{ __('القائمة') }}</button>
        </div>
    </div>
</header>

{{-- شريط ثابت يظهر عند النزول --}}
<header id="stickyHeader" aria-hidden="true" class="fixed top-0 inset-x-0 z-50 bg-ink/90 backdrop-blur-md text-white border-b border-brand/40 shadow-lg shadow-black/30 -translate-y-full transition-transform duration-300">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-10 h-16 flex items-center justify-between gap-4">
        @include('partials.logo', ['size' => 'h-10'])
        <nav class="hidden lg:flex items-center gap-8 text-sm font-medium">
            @foreach ($links as [$route, $label, $active])
                <a href="{{ route($route) }}" tabindex="-1" class="{{ $active ? 'text-brand' : 'text-white/90 hover:text-brand' }}">{{ $label }}</a>
            @endforeach
        </nav>
        @auth
            <a href="{{ route('dashboard') }}" tabindex="-1" class="btn btn-brand !py-1.5 !text-sm">{{ __('لوحتي') }}</a>
        @else
            <a href="{{ route('register') }}" tabindex="-1" class="btn btn-brand !py-1.5 !text-sm">{{ __('سجّل مجاناً') }}</a>
        @endauth
    </div>
</header>

{{-- القائمة الجانبية للموبايل --}}
<div data-drawer-overlay class="fixed inset-0 z-[90] bg-black/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden"></div>
<aside data-drawer aria-hidden="true" class="fixed inset-y-0 end-0 z-[100] w-[86%] max-w-sm bg-ink text-white flex flex-col shadow-2xl border-s border-brand/30 translate-x-full rtl:-translate-x-full invisible transition-transform duration-300 lg:hidden">
    <div class="flex items-center justify-between px-6 pt-6 pb-5 border-b border-white/10">
        @include('partials.logo', ['size' => 'h-10'])
        <button type="button" data-drawer-close class="h-10 px-4 rounded-full border border-white/25 hover:text-brand text-sm inline-flex items-center gap-2" aria-label="{{ __('إغلاق') }}"><x-icon name="x" class="w-4 h-4" stroke="2.2" />{{ __('إغلاق') }}</button>
    </div>
    <nav class="flex-1 overflow-y-auto px-6 py-4">
        <ul class="divide-y divide-white/10">
            @foreach ($links as [$route, $label, $active])
                <li><a href="{{ route($route) }}" class="flex items-center justify-between py-4 text-lg font-semibold {{ $active ? 'text-brand' : 'text-white/90 hover:text-brand' }}">{{ $label }}</a></li>
            @endforeach
            <li><a href="{{ route('lang', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" class="flex items-center gap-3 py-4 text-lg font-semibold text-brand"><x-icon name="globe" class="w-5 h-5" />{{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}</a></li>
            <li><a href="{{ route('compare') }}" class="flex items-center gap-3 py-4 text-lg font-semibold text-white/90 hover:text-brand"><x-icon name="scale" class="w-5 h-5" />{{ __('المقارنة') }}</a></li>
            @auth
                <li><a href="{{ route('dashboard') }}" class="block py-4 text-lg font-semibold text-brand">{{ __('لوحة التحكم') }}</a></li>
            @endauth
        </ul>
    </nav>
    <div class="px-6 pt-5 pb-7 border-t border-brand/30 bg-black/30 flex gap-3">
        @auth
            <form method="POST" action="{{ route('logout') }}" class="w-full">@csrf<button class="btn btn-ghost !bg-transparent !text-white !border-white/30 w-full">{{ __('تسجيل الخروج') }}</button></form>
        @else
            <a href="{{ route('login') }}" class="btn btn-ghost !bg-transparent !text-white !border-white/30 flex-1">{{ __('دخول') }}</a>
            <a href="{{ route('register') }}" class="btn btn-brand flex-1">{{ __('سجّل مجاناً') }}</a>
        @endauth
    </div>
</aside>
