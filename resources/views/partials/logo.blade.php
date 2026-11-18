{{-- لوجو Turath Code (أبيض + أزرق، مناسب للخلفية الداكنة) --}}
<a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0" aria-label="{{ config('site.name') }}">
    <img src="{{ asset('images/logo.png') }}" alt="Turath Code" class="{{ $size ?? 'h-14' }} w-auto">
    <span class="hidden sm:flex flex-col leading-tight border-s border-white/20 ps-3">
        <span class="text-white font-extrabold text-base">{{ __('بيتك في ايدك') }}</span>
        <span class="text-brand text-[11px] tracking-wide">{{ __('اعرض • استأجر • اتفق') }}</span>
    </span>
</a>
