<div class="card p-5">
    <p class="text-sm text-slate-500">{{ $label }}</p>
    <p class="text-2xl font-extrabold text-ink mt-2">{{ $value }}</p>
    @isset($hint)<p class="text-[11px] text-slate-400 mt-0.5">{{ $hint }}</p>@endisset
</div>
