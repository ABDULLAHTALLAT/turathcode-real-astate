{{-- شريط السعر: توزيع العقارات + علامة المتوسط + مقبضين. $price (min,max,avg,bins,step) و $from/$to القيم الحالية --}}
@php
    $from = (int) ($from ?? 0) ?: $price['min'];
    $to = (int) ($to ?? 0) ?: $price['max'];
    $peak = max(1, max($price['bins']));
    $pop = !empty($pop);
    $avgPct = ($price['avg'] - $price['min']) / max(1, $price['max'] - $price['min']) * 100;
@endphp
<div data-price-range data-id="{{ $id ?? '' }}" data-min="{{ $price['min'] }}" data-max="{{ $price['max'] }}" data-avg="{{ $price['avg'] }}" data-step="{{ $price['step'] }}" data-unit="{{ __('ج.م') }}" class="pr">
    @if ($pop)
    <div class="grid grid-cols-2 gap-2 mb-4">
        <input type="number" data-pr-num-lo class="field !min-h-[42px] !py-2 text-center" aria-label="{{ __('أقل إيجار') }}" inputmode="numeric">
        <input type="number" data-pr-num-hi class="field !min-h-[42px] !py-2 text-center" aria-label="{{ __('أعلى إيجار') }}" inputmode="numeric">
    </div>
    @else
    <div class="flex items-end justify-between gap-3 mb-2">
        <label class="label !mb-0">{{ $label ?? __('الإيجار الشهري') }}</label>
        <p class="text-sm font-bold text-ink" dir="ltr"><span data-pr-from>{{ number_format($from) }}</span> <span class="text-slate-400 font-normal">—</span> <span data-pr-to>{{ number_format($to) }}</span> <span class="text-xs font-semibold text-slate-500">{{ __('ج.م') }}</span></p>
    </div>
    @endif
    <div dir="ltr" class="relative">
        <div class="pr-bins" aria-hidden="true">
            @foreach ($price['bins'] as $b)<i style="height: {{ max(6, round($b / $peak * 100)) }}%"></i>@endforeach
        </div>
        <div class="pr-track"><div class="pr-fill" data-pr-fill></div>
            <span class="pr-avg" style="left: {{ round($avgPct, 1) }}%" aria-hidden="true"></span></div>
        <input type="range" class="pr-in" data-pr-lo min="{{ $price['min'] }}" max="{{ $price['max'] }}" step="{{ $price['step'] }}" value="{{ $from }}" aria-label="{{ __('أقل إيجار') }}">
        <input type="range" class="pr-in" data-pr-hi min="{{ $price['min'] }}" max="{{ $price['max'] }}" step="{{ $price['step'] }}" value="{{ $to }}" aria-label="{{ __('أعلى إيجار') }}">
    </div>
    <div class="relative h-5 mt-1.5" dir="ltr" aria-hidden="true">
        <span class="absolute -translate-x-1/2 text-[11px] font-semibold text-brand-dark whitespace-nowrap" style="left: clamp(12%, {{ round($avgPct, 1) }}%, 88%)">{{ __('متوسط') }} {{ number_format($price['avg']) }}</span>
    </div>
    <input type="hidden" name="min_price" value="{{ $from > $price['min'] ? $from : '' }}" data-pr-min-out>
    <input type="hidden" name="max_price" value="{{ $to < $price['max'] ? $to : '' }}" data-pr-max-out>
</div>
