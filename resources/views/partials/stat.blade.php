<div class="card p-5">
    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500">{{ $label }}</p>
        <span class="w-9 h-9 rounded-lg bg-brand-50 text-brand-dark flex items-center justify-center"><i class="bi {{ $icon ?? 'bi-bar-chart' }}"></i></span>
    </div>
    <p class="text-2xl font-extrabold text-ink mt-2">{{ $value }}</p>
    @isset($hint)<p class="text-[11px] text-slate-400 mt-0.5">{{ $hint }}</p>@endisset
</div>
