@extends('layouts.app')
@section('title', 'العقارات المعروضة')

@section('content')
<section class="bg-ink text-white"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-3xl font-extrabold">العقارات المعروضة</h1>
    <p class="text-white/70 mt-2">{{ $properties->total() }} عقار متاح للإيجار طويل المدة</p>
</div></section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid lg:grid-cols-4 gap-8">
    <form method="GET" class="lg:col-span-1 card p-5 h-fit lg:sticky lg:top-20 space-y-4" aria-label="فلترة العقارات">
        <h2 class="font-bold text-ink flex items-center gap-2"><i class="bi bi-funnel"></i> فلترة النتائج</h2>
        <div><label class="label">بحث</label><input name="q" value="{{ $filters['q'] ?? '' }}" class="field" placeholder="منطقة أو اسم"></div>
        <div><label class="label">المحافظة</label>
            <select name="governorate" class="field"><option value="">الكل</option>@foreach (catalog('governorates') as $g)<option @selected(($filters['governorate'] ?? '') === $g)>{{ $g }}</option>@endforeach</select></div>
        <div><label class="label">نوع العقار</label>
            <select name="type" class="field"><option value="">الكل</option>@foreach (catalog('property_types') as $k => $v)<option value="{{ $k }}" @selected(($filters['type'] ?? '') === $k)>{{ $v }}</option>@endforeach</select></div>
        <div><label class="label">مرحلة التشطيب</label>
            <select name="stage" class="field"><option value="">الكل</option>@foreach (catalog('finishing_stages') as $k => $v)<option value="{{ $k }}" @selected(($filters['stage'] ?? '') === $k)>{{ $v['label'] }}</option>@endforeach</select></div>
        <div><label class="label">الغرض</label>
            <select name="purpose" class="field"><option value="">الكل</option>@foreach (catalog('purposes') as $k => $v)<option value="{{ $k }}" @selected(($filters['purpose'] ?? '') === $k)>{{ $v }}</option>@endforeach</select></div>
        <div class="grid grid-cols-2 gap-2">
            <div><label class="label">إيجار من</label><input type="number" name="min_price" min="0" step="500" inputmode="numeric" value="{{ $filters['min_price'] ?? '' }}" class="field"></div>
            <div><label class="label">إلى</label><input type="number" name="max_price" value="{{ $filters['max_price'] ?? '' }}" class="field"></div>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <div><label class="label">مساحة من (م²)</label><input type="number" name="min_area" value="{{ $filters['min_area'] ?? '' }}" class="field"></div>
            <div><label class="label">مدة (سنين)</label><input type="number" name="years" value="{{ $filters['years'] ?? '' }}" class="field"></div>
        </div>
        <div><label class="label">الترتيب</label>
            <select name="sort" class="field">
                @foreach (['latest' => 'الأحدث', 'price_asc' => 'الأرخص', 'price_desc' => 'الأعلى سعراً', 'area' => 'الأكبر مساحة'] as $k => $v)<option value="{{ $k }}" @selected(($filters['sort'] ?? 'latest') === $k)>{{ $v }}</option>@endforeach
            </select></div>
        <div class="flex gap-2"><button class="btn btn-primary flex-1">تطبيق</button><a href="{{ route('properties.index') }}" class="btn btn-ghost">مسح</a></div>
    </form>

    <div class="lg:col-span-3">
        @if ($properties->count())
            <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ($properties as $property) @include('partials.property-card') @endforeach
            </div>
            <div class="mt-8">{{ $properties->links() }}</div>
        @else
            <div class="card p-12 text-center">
                <i class="bi bi-house-slash text-5xl text-slate-300"></i>
                <p class="font-bold text-ink mt-4">مفيش عقارات بالمواصفات دي حالياً</p>
                <p class="text-sm text-slate-500 mt-1">جرّب تغيّر الفلاتر أو ارجع تاني قريب.</p>
            </div>
        @endif
    </div>
</div>
@endsection
