@php
    $links = [
        ['home', 'الرئيسية', request()->routeIs('home')],
        ['properties.index', 'العقارات', request()->routeIs('properties.*')],
        ['how', 'كيف تعمل المنصة', request()->routeIs('how')],
        ['about', 'من نحن', request()->routeIs('about')],
        ['contact', 'تواصل معنا', request()->routeIs('contact')],
    ];
    $user = auth()->user();
    $cmpCount = count(session('compare', []));
@endphp

<header id="topHeader" class="relative bg-ink text-white z-40">
    <div class="hidden lg:block border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-10 flex items-center justify-between text-xs text-white/80">
            <div class="flex items-center gap-6">
                <a href="tel:{{ preg_replace('/\s+/', '', config('site.phone')) }}" class="hover:text-brand flex items-center gap-2" dir="ltr"><i class="bi bi-telephone-fill text-brand"></i> {{ config('site.phone') }}</a>
                <a href="mailto:{{ config('site.email') }}" class="hover:text-brand flex items-center gap-2"><i class="bi bi-envelope-fill text-brand"></i> {{ config('site.email') }}</a>
            </div>
            <span class="text-white/60">منصة لإحياء العقارات المعطلة في مصر 🇪🇬</span>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
        @include('partials.logo')

        <nav class="hidden lg:flex items-center gap-7 text-[15px] font-medium" aria-label="الرئيسية">
            @foreach ($links as [$route, $label, $active])
                <a href="{{ route($route) }}" class="relative py-2 transition {{ $active ? 'text-brand' : 'text-white/90 hover:text-brand' }}">
                    {{ $label }}
                    @if ($active)<span class="absolute inset-x-0 -bottom-0.5 h-0.5 bg-brand rounded"></span>@endif
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ route('compare') }}" class="relative w-10 h-10 rounded-full border border-white/25 hover:border-brand hover:text-brand flex items-center justify-center transition" title="المقارنة" aria-label="المقارنة">
                <i class="bi bi-arrow-left-right"></i>
                @if ($cmpCount)<span class="absolute -top-1 -end-1 bg-brand text-ink text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">{{ $cmpCount }}</span>@endif
            </a>
            @auth
                @if ($user->isTenant())
                    <a href="{{ route('favorites') }}" class="w-10 h-10 rounded-full border border-white/25 hover:border-brand hover:text-brand flex items-center justify-center transition" title="المفضلة" aria-label="المفضلة"><i class="bi bi-heart"></i></a>
                @endif
                <a href="{{ route('dashboard') }}" class="hidden sm:flex items-center gap-2 ps-1 pe-4 h-10 rounded-full bg-white/10 hover:bg-white/20 transition">
                    <img src="{{ $user->avatar_url }}" class="w-8 h-8 rounded-full object-cover" alt="">
                    <span class="text-sm font-semibold max-w-[110px] truncate">{{ $user->name }}</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">@csrf
                    <button class="w-10 h-10 rounded-full border border-white/25 hover:border-rose-400 hover:text-rose-300 flex items-center justify-center transition" title="تسجيل الخروج" aria-label="تسجيل الخروج"><i class="bi bi-box-arrow-left"></i></button>
                </form>
            @else
                <a href="{{ route('login') }}" class="hidden sm:inline-flex btn btn-ghost !bg-transparent !text-white !border-white/30 hover:!border-brand !py-2">دخول</a>
                <a href="{{ route('register') }}" class="hidden sm:inline-flex btn btn-brand !py-2">سجّل مجاناً</a>
            @endauth
            <button type="button" data-menu-toggle aria-expanded="false" aria-controls="mobileDrawer" class="lg:hidden w-10 h-10 rounded-lg border border-white/30 text-brand flex items-center justify-center" aria-label="القائمة"><i class="bi bi-list text-xl"></i></button>
        </div>
    </div>
</header>

{{-- شريط ثابت يظهر عند النزول --}}
<header id="stickyHeader" aria-hidden="true" class="fixed top-0 inset-x-0 z-50 bg-ink/90 backdrop-blur-md text-white border-b border-brand/40 shadow-lg shadow-black/30 -translate-y-full transition-transform duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between gap-4">
        @include('partials.logo', ['size' => 'h-9'])
        <nav class="hidden lg:flex items-center gap-7 text-sm font-medium">
            @foreach ($links as [$route, $label, $active])
                <a href="{{ route($route) }}" tabindex="-1" class="{{ $active ? 'text-brand' : 'text-white/90 hover:text-brand' }}">{{ $label }}</a>
            @endforeach
        </nav>
        @auth
            <a href="{{ route('dashboard') }}" tabindex="-1" class="btn btn-brand !py-1.5 !text-sm"><i class="bi bi-speedometer2"></i> لوحتي</a>
        @else
            <a href="{{ route('register') }}" tabindex="-1" class="btn btn-brand !py-1.5 !text-sm">سجّل مجاناً</a>
        @endauth
    </div>
</header>

{{-- القائمة الجانبية للموبايل --}}
<div data-drawer-overlay class="fixed inset-0 z-[90] bg-black/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden"></div>
<aside data-drawer aria-hidden="true" class="fixed inset-y-0 end-0 z-[100] w-[86%] max-w-sm bg-ink text-white flex flex-col shadow-2xl border-s border-brand/30 translate-x-full rtl:-translate-x-full invisible transition-transform duration-300 lg:hidden">
    <div class="flex items-center justify-between px-6 pt-6 pb-5 border-b border-white/10">
        @include('partials.logo', ['size' => 'h-10'])
        <button type="button" data-drawer-close class="w-10 h-10 rounded-full border border-white/25 hover:text-brand flex items-center justify-center" aria-label="إغلاق"><i class="bi bi-x-lg"></i></button>
    </div>
    <nav class="flex-1 overflow-y-auto px-6 py-4">
        <ul class="divide-y divide-white/10">
            @foreach ($links as [$route, $label, $active])
                <li><a href="{{ route($route) }}" class="flex items-center justify-between py-4 text-lg font-semibold {{ $active ? 'text-brand' : 'text-white/90 hover:text-brand' }}">{{ $label }} <span class="text-brand/70 rtl:rotate-180">›</span></a></li>
            @endforeach
            @auth
                <li><a href="{{ route('dashboard') }}" class="flex items-center gap-3 py-4 text-lg font-semibold text-brand"><i class="bi bi-speedometer2"></i> لوحة التحكم</a></li>
            @endauth
        </ul>
    </nav>
    <div class="px-6 pt-5 pb-7 border-t border-brand/30 bg-black/30 flex gap-3">
        @auth
            <form method="POST" action="{{ route('logout') }}" class="w-full">@csrf<button class="btn btn-ghost !bg-transparent !text-white !border-white/30 w-full">تسجيل الخروج</button></form>
        @else
            <a href="{{ route('login') }}" class="btn btn-ghost !bg-transparent !text-white !border-white/30 flex-1">دخول</a>
            <a href="{{ route('register') }}" class="btn btn-brand flex-1">سجّل مجاناً</a>
        @endauth
    </div>
</aside>
