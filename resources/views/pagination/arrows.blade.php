@if ($paginator->hasPages())
<nav role="navigation" aria-label="{{ __('التنقل بين الصفحات') }}" class="flex items-center justify-center gap-4">
    @php $btn = 'pg-arrow'; @endphp
    {{-- السابق: السهم ناحية بداية السطر (يمين في العربي) --}}
    @if ($paginator->onFirstPage())
        <span class="{{ $btn }} is-off" aria-disabled="true" aria-label="{{ __('السابق') }}"><x-icon name="arrow-right" class="w-5 h-5 rtl-keep" stroke="2.2" /></span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $btn }}" aria-label="{{ __('السابق') }}" title="{{ __('السابق') }}"><x-icon name="arrow-right" class="w-5 h-5 rtl-keep" stroke="2.2" /></a>
    @endif

    <p class="text-sm font-semibold text-slate-500 min-w-[5rem] text-center" dir="ltr"><span class="text-ink">{{ $paginator->currentPage() }}</span> / {{ $paginator->lastPage() }}</p>

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $btn }}" aria-label="{{ __('التالي') }}" title="{{ __('التالي') }}"><x-icon name="arrow-left" class="w-5 h-5 rtl-keep" stroke="2.2" /></a>
    @else
        <span class="{{ $btn }} is-off" aria-disabled="true" aria-label="{{ __('التالي') }}"><x-icon name="arrow-left" class="w-5 h-5 rtl-keep" stroke="2.2" /></span>
    @endif
</nav>
@endif
