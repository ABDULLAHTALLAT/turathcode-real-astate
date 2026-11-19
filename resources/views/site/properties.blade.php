@extends('layouts.app')
@section('title', __('العقارات المعروضة'))

@section('content')
<section class="bg-ink text-white"><div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-3xl font-extrabold">{{ __('العقارات المعروضة') }}</h1>
    <p class="text-white/70 mt-2">{{ __(':n عقار متاح للإيجار طويل المدة', ['n' => $properties->total()]) }}</p>
</div></section>

<div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid lg:grid-cols-4 gap-8">
    <form method="GET" data-live-count="{{ route('properties.count') }}" class="lg:col-span-1 card p-5 h-fit lg:sticky lg:top-20 space-y-4" aria-label="{{ __('فلترة العقارات') }}">
        <h2 class="font-bold text-ink flex items-center gap-2">{{ __('فلترة النتائج') }}</h2>
        <div><label class="label">{{ __('بحث') }}</label><input name="q" value="{{ $filters['q'] ?? '' }}" class="field" placeholder="{{ __('منطقة أو اسم') }}"></div>
        <div><label class="label">{{ __('المحافظة') }}</label>
            <select name="governorate" class="field"><option value="">{{ __('الكل') }}</option>@foreach (catalog('governorates') as $g)<option value="{{ $g }}" @selected(($filters['governorate'] ?? '') === $g)>{{ __($g) }}</option>@endforeach</select></div>
        <div><label class="label">{{ __('نوع العقار') }}</label>
            <select name="type" class="field"><option value="">{{ __('الكل') }}</option>@foreach (catalog('property_types') as $k => $v)<option value="{{ $k }}" @selected(($filters['type'] ?? '') === $k)>{{ $v }}</option>@endforeach</select></div>
        <div><label class="label">{{ __('مرحلة التشطيب') }}</label>
            <select name="stage" class="field"><option value="">{{ __('الكل') }}</option>@foreach (catalog('finishing_stages') as $k => $v)<option value="{{ $k }}" @selected(($filters['stage'] ?? '') === $k)>{{ $v['label'] }}</option>@endforeach</select></div>
        <div><label class="label">{{ __('الغرض') }}</label>
            <select name="purpose" class="field"><option value="">{{ __('الكل') }}</option>@foreach (catalog('purposes') as $k => $v)<option value="{{ $k }}" @selected(($filters['purpose'] ?? '') === $k)>{{ $v }}</option>@endforeach</select></div>
        @include('partials.price-range', ['price' => $price, 'from' => $filters['min_price'] ?? 0, 'to' => $filters['max_price'] ?? 0])
        <div class="grid grid-cols-2 gap-2">
            <div><label class="label">{{ __('مساحة من (م²)') }}</label><input type="number" name="min_area" value="{{ $filters['min_area'] ?? '' }}" class="field"></div>
            <div><label class="label">{{ __('مدة (سنين)') }}</label><input type="number" name="years" value="{{ $filters['years'] ?? '' }}" class="field"></div>
        </div>
        <div><label class="label">{{ __('الترتيب') }}</label>
            <select name="sort" class="field">
                @foreach (['latest' => __('الأحدث'), 'price_asc' => __('الأرخص'), 'price_desc' => __('الأعلى سعراً'), 'area' => __('الأكبر مساحة')] as $k => $v)<option value="{{ $k }}" @selected(($filters['sort'] ?? 'latest') === $k)>{{ $v }}</option>@endforeach
            </select></div>
        <div class="flex gap-2"><button class="sb-go !min-h-[48px] !px-4 !text-sm flex-1"><span>{{ __('عرض') }}</span><b data-count-out>{{ $properties->total() }}</b><span>{{ __('عقار') }}</span></button><a href="{{ route('properties.index') }}" class="btn btn-ghost">{{ __('مسح') }}</a></div>
    </form>

    <div class="lg:col-span-3">
        <div class="flex flex-wrap gap-2 mb-5">@foreach ($govQuick as $g => $n)<a href="{{ route('properties.index', array_merge(collect($filters)->except('page')->all(), ['governorate' => $g])) }}" class="sb-chip {{ ($filters['governorate'] ?? '') === $g ? 'is-on' : '' }}">{{ __($g) }} <span class="opacity-60">{{ $n }}</span></a>@endforeach</div>
        @php
            $labels = ['q' => __('بحث'), 'governorate' => __('محافظة'), 'type' => __('النوع'), 'stage' => __('التشطيب'), 'purpose' => __('الغرض'), 'min_price' => __('إيجار من'), 'max_price' => __('إيجار حتى'), 'min_area' => __('مساحة من'), 'years' => __('سنين')];
            $active = collect($filters)->only(array_keys($labels))->filter(fn ($v) => $v !== null && $v !== '');
        @endphp
        @if ($active->isNotEmpty())
            <div class="flex flex-wrap items-center gap-2 mb-5" aria-label="{{ __('الفلاتر المفعّلة') }}">
                @foreach ($active as $k => $v)
                    <a href="{{ route('properties.index', collect($filters)->except($k)->all()) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold bg-brand-50 text-brand-dark border border-brand/30 rounded-full ps-3 pe-2 py-1.5 hover:bg-brand-100" title="{{ __('إزالة الفلتر') }}">
                        {{ $labels[$k] }}: {{ catalog('property_types')[$v] ?? catalog('purposes')[$v] ?? (catalog('finishing_stages')[$v]['label'] ?? $v) }} 
                    </a>
                @endforeach
                <a href="{{ route('properties.index') }}" class="text-xs text-slate-500 underline">{{ __('مسح الكل') }}</a>
            </div>
        @endif
        @if ($properties->count())
            <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ($properties as $property) @include('partials.property-card') @endforeach
            </div>
            <p class="mt-8 text-center text-xs text-slate-500">{{ __('عرض :from إلى :to من :total', ['from' => $properties->firstItem(), 'to' => $properties->lastItem(), 'total' => $properties->total()]) }} عقار</p>
            <div class="mt-3">{{ $properties->links() }}</div>
        @else
            <div class="card p-12 text-center">
                
                <p class="font-bold text-ink mt-4">{{ __('مفيش عقارات بالمواصفات دي حالياً') }}</p>
                <p class="text-sm text-slate-500 mt-1">{{ __('جرّب تغيّر الفلاتر أو ارجع تاني قريب.') }}</p>
                <a href="{{ route('properties.index') }}" class="btn btn-brand mt-5">{{ __('اعرض كل العقارات') }}</a>
            </div>
        @endif
    </div>
</div>
@endsection
