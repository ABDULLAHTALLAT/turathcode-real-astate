@extends('layouts.app')
@section('title', 'الرئيسية')

@section('content')
@php
    $wrap = 'max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-10';
    $stages = catalog('finishing_stages');
@endphp

{{-- ============================== HERO ============================== --}}
<section class="relative text-white bg-ink min-h-[760px] lg:min-h-[88vh] flex items-center overflow-hidden">
    <div class="absolute inset-0 overflow-hidden" aria-hidden="true">
        @foreach ($hero as $i => $h)
            <img class="hero-slide {{ $i === 0 ? 'is-active' : '' }}" src="{{ $h['url'] }}" alt="" data-caption="{{ $h['caption'] }}" @if($i > 0) loading="lazy" @endif>
        @endforeach
        <div class="absolute inset-0 bg-black/55"></div>
        <div class="absolute inset-0 hero-vignette"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/30 to-ink/50"></div>
        <div class="absolute inset-0 bg-gradient-to-l from-ink/85 via-ink/30 to-transparent"></div>
    </div>

    <div class="relative z-10 {{ $wrap }} w-full pt-20 pb-60 lg:pb-64">
        <div class="max-w-3xl">
            <p style="--d:.1s" class="hero-rise eyebrow !text-brand">أول منصة مصرية لإحياء العقارات المعطلة</p>
            <h1 style="--d:.25s" class="hero-rise mt-6 text-5xl sm:text-6xl xl:text-7xl font-extrabold leading-[1.18]">
                بيتك <span class="brand-text">في ايدك</span><br>عقار معطل؟ خلّيه يشتغل.
            </h1>
            <p style="--d:.45s" class="hero-rise mt-7 text-lg sm:text-xl text-white/80 leading-9 max-w-2xl">
                اعرض مبنى أو شقة فاضية من سنين بتفاصيل التشطيب الحقيقية، واتفق مع مستأجر يكمّل التشطيب ويستأجر لمدة طويلة بسعر أقل من السوق. أو دوّر على عقار بسعر ذكي وأنت اللي تظبطه على ذوقك.
            </p>
            <div style="--d:.65s" class="hero-rise mt-10 flex flex-wrap gap-4">
                <a href="{{ route('register', ['role' => 'owner']) }}" class="btn btn-brand !min-h-[56px] !px-9 !text-base shadow-lg shadow-brand/20">أنا مالك وعايز أعرض عقاري</a>
                <a href="{{ route('properties.index') }}" class="btn !min-h-[56px] !px-9 !text-base border border-white/40 text-white hover:border-brand hover:text-brand backdrop-blur-sm">أدور على عقار</a>
            </div>
        </div>
    </div>

    @if (count($hero))
    <div class="absolute inset-x-0 bottom-[170px] lg:bottom-[160px] z-10">
        <div class="{{ $wrap }} flex items-center justify-between gap-4">
            <div class="flex items-center gap-2" role="tablist">
                @foreach ($hero as $i => $h)
                    <button type="button" class="hero-dot {{ $i === 0 ? 'is-active' : '' }}" aria-label="الصورة {{ $i + 1 }}"></button>
                @endforeach
            </div>
            <p class="hidden sm:flex items-center gap-4 text-sm text-white/80">
                <span id="heroCaption">{{ $hero[0]['caption'] }}</span>
                <span class="text-white/40" dir="ltr"><span id="heroIndex">01</span> / {{ sprintf('%02d', count($hero)) }}</span>
            </p>
        </div>
    </div>
    @endif
</section>

{{-- ====================== SEARCH (نص على الهيرو ونص تحته) ====================== --}}
<div class="relative z-20 -mt-36 sm:-mt-32 max-w-7xl mx-auto px-4 sm:px-6 lg:px-10">
    <form action="{{ route('properties.index') }}" method="GET" class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl shadow-black/25 ring-1 ring-black/5 p-6 sm:p-9">
        <div class="flex items-end justify-between gap-4 mb-6">
            <div>
                <p class="eyebrow">ابحث</p>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-ink mt-1">دوّر على عقارك</h2>
            </div>
            <a href="{{ route('properties.index') }}" class="hidden sm:block text-sm font-semibold text-brand-dark hover:underline">عرض كل العقارات ←</a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-6 gap-4">
            <div class="lg:col-span-2"><label class="label">ابحث بالمنطقة أو الاسم</label><input name="q" class="field" placeholder="مثال: مدينة نصر، المنصورة…"></div>
            <div><label class="label">المحافظة</label>
                <select name="governorate" class="field"><option value="">كل المحافظات</option>@foreach (catalog('governorates') as $g)<option>{{ $g }}</option>@endforeach</select></div>
            <div><label class="label">نوع العقار</label>
                <select name="type" class="field"><option value="">الكل</option>@foreach (catalog('property_types') as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
            <div><label class="label">مرحلة التشطيب</label>
                <select name="stage" class="field"><option value="">الكل</option>@foreach ($stages as $k => $v)<option value="{{ $k }}">{{ $v['label'] }}</option>@endforeach</select></div>
            <div><label class="label">الغرض</label>
                <select name="purpose" class="field"><option value="">الكل</option>@foreach (catalog('purposes') as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
            <div class="lg:col-span-2"><label class="label">أقصى إيجار شهري (ج.م)</label><input type="number" name="max_price" min="0" class="field" placeholder="مثال: 15000"></div>
            <div><label class="label">مدة العقد (سنين)</label><input type="number" name="years" min="1" class="field" placeholder="مثال: 5"></div>
            <div class="lg:col-span-3 flex items-end"><button class="btn btn-primary w-full !min-h-[52px] !text-base">ابحث</button></div>
        </div>
    </form>
</div>

{{-- ============================== STATS ============================== --}}
<section class="{{ $wrap }} pt-24">
    <dl class="grid grid-cols-2 lg:grid-cols-4 gap-y-10 text-center">
        @foreach ([[$stats['properties'], 'عقار معروض'], [$stats['governorates'], 'محافظة'], [$stats['owners'], 'مالك مسجّل'], [$stats['tenants'], 'مستأجر مسجّل']] as $i => [$n, $label])
            <div class="px-4 {{ $i > 0 ? 'lg:border-s border-slate-200 dark:border-slate-700' : '' }}">
                <dt class="text-5xl sm:text-6xl font-extrabold text-ink" dir="ltr"><span data-count="{{ $n }}">0</span><span class="text-brand">+</span></dt>
                <dd class="mt-3 text-base text-slate-500">{{ $label }}</dd>
            </div>
        @endforeach
    </dl>
</section>

{{-- ============================== ABOUT ============================== --}}
<section class="reveal {{ $wrap }} py-28">
    <div class="grid lg:grid-cols-2 gap-16 xl:gap-24 items-center">
        <div class="relative">
            <div class="absolute -top-5 -start-5 w-2/3 h-2/3 rounded-3xl border-2 border-brand/60" aria-hidden="true"></div>
            <img src="{{ $aboutImages[0] ?? asset('images/placeholder.svg') }}" alt="" loading="lazy" class="relative w-full h-[440px] sm:h-[580px] object-cover rounded-3xl shadow-2xl">
            @if (isset($aboutImages[1]))
                <img src="{{ $aboutImages[1] }}" alt="" loading="lazy" class="hidden sm:block absolute -bottom-10 -end-8 w-56 h-56 object-cover rounded-3xl ring-8 ring-white dark:ring-slate-900 shadow-xl">
            @endif
            <div class="absolute bottom-8 start-8 bg-ink text-white rounded-2xl px-6 py-5 shadow-xl">
                <p class="text-4xl font-extrabold text-brand" dir="ltr">{{ $ownerRate + $tenantRate }}%</p>
                <p class="text-xs text-white/70 mt-1">إجمالي نسبة المنصة بس</p>
            </div>
        </div>

        <div>
            <p class="eyebrow">عن المنصة</p>
            <h2 class="mt-4 text-4xl sm:text-5xl font-extrabold text-ink leading-tight">عقارات واقفة من سنين، وناس محتاجة مكان.</h2>
            <span class="bar mt-6"></span>
            <p class="mt-8 text-lg text-slate-600 leading-9">كتير من المباني والشقق فاضية من سنين من غير ما حد يستفيد منها، وفي نفس الوقت كتير ناس مستعدة تكمّل التشطيب وتستأجر لسنين طويلة لو السعر مناسب. «بيتك في ايدك» هي الوسيط اللي بيجمع الطرفين بشروط واضحة.</p>
            <p class="mt-5 text-lg text-slate-600 leading-9">المالك بيعرض عقاره بكل تفاصيله، والمستأجر بيشوف بالظبط إيه اللي موجود وإيه اللي ناقص، والاتفاق على السعر والمدة بيتم جوه المنصة وبعقد.</p>
            <ul class="mt-8 grid sm:grid-cols-2 gap-x-8 gap-y-4 text-base text-slate-700">
                @foreach (['عنوان بالتفصيل ومرحلة تشطيب واضحة', 'جدول بنود التشطيب: تم / جزئي / ناقص', 'تفاوض على الإيجار والمدة', 'بيانات التواصل بتتفتح بعد الاتفاق'] as $point)
                    <li class="flex items-start gap-3"><span class="mt-3 w-2 h-2 rounded-full bg-brand shrink-0"></span>{{ $point }}</li>
                @endforeach
            </ul>
            <a href="{{ route('how') }}" class="btn btn-primary mt-10 !min-h-[54px] !px-10 !text-base">كيف تعمل المنصة</a>
        </div>
    </div>
</section>

{{-- ============================== المراحل ============================== --}}
<section class="bg-slate-50 dark:bg-slate-900/40 py-28">
    <div class="{{ $wrap }}">
        <div class="reveal flex flex-wrap items-end justify-between gap-6 mb-14">
            <div class="max-w-2xl">
                <p class="eyebrow">مراحل التشطيب</p>
                <h2 class="mt-3 text-4xl sm:text-5xl font-extrabold text-ink">ابدأ من المرحلة اللي تناسبك</h2>
                <span class="bar mt-6"></span>
                <p class="mt-6 text-lg text-slate-600 leading-8">من الهيكل الخرساني لحد العقار المفروش. اختار الشغل اللي هتعمله بإيدك، والإيجار بيقل كل ما التشطيب الناقص يزيد.</p>
            </div>
            <a href="{{ route('properties.index') }}" class="btn btn-ghost !min-h-[52px] !px-8">كل العقارات</a>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-3 auto-rows-[240px] sm:auto-rows-[320px] gap-5">
            @foreach ($stages as $key => $st)
                <a href="{{ route('properties.index', ['stage' => $key]) }}" style="--d:{{ $loop->index * 0.07 }}s"
                   class="reveal group relative rounded-3xl overflow-hidden bg-ink ">
                    @if ($stageImages[$key] ?? null)
                        <img src="{{ $stageImages[$key] }}" alt="{{ $st['label'] }}" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-[1400ms] ease-out">
                    @endif
                    <span class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></span>
                    <span class="absolute inset-0 rounded-3xl ring-1 ring-inset ring-white/10 group-hover:ring-brand/80 transition"></span>
                    <span class="absolute bottom-0 inset-x-0 p-7 text-white">
                        <span class="block text-xs text-brand font-semibold tracking-wide">المرحلة {{ $loop->iteration }}</span>
                        <span class="block mt-1 text-2xl sm:text-3xl font-bold">{{ $st['label'] }}</span>
                        <span class="mt-2 flex items-center justify-between text-sm text-white/75"><span>{{ $st['hint'] }}</span><span class="text-brand font-semibold shrink-0 ms-3">{{ $stageCounts[$key] ?? 0 }} عقار</span></span>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================== المحافظات ============================== --}}
<section class="{{ $wrap }} py-28">
    <div class="reveal text-center max-w-3xl mx-auto mb-16">
        <p class="eyebrow">من كل ربوع مصر</p>
        <h2 class="mt-3 text-4xl sm:text-5xl font-extrabold text-ink">عقارات معطلة من الإسكندرية لأسوان</h2>
        <span class="bar mx-auto mt-6"></span>
        <p class="mt-6 text-lg text-slate-600 leading-8">اختار محافظتك وشوف العقارات المتاحة فيها.</p>
    </div>
    @php($spans = ['col-span-2 lg:row-span-2', '', '', '', ''])
    <div class="grid grid-cols-2 lg:grid-cols-4 auto-rows-[220px] sm:auto-rows-[270px] gap-5">
        @foreach ($govImages as $gov => $img)
            <a href="{{ route('properties.index', ['governorate' => $gov]) }}" style="--d:{{ $loop->index * 0.07 }}s"
               class="reveal group relative rounded-3xl overflow-hidden bg-ink {{ $spans[$loop->index] ?? '' }}">
                @if ($img)<img src="{{ $img }}" alt="{{ $gov }}" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-[1400ms] ease-out">@endif
                <span class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></span>
                <span class="absolute inset-0 rounded-3xl ring-1 ring-inset ring-white/10 group-hover:ring-brand/80 transition"></span>
                <span class="absolute bottom-0 inset-x-0 p-6 sm:p-7 text-white">
                    <span class="block text-2xl sm:text-3xl font-bold">{{ $gov }}</span>
                    <span class="block mt-1 text-sm text-brand">{{ $govCounts[$gov] }} عقار</span>
                </span>
            </a>
        @endforeach
    </div>
    @if ($govCounts->count() > 5)
        <div class="reveal mt-10 flex flex-wrap justify-center gap-3">
            @foreach ($govCounts->skip(5) as $gov => $count)
                <a href="{{ route('properties.index', ['governorate' => $gov]) }}" class="btn btn-ghost !rounded-full">{{ $gov }} <span class="text-xs text-slate-400">{{ $count }}</span></a>
            @endforeach
        </div>
    @endif
</section>

{{-- ============================== عقارات مميزة ============================== --}}
@if ($featured->count())
<section class="bg-slate-50 dark:bg-slate-900/40 py-28">
    <div class="{{ $wrap }}">
        <div class="reveal flex flex-wrap items-end justify-between gap-6 mb-12">
            <div>
                <p class="eyebrow">اختياراتنا</p>
                <h2 class="mt-3 text-4xl sm:text-5xl font-extrabold text-ink">عقارات مميزة</h2>
                <span class="bar mt-6"></span>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" data-snap-prev="featuredRow" class="h-12 px-6 rounded-full border border-slate-300 bg-white text-sm font-semibold text-ink hover:bg-ink hover:text-white hover:border-ink transition dark:bg-slate-800 dark:border-slate-600">السابق</button>
                <button type="button" data-snap-next="featuredRow" class="h-12 px-6 rounded-full border border-slate-300 bg-white text-sm font-semibold text-ink hover:bg-ink hover:text-white hover:border-ink transition dark:bg-slate-800 dark:border-slate-600">التالي</button>
            </div>
        </div>
        <div id="featuredRow" class="snap-row">
            @foreach ($featured as $property)
                <div>@include('partials.property-card')</div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============================== CTA ============================== --}}
<section class="{{ $wrap }} py-28">
    <div class="reveal relative overflow-hidden rounded-[2rem] hero-bg text-white px-8 py-16 sm:px-16 sm:py-20 grid lg:grid-cols-5 gap-10 items-center">
        <div class="lg:col-span-3">
            <p class="eyebrow !text-brand">للملّاك</p>
            <h2 class="mt-4 text-4xl sm:text-5xl font-extrabold leading-tight">عندك مبنى أو شقة فاضية؟</h2>
            <p class="mt-6 text-lg text-white/75 leading-9 max-w-2xl">بدل ما العقار يفضل مقفول، اعرضه مجاناً وخلّي مستأجر يكمّل تشطيبه ويدفعلك إيجار شهري لسنين. إنت اللي بتحدد السعر والمدة، وإحنا بنضمن حقك.</p>
        </div>
        <div class="lg:col-span-2 flex flex-wrap gap-4 lg:justify-end">
            <a href="{{ route('register', ['role' => 'owner']) }}" class="btn btn-brand !min-h-[56px] !px-9 !text-base">اعرض عقارك دلوقتي</a>
            <a href="{{ route('terms') }}" class="btn !min-h-[56px] !px-9 !text-base border border-white/40 text-white hover:border-brand hover:text-brand">اعرف نسبة المنصة</a>
        </div>
    </div>
</section>

{{-- ============================== خطوات ============================== --}}
<section class="{{ $wrap }} pb-28">
    <div class="reveal text-center max-w-3xl mx-auto mb-16">
        <p class="eyebrow">الخطوات</p>
        <h2 class="mt-3 text-4xl sm:text-5xl font-extrabold text-ink">إزاي بتشتغل المنصة؟</h2>
        <span class="bar mx-auto mt-6"></span>
    </div>
    <ol class="grid md:grid-cols-2 xl:grid-cols-4 gap-x-10 gap-y-14">
        @foreach ([
            ['المالك يعرض العقار', 'صور، عنوان بالتفصيل، وكل بند تشطيب حالته إيه: تم، جزئي، ولا ناقص.'],
            ['المستأجر يختار ويقدّم', 'يحدد الغرض (سكن، شركة…)، الحالة الاجتماعية، والإيجار والمدة اللي تناسبه.'],
            ['تفاوض وتوافق', 'عرض وعرض مضاد على السعر والمدة لحد ما الطرفين يتفقوا.'],
            ['عقد وبيانات تواصل', 'بعد سداد نسبة المنصة بتتفتح بيانات التواصل الكاملة والعنوان.'],
        ] as $i => [$t, $d])
            <li class="reveal relative border-t-2 border-ink/10 dark:border-white/10 pt-8" style="--d:{{ $i * 0.1 }}s">
                <span class="block text-7xl font-extrabold text-brand/40 leading-none" dir="ltr">0{{ $i + 1 }}</span>
                <h3 class="mt-6 text-2xl font-bold text-ink">{{ $t }}</h3>
                <p class="mt-3 text-base text-slate-600 leading-8">{{ $d }}</p>
            </li>
        @endforeach
    </ol>
</section>

{{-- ============================== WHY US ============================== --}}
<section class="bg-ink text-white py-28">
    <div class="{{ $wrap }}">
        <div class="reveal grid lg:grid-cols-3 gap-10 mb-16 items-end">
            <div class="lg:col-span-2">
                <p class="eyebrow !text-brand">ليه بيتك في ايدك</p>
                <h2 class="mt-3 text-4xl sm:text-5xl font-extrabold leading-tight">اتفاق واضح من أول يوم لآخر يوم.</h2>
            </div>
            <p class="text-lg text-white/70 leading-9">مفيش وسيط بيخبّي حاجة. كل بند في العقار معروف، وكل نسبة معروفة، وكل خطوة بتتسجل.</p>
        </div>
        <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-6">
            @foreach ([
                ['تشطيب شفاف', 'جدول بنود التشطيب لكل عقار: الكهرباء، السباكة، المحارة، السيراميك… كل بند «تم / جزئي / ناقص».'],
                ['نسبة معلنة', 'نسبة المنصة '.$ownerRate.'% على المالك و'.$tenantRate.'% على المستأجر من إجمالي العقد. بتتثبت وقت الاتفاق ومبتتغيرش.'],
                ['تواصل محمي', 'أرقام التليفون والإيميلات مخفية في الإعلانات والرسائل لحد ما العقد يتم، عشان حق الطرفين.'],
                ['عقود طويلة', 'مدة إيجار من سنة لـ 30 سنة، مع فترة سماح وزيادة سنوية متفق عليها من الأول.'],
            ] as $i => [$t, $d])
                <div class="reveal rounded-3xl border border-white/10 bg-white/5 p-8 hover:border-brand/60 transition" style="--d:{{ $i * 0.08 }}s">
                    <h3 class="text-xl font-bold text-brand">{{ $t }}</h3>
                    <p class="mt-4 text-white/70 leading-8">{{ $d }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================== أحدث العقارات ============================== --}}
@if ($latest->count())
<section class="{{ $wrap }} py-28">
    <div class="reveal flex flex-wrap items-end justify-between gap-6 mb-12">
        <div>
            <p class="eyebrow">جديد على المنصة</p>
            <h2 class="mt-3 text-4xl sm:text-5xl font-extrabold text-ink">أحدث العقارات</h2>
            <span class="bar mt-6"></span>
        </div>
        <a href="{{ route('properties.index') }}" class="btn btn-ghost !min-h-[52px] !px-8">عرض الكل</a>
    </div>
    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-7">
        @foreach ($latest as $property)
            <div class="reveal" style="--d:{{ ($loop->index % 4) * 0.08 }}s">@include('partials.property-card')</div>
        @endforeach
    </div>
</section>
@endif

{{-- ============================== أنواع العقارات ============================== --}}
<section class="bg-slate-50 dark:bg-slate-900/40 py-20">
    <div class="{{ $wrap }}">
        <h2 class="reveal text-2xl sm:text-3xl font-extrabold text-ink mb-8">تصفح حسب النوع</h2>
        <div class="reveal flex flex-wrap gap-3">
            @foreach (catalog('property_types') as $k => $label)
                <a href="{{ route('properties.index', ['type' => $k]) }}" class="btn btn-ghost !rounded-full !px-7">{{ $label }} <span class="text-xs text-slate-400">{{ $typeCounts[$k] ?? 0 }}</span></a>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================== FAQ ============================== --}}
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-10 py-28">
    <div class="reveal text-center mb-14">
        <p class="eyebrow">أسئلة شائعة</p>
        <h2 class="mt-3 text-4xl sm:text-5xl font-extrabold text-ink">محتاج تعرف إيه؟</h2>
        <span class="bar mx-auto mt-6"></span>
    </div>
    <div class="space-y-4">
        @foreach ([
            ['هل عرض العقار بفلوس؟', 'عرض العقار على المنصة مجاني. المنصة بتاخد نسبة بس لما الاتفاق يتم: '.$ownerRate.'% من المالك و'.$tenantRate.'% من المستأجر من إجمالي قيمة العقد.'],
            ['إزاي المالك بيحدد السعر؟', 'بتكتب سعر السوق الحالي للعقار وسعر الإيجار اللي عايز تعرضه بيه، وتحدد أقل وأقصى مدة للعقد وفترة السماح والزيادة السنوية.'],
            ['مين بيكمّل التشطيب الناقص؟', 'ده بيتحدد في الإعلان: ممكن المالك أو المستأجر أو الاتنين بالاشتراك، وكل بند ناقص مكتوب بالتفصيل في جدول التشطيب.'],
            ['إمتى بشوف رقم المالك أو عنوان العقار بالتفصيل؟', 'بعد ما الاتفاق يتم ونسبة المنصة تتسدد من الطرفين. قبل كده التواصل بيكون جوه المنصة، وأي رقم في الرسائل بيتخفى.'],
            ['ينفع أقارن بين أكتر من عقار؟', 'أكيد. أضف أي عقار للمقارنة وشوف السعر والمساحة ومرحلة التشطيب جنب بعض. وكمان تقدر تحفظ المفضلة من حسابك كمستأجر.'],
        ] as $i => [$q, $a])
            <details class="reveal group rounded-2xl border border-slate-200 dark:border-slate-700 open:border-brand open:shadow-lg bg-white dark:bg-slate-900 transition" style="--d:{{ $i * 0.05 }}s" @if($i === 0) open @endif>
                <summary class="flex items-center justify-between gap-6 px-7 py-6 text-lg font-bold text-ink">
                    {{ $q }}
                    <span class="faq-sign text-3xl font-light text-brand transition-transform shrink-0" aria-hidden="true">+</span>
                </summary>
                <p class="px-7 pb-7 text-base text-slate-600 leading-8">{{ $a }}</p>
            </details>
        @endforeach
    </div>
</section>

{{-- ============================== CONTACT ============================== --}}
<section id="contact" class="{{ $wrap }} pb-28 scroll-mt-24">
    <div class="reveal rounded-[2rem] overflow-hidden grid lg:grid-cols-5 bg-ink text-white shadow-2xl shadow-ink/20">
        <div class="lg:col-span-2 p-10 sm:p-14 flex flex-col justify-between gap-10 hero-bg">
            <div>
                <p class="eyebrow !text-brand">تواصل معنا</p>
                <h2 class="mt-4 text-4xl font-extrabold leading-tight">عندك سؤال؟ ابعتلنا.</h2>
                <p class="mt-5 text-white/70 leading-8">فريقنا بيرد عليك في أسرع وقت، سواء كنت مالك عايز تعرض عقارك أو مستأجر بيدور.</p>
            </div>
            <dl class="space-y-5 text-base">
                <div><dt class="text-xs text-white/50">الموبايل</dt><dd class="mt-1 font-semibold" dir="ltr"><a href="tel:{{ preg_replace('/\s+/', '', config('site.phone')) }}" class="hover:text-brand">{{ config('site.phone') }}</a></dd></div>
                <div><dt class="text-xs text-white/50">الإيميل</dt><dd class="mt-1 font-semibold"><a href="mailto:{{ config('site.email') }}" class="hover:text-brand">{{ config('site.email') }}</a></dd></div>
                <div><dt class="text-xs text-white/50">واتساب</dt><dd class="mt-1 font-semibold"><a href="https://wa.me/{{ config('site.whatsapp') }}" target="_blank" rel="noopener" class="hover:text-brand">ابدأ محادثة</a></dd></div>
            </dl>
        </div>
        <form method="POST" action="{{ route('contact.send') }}" class="lg:col-span-3 p-10 sm:p-14 bg-white dark:bg-slate-900 text-slate-800 space-y-5">
            @csrf
            <div class="grid sm:grid-cols-2 gap-5">
                <div><label class="label">الاسم *</label><input name="name" value="{{ old('name') }}" class="field" required></div>
                <div><label class="label">الموبايل</label><input name="phone" value="{{ old('phone') }}" class="field" dir="ltr"></div>
            </div>
            <div><label class="label">الإيميل</label><input type="email" name="email" value="{{ old('email') }}" class="field" dir="ltr"></div>
            <div><label class="label">رسالتك *</label><textarea name="message" rows="5" class="field" required>{{ old('message') }}</textarea></div>
            <button class="btn btn-primary !min-h-[54px] !px-12 !text-base">إرسال الرسالة</button>
        </form>
    </div>
</section>
@endsection
