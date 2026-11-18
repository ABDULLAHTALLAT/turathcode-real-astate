@extends('layouts.app')
@section('title', $property->title)
@section('description', \Illuminate\Support\Str::limit(strip_tags($property->description), 150))

@section('content')
@php
    $u = auth()->user();
    $ownerRate = \App\Models\Setting::commission('owner');
    $tenantRate = \App\Models\Setting::commission('tenant');
    $imgs = $property->images;
    $summary = $property->finishingSummary();
@endphp

<div class="bg-slate-50 border-b border-slate-100 dark:bg-slate-900/40 dark:border-slate-800">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-3 text-xs text-slate-500 flex items-center gap-2 flex-wrap">
        <a href="{{ route('home') }}" class="hover:text-brand-dark">الرئيسية</a> 
        <a href="{{ route('properties.index') }}" class="hover:text-brand-dark">العقارات</a> 
        <span class="text-slate-700 dark:text-slate-300 truncate">{{ $property->title }}</span>
    </div>
</div>

<div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 grid lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-8">

        {{-- العنوان --}}
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <span class="text-xs font-bold bg-ink text-brand px-3 py-1 rounded-full">{{ $property->stage_label }}</span>
                <span class="text-xs font-semibold bg-brand-50 text-brand-dark px-3 py-1 rounded-full">{{ $property->type_label }}</span>
                @if ($property->status !== 'approved')<span class="text-xs font-bold bg-{{ $property->status_color }}-100 text-{{ $property->status_color }}-700 px-3 py-1 rounded-full">{{ $property->status_label }}</span>@endif
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink leading-snug">{{ $property->title }}</h1>
            <p class="text-slate-500 mt-2 flex items-center gap-1.5">{{ $property->public_address }}</p>
        </div>

        {{-- المعرض --}}
        <div class="grid grid-cols-4 gap-2 rounded-2xl overflow-hidden">
            <button type="button" data-lb-src="{{ $imgs->first()?->large_url ?? $property->cover_url }}" class="col-span-4 sm:col-span-3 sm:row-span-2 h-72 sm:h-[26rem] bg-slate-100 overflow-hidden">
                <img src="{{ $imgs->first()?->url ?? $property->cover_url }}" alt="{{ $property->title }}" fetchpriority="high" class="w-full h-full object-cover hover:scale-105 transition duration-500">
            </button>
            @foreach ($imgs->slice(1, 4) as $img)
                <button type="button" data-lb-src="{{ $img->large_url }}" class="hidden sm:block h-[12.8rem] bg-slate-100 overflow-hidden"><img src="{{ $img->thumb_url }}" loading="lazy" alt="{{ $property->title }}" class="w-full h-full object-cover hover:scale-105 transition duration-500"></button>
            @endforeach
        </div>
        @if ($imgs->count() > 5) <p class="text-xs text-slate-500 -mt-5">+ {{ $imgs->count() - 5 }} صور إضافية: اضغط على أي صورة للتصفح</p> @endif
        <div class="hidden" aria-hidden="true">@foreach ($imgs->slice(5) as $img)<span data-lb-src="{{ $img->large_url }}"></span>@endforeach</div>

        {{-- المواصفات --}}
        <section class="card p-6">
            <h2 class="font-extrabold text-ink mb-5 section-title">المواصفات</h2>
            <dl class="grid grid-cols-2 sm:grid-cols-4 gap-5 text-sm">
                <div><dt class="text-slate-500 text-xs">المساحة</dt><dd class="font-bold text-ink">{{ $property->area_m2 }} م²</dd></div>
                @if ($property->rooms !== null)<div><dt class="text-slate-500 text-xs">الغرف</dt><dd class="font-bold text-ink">{{ $property->rooms }}</dd></div>@endif
                @if ($property->bathrooms !== null)<div><dt class="text-slate-500 text-xs">الحمامات</dt><dd class="font-bold text-ink">{{ $property->bathrooms }}</dd></div>@endif
                @if ($property->floors_count)<div><dt class="text-slate-500 text-xs">عدد الأدوار</dt><dd class="font-bold text-ink">{{ $property->floors_count }}</dd></div>@endif
                @if ($property->vacant_since_years)<div><dt class="text-slate-500 text-xs">معطل من</dt><dd class="font-bold text-ink">{{ $property->vacant_since_years }} سنة</dd></div>@endif
                <div><dt class="text-slate-500 text-xs">التشطيب على</dt><dd class="font-bold text-ink">{{ $property->finishing_by === 'tenant' ? 'المستأجر' : ($property->finishing_by === 'owner' ? 'المالك' : 'الطرفين') }}</dd></div>
                @if ($property->rent_free_months)<div><dt class="text-slate-500 text-xs">فترة سماح</dt><dd class="font-bold text-ink">{{ $property->rent_free_months }} شهر</dd></div>@endif
                @if ($property->annual_increase_percent)<div><dt class="text-slate-500 text-xs">زيادة سنوية</dt><dd class="font-bold text-ink">{{ $property->annual_increase_percent }}%</dd></div>@endif
            </dl>
            @if ($property->description)<p class="mt-6 text-sm leading-8 text-slate-600 whitespace-pre-line">{{ $property->description }}</p>@endif

            <div class="mt-6 grid sm:grid-cols-2 gap-5">
                @if ($property->utilities)
                    <div><p class="text-xs font-bold text-slate-500 mb-2">المرافق المتوفرة</p>
                        <div class="flex flex-wrap gap-2">@foreach ($property->utilities as $k)<span class="text-xs bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full dark:bg-emerald-950/40 dark:text-emerald-300">{{ catalog('utilities')[$k] ?? $k }}</span>@endforeach</div></div>
                @endif
                @if ($property->allowed_purposes)
                    <div><p class="text-xs font-bold text-slate-500 mb-2">الأغراض المسموحة</p>
                        <div class="flex flex-wrap gap-2">@foreach ($property->allowed_purposes as $k)<span class="text-xs bg-brand-50 text-brand-dark px-3 py-1 rounded-full">{{ catalog('purposes')[$k] ?? $k }}</span>@endforeach</div></div>
                @endif
            </div>
        </section>

        {{-- حالة التشطيب بالتفصيل --}}
        <section class="card p-6">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <h2 class="font-extrabold text-ink section-title">حالة التشطيب بالتفصيل</h2>
                @if ($summary['total'])
                    <div class="flex gap-2 text-[11px] font-bold">
                        <span class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full">تم {{ $summary['done'] }}</span>
                        <span class="bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full">جزئي {{ $summary['partial'] }}</span>
                        <span class="bg-rose-100 text-rose-700 px-2.5 py-1 rounded-full">ناقص {{ $summary['missing'] }}</span>
                    </div>
                @endif
            </div>
            @include('partials.finishing-table')
            @if ($property->finishing_notes)<p class="mt-5 text-sm text-slate-600 leading-8 bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4"><b class="text-ink">ملاحظات المالك:</b> {{ $property->finishing_notes }}</p>@endif
        </section>

        {{-- الموقع --}}
        <section class="card p-6">
            <h2 class="font-extrabold text-ink mb-4 section-title">الموقع</h2>
            <p class="text-sm text-slate-600">{{ $property->public_address }}</p>
            <p class="text-xs text-slate-500 mt-3 bg-brand-50 dark:bg-slate-800/50 rounded-lg px-3 py-2">رقم العقار والدور والشقة والعلامة المميزة بتظهر بعد إتمام الاتفاق وسداد نسبة المنصة.</p>
        </section>

        @if ($similar->count())
            <section><h2 class="font-extrabold text-ink mb-5 section-title">عقارات مشابهة</h2>
                <div class="grid sm:grid-cols-3 gap-5">@foreach ($similar as $property) @include('partials.property-card') @endforeach</div></section>
        @endif
    </div>

    {{-- العمود الجانبي --}}
    <aside class="space-y-5 lg:sticky lg:top-20 h-fit">
        <div class="card p-6 shadow-xl shadow-slate-200/60 dark:shadow-none">
            @if ($property->market_rent && $property->market_rent > $property->offered_rent)
                <p class="text-xs text-slate-400">سعر المنطقة حالياً: <span class="line-through">{{ egp($property->market_rent) }}</span> / شهر</p>
            @endif
            <p class="text-3xl font-extrabold text-ink mt-1">{{ egp($property->offered_rent) }}<span class="text-sm font-medium text-slate-500"> / شهر</span></p>
            @if ($property->discount_percent)
                <p class="mt-2 text-sm text-emerald-600 font-semibold">وفّر {{ $property->discount_percent }}% — يعني {{ egp($property->savingsFor($property->max_lease_years)) }} على {{ $property->max_lease_years }} سنين</p>
            @endif
            <p class="text-xs text-slate-500 mt-2">مدة العقد من {{ $property->min_lease_years }} إلى {{ $property->max_lease_years }} سنة</p>

            <hr class="my-5 border-slate-100 dark:border-slate-700">

            @if ($property->status === 'rented')
                <div class="text-center py-4"><p class="font-bold text-ink mt-2">العقار ده اتأجر</p><a href="{{ route('properties.index') }}" class="btn btn-ghost mt-3">شوف عقارات تانية</a></div>
            @elseif ($property->status !== 'approved')
                <p class="text-sm text-slate-500 text-center">العقار غير منشور حالياً ({{ $property->status_label }}).</p>
            @elseif (! $u)
                <a href="{{ route('register', ['role' => 'tenant']) }}" class="btn btn-brand w-full">سجّل كمستأجر وقدّم طلبك</a>
                <p class="text-center text-xs text-slate-500 mt-3">عندك حساب؟ <a href="{{ route('login') }}" class="text-brand-dark font-bold">ادخل</a></p>
            @elseif (! $u->isTenant())
                <p class="text-sm text-slate-500 text-center">التقديم متاح لحسابات المستأجرين فقط.</p>
            @elseif ($myRequest && in_array($myRequest->status, ['pending', 'negotiating', 'accepted', 'completed']))
                <div class="text-center">
                    <span class="text-xs font-bold bg-{{ $myRequest->status_color }}-100 text-{{ $myRequest->status_color }}-700 px-3 py-1 rounded-full">{{ $myRequest->status_label }}</span>
                    <p class="text-sm text-slate-600 mt-3">عندك طلب على العقار ده برقم <b>{{ $myRequest->code }}</b></p>
                    <a href="{{ route('requests.show', $myRequest) }}" class="btn btn-primary w-full mt-4">متابعة الطلب</a>
                </div>
            @else
                <form method="POST" action="{{ route('requests.store', $property->id) }}" data-rent-calc data-owner-rate="{{ $ownerRate }}" data-tenant-rate="{{ $tenantRate }}" class="space-y-4">
                    @csrf
                    <h3 class="font-bold text-ink">قدّم طلب إيجار</h3>
                    <div><label class="label">الغرض من العقار *</label>
                        <select name="purpose" class="field" required>
                            @foreach (($property->allowed_purposes ?: array_keys(catalog('purposes'))) as $k)<option value="{{ $k }}" @selected(old('purpose', $profile?->purpose) === $k)>{{ catalog('purposes')[$k] }}</option>@endforeach
                        </select></div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="label">الحالة الاجتماعية *</label>
                            <select name="marital_status" class="field" required>@foreach (catalog('marital_statuses') as $k => $v)<option value="{{ $k }}" @selected(old('marital_status', $profile?->marital_status) === $k)>{{ $v }}</option>@endforeach</select></div>
                        <div><label class="label">عدد الأفراد</label><input type="number" name="occupants" min="1" class="field" value="{{ old('occupants', $profile?->family_members) }}"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="label">إيجارك المقترح / شهر *</label><input type="number" name="proposed_rent" min="1" class="field" value="{{ old('proposed_rent', $property->offered_rent) }}" required></div>
                        <div><label class="label">المدة (سنين) *</label><input type="number" name="lease_years" min="{{ $property->min_lease_years }}" max="{{ $property->max_lease_years }}" class="field" value="{{ old('lease_years', $property->min_lease_years) }}" required></div>
                    </div>
                    <div><label class="label">تاريخ الاستلام المناسب</label><input type="date" name="start_date" class="field" min="{{ now()->toDateString() }}" value="{{ old('start_date') }}"></div>
                    <div><label class="label">رسالة للمالك</label><textarea name="message" rows="3" class="field" placeholder="عرّف بنفسك وخطتك للتشطيب…">{{ old('message') }}</textarea></div>
                    <p data-calc-out class="text-xs bg-brand-50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 rounded-lg px-3 py-2 leading-6"></p>
                    <label class="flex gap-2 items-start text-xs text-slate-600 leading-6"><input type="checkbox" name="terms" value="1" class="mt-1 accent-sky-500" required>
                        <span>أوافق على <a href="{{ route('terms') }}" target="_blank" class="text-brand-dark font-bold underline">شروط المنصة</a>، وإن نسبة المنصة {{ rtrim(rtrim(number_format($tenantRate, 2), '0'), '.') }}% من قيمة العقد، وإن التواصل بيتم داخل المنصة لحد إتمام الاتفاق.</span></label>
                    <button class="btn btn-brand w-full">أرسل الطلب</button>
                </form>
            @endif
        </div>

        <div class="card p-5 text-sm">
            <p class="text-xs text-slate-500 mb-3">صاحب العقار</p>
            <div class="flex items-center gap-3">
                <img src="{{ $property->owner->avatar_url }}" class="w-11 h-11 rounded-full object-cover" alt="">
                <div><p class="font-bold text-ink">{{ \Illuminate\Support\Str::of($property->owner->name)->explode(' ')->first() }} {{ mb_substr(\Illuminate\Support\Str::of($property->owner->name)->explode(' ')->get(1, ''), 0, 1) }}.</p>
                <p class="text-xs text-slate-500">عضو من {{ $property->owner->created_at->translatedFormat('F Y') }}</p></div>
            </div>
            <p class="text-xs text-slate-500 mt-4 leading-6">بيانات التواصل المباشرة بتتفتح للطرفين بعد الاتفاق وسداد نسبة المنصة، وده بيحمي حقك وحق المالك.</p>
        </div>

        <div class="flex gap-2">
            <form method="POST" action="{{ route('compare.toggle', $property->id) }}" class="flex-1">@csrf<button class="btn btn-ghost w-full">قارن</button></form>
            @if ($u?->isTenant())
                <form method="POST" action="{{ route('favorites.toggle', $property->id) }}" class="flex-1">@csrf<button class="btn btn-ghost w-full {{ $isFav ? '!text-rose-500' : '' }}">{{ $isFav ? 'في المفضلة' : 'احفظ' }}</button></form>
            @endif
        </div>
    </aside>
</div>

{{-- Lightbox --}}
<div data-lightbox class="hidden fixed inset-0 z-[120] bg-ink/90 items-center justify-center px-4">
    <button type="button" data-lb-close class="absolute top-5 end-5 text-white/80 hover:text-white text-base px-4 h-10 rounded-full bg-white/10">إغلاق</button>
    <button type="button" data-lb-prev dir="ltr" class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 w-auto px-4 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white">السابق</button>
    <img src="" alt="" class="max-h-[85vh] max-w-[90vw] object-contain rounded-lg">
    <button type="button" data-lb-next dir="ltr" class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 w-auto px-4 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white">التالي</button>
    <span data-lb-count class="absolute bottom-5 left-1/2 -translate-x-1/2 text-white/80 text-sm"></span>
</div>
@endsection
