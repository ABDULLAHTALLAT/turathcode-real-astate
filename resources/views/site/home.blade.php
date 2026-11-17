@extends('layouts.app')
@section('title', 'الرئيسية')

@section('content')
<section class="hero-bg text-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-28 lg:pt-20 lg:pb-36 grid lg:grid-cols-2 gap-12 items-center">
        <div class="rise">
            <span class="inline-flex items-center gap-2 bg-white/10 border border-white/15 text-brand text-xs font-semibold px-3 py-1.5 rounded-full mb-5"><i class="bi bi-stars"></i> أول منصة مصرية لإحياء العقارات المعطلة</span>
            <h1 class="text-4xl sm:text-5xl font-extrabold leading-[1.25]">بيتك <span class="brand-text">في ايدك</span><br>عقار معطل؟ خلّيه يشتغل.</h1>
            <p class="mt-5 text-white/75 leading-8 max-w-xl">اعرض مبنى أو شقة فاضية من سنين بتفاصيل التشطيب الحقيقية، واتفق مع مستأجر يكمّل التشطيب ويستأجر لمدة طويلة بسعر أقل من السوق. أو دوّر على عقار بسعر ذكي وأنت اللي تظبطه على ذوقك.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('register', ['role' => 'owner']) }}" class="btn btn-brand"><i class="bi bi-building-add"></i> أنا مالك وعايز أعرض عقاري</a>
                <a href="{{ route('properties.index') }}" class="btn btn-ghost !bg-transparent !text-white !border-white/30 hover:!border-brand"><i class="bi bi-search"></i> أدور على عقار</a>
            </div>
            <dl class="mt-10 grid grid-cols-3 gap-4 max-w-md">
                <div><dt class="text-xs text-white/60">عقار معروض</dt><dd class="text-2xl font-extrabold text-brand">{{ number_format($stats['properties']) }}</dd></div>
                <div><dt class="text-xs text-white/60">تم تأجيره</dt><dd class="text-2xl font-extrabold text-brand">{{ number_format($stats['rented']) }}</dd></div>
                <div><dt class="text-xs text-white/60">مالك ومستأجر</dt><dd class="text-2xl font-extrabold text-brand">{{ number_format($stats['owners'] + $stats['tenants']) }}</dd></div>
            </dl>
        </div>

        {{-- بحث سريع --}}
        <form action="{{ route('properties.index') }}" method="GET" class="bg-white text-slate-800 rounded-2xl p-5 sm:p-6 shadow-2xl shadow-black/30 rise dark:bg-slate-900 dark:text-slate-100">
            <h2 class="font-extrabold text-ink text-lg mb-4">دوّر على عقارك</h2>
            <div class="grid sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2"><label class="label">ابحث بالمنطقة أو الاسم</label><input name="q" class="field" placeholder="مثال: مدينة نصر، المنصورة…"></div>
                <div><label class="label">المحافظة</label>
                    <select name="governorate" class="field"><option value="">كل المحافظات</option>@foreach (catalog('governorates') as $g)<option>{{ $g }}</option>@endforeach</select></div>
                <div><label class="label">نوع العقار</label>
                    <select name="type" class="field"><option value="">الكل</option>@foreach (catalog('property_types') as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
                <div><label class="label">مرحلة التشطيب</label>
                    <select name="stage" class="field"><option value="">الكل</option>@foreach (catalog('finishing_stages') as $k => $v)<option value="{{ $k }}">{{ $v['label'] }}</option>@endforeach</select></div>
                <div><label class="label">الغرض</label>
                    <select name="purpose" class="field"><option value="">الكل</option>@foreach (catalog('purposes') as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
                <div><label class="label">أقصى إيجار شهري (ج.م)</label><input type="number" name="max_price" min="0" class="field" placeholder="مثال: 15000"></div>
                <div><label class="label">مدة العقد (سنين)</label><input type="number" name="years" min="1" class="field" placeholder="مثال: 5"></div>
            </div>
            <button class="btn btn-primary w-full mt-5"><i class="bi bi-search"></i> ابحث</button>
        </form>
    </div>
</section>

{{-- الأقسام: ادخل عن طريق مرحلة التشطيب --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-14 relative z-10">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        @php($icons = ['concrete' => 'bi-bricks', 'red_brick' => 'bi-bricks', 'plaster' => 'bi-paint-bucket', 'half_finished' => 'bi-tools', 'finished' => 'bi-house-check', 'furnished' => 'bi-lamp'])
        @foreach (catalog('finishing_stages') as $key => $st)
            <a href="{{ route('properties.index', ['stage' => $key]) }}" class="card p-4 hover:shadow-xl hover:border-brand transition text-center group">
                <span class="mx-auto w-11 h-11 rounded-xl bg-brand-50 text-brand-dark flex items-center justify-center text-xl group-hover:bg-brand group-hover:text-ink transition"><i class="bi {{ $icons[$key] }}"></i></span>
                <p class="font-bold text-ink mt-2 text-sm">{{ $st['label'] }}</p>
                <p class="text-[11px] text-slate-500 mt-0.5">{{ $stageCounts[$key] ?? 0 }} عقار</p>
            </a>
        @endforeach
    </div>
</section>

@if ($featured->count())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
    <div class="flex items-end justify-between mb-7">
        <h2 class="section-title text-2xl font-extrabold text-ink">عقارات مميزة</h2>
        <a href="{{ route('properties.index') }}" class="text-sm font-semibold text-brand-dark hover:underline">عرض الكل ←</a>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($featured as $property) @include('partials.property-card') @endforeach
    </div>
</section>
@endif

{{-- تصفح بنوع العقار --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
    <h2 class="section-title text-2xl font-extrabold text-ink mb-7">تصفح حسب النوع</h2>
    <div class="flex flex-wrap gap-3">
        @foreach (catalog('property_types') as $k => $label)
            <a href="{{ route('properties.index', ['type' => $k]) }}" class="btn btn-ghost !rounded-full">{{ $label }} <span class="text-xs text-slate-400">{{ $typeCounts[$k] ?? 0 }}</span></a>
        @endforeach
    </div>
</section>

{{-- ازاي بتشتغل --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-20">
    <h2 class="section-title text-2xl font-extrabold text-ink mb-8">ازاي بتشتغل المنصة؟</h2>
    <div class="grid md:grid-cols-4 gap-5">
        @foreach ([
            ['bi-building-add', 'المالك يعرض العقار', 'صور، عنوان بالتفصيل، وكل بند تشطيب حالته إيه: تم، جزئي، ولا ناقص.'],
            ['bi-search-heart', 'المستأجر يختار ويقدّم', 'يحدد الغرض (سكن، شركة…)، الحالة الاجتماعية، والإيجار والمدة اللي يناسبوه.'],
            ['bi-chat-left-dots', 'تفاوض وتوافق', 'عرض وعرض مضاد على السعر والمدة لحد ما الطرفين يتفقوا.'],
            ['bi-patch-check', 'عقد وبيانات تواصل', 'بعد سداد نسبة المنصة بتتفتح بيانات التواصل الكاملة والعنوان.'],
        ] as $i => [$ic, $t, $d])
            <div class="card p-6 relative">
                <span class="absolute top-4 end-4 text-4xl font-black text-slate-100 dark:text-slate-800">{{ $i + 1 }}</span>
                <span class="w-12 h-12 rounded-xl brand-gradient text-ink flex items-center justify-center text-xl mb-4"><i class="bi {{ $ic }}"></i></span>
                <h3 class="font-bold text-ink mb-2">{{ $t }}</h3>
                <p class="text-sm text-slate-500 leading-7">{{ $d }}</p>
            </div>
        @endforeach
    </div>
</section>

@if ($latest->count())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-20">
    <h2 class="section-title text-2xl font-extrabold text-ink mb-7">أحدث العقارات</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach ($latest as $property) @include('partials.property-card') @endforeach
    </div>
</section>
@endif

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-20">
    <div class="hero-bg rounded-3xl p-8 sm:p-12 text-white grid md:grid-cols-2 gap-8 items-center">
        <div>
            <h2 class="text-2xl sm:text-3xl font-extrabold leading-snug">عندك مبنى أو شقة فاضية؟</h2>
            <p class="text-white/70 mt-3 leading-8">بدل ما العقار يفضل مقفول، اعرضه مجاناً وخلّي مستأجر يكمّل تشطيبه ويدفعلك إيجار شهري لسنين.</p>
        </div>
        <div class="flex flex-wrap gap-3 md:justify-end">
            <a href="{{ route('register', ['role' => 'owner']) }}" class="btn btn-brand">اعرض عقارك دلوقتي</a>
            <a href="{{ route('terms') }}" class="btn btn-ghost !bg-transparent !text-white !border-white/30">اعرف نسبة المنصة</a>
        </div>
    </div>
</section>
@endsection
