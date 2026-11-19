@extends('layouts.app')
@section('title', __('الرئيسية'))

@section('content')
@php
    $wrap = 'max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-10';
    $stages = catalog('finishing_stages');
@endphp

{{-- ============================== HERO ============================== --}}
<section class="relative text-white bg-ink min-h-[680px] lg:min-h-[80vh] flex items-center overflow-hidden">
    <div class="absolute inset-0 overflow-hidden" aria-hidden="true">
        @foreach ($hero as $i => $h)
            <img class="hero-slide {{ $i === 0 ? 'is-active' : '' }}" src="{{ $h['url'] }}" alt="" data-caption="{{ $h['caption'] }}" @if($i > 0) loading="lazy" @endif>
        @endforeach
        <div class="absolute inset-0 bg-ink/35"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-ink/80 via-ink/10 to-ink/30"></div>
        <div class="absolute inset-0 hero-side"></div>
    </div>

    <div class="relative z-10 {{ $wrap }} w-full pt-20 pb-60 lg:pb-64">
        <div class="max-w-3xl">
            <p style="--d:.1s" class="hero-rise eyebrow !text-brand">{{ __('أول منصة مصرية لإحياء العقارات المعطلة') }}</p>
            <h1 class="hero-title mt-5 text-3xl sm:text-4xl xl:text-[3.4rem] font-extrabold">
                <span class="ln"><span style="--d:.25s">{{ __('بيتك') }} <span class="brand-shine">{{ __('في ايدك') }}</span></span></span>
                <span class="ln"><span style="--d:.5s">{{ __('عقار معطل؟ خلّيه يشتغل.') }}</span></span>
            </h1>
            <p style="--d:.8s" class="hero-rise mt-6 text-base sm:text-base text-white/80 leading-9 max-w-2xl">
                {{ __('اعرض مبنى أو شقة فاضية من سنين بتفاصيل التشطيب الحقيقية، واتفق مع مستأجر يكمّل التشطيب ويستأجر لمدة طويلة بسعر أقل من السوق. أو دوّر على عقار بسعر ذكي وأنت اللي تظبطه على ذوقك.') }}
            </p>
            <div style="--d:1s" class="hero-rise mt-10 flex flex-wrap gap-4">
                <a href="{{ route('register', ['role' => 'owner']) }}" class="btn btn-brand !min-h-[48px] !px-9 !text-sm shadow-lg shadow-brand/20">{{ __('أنا مالك وعايز أعرض عقاري') }}</a>
                <a href="{{ route('properties.index') }}" class="btn !min-h-[48px] !px-9 !text-sm border border-white/40 text-white hover:border-brand hover:text-brand backdrop-blur-sm">{{ __('أدور على عقار') }}</a>
            </div>
        </div>
        <div class="hidden lg:flex flex-col gap-4 absolute end-10 top-1/2 -translate-y-[60%] w-60" aria-hidden="true">
            <div style="--d:0s" class="hero-float rounded-2xl bg-white/12 backdrop-blur-md ring-1 ring-white/25 p-4"><p class="text-[11px] text-white/70">{{ __('عقار متاح الآن') }}</p><p class="text-2xl font-extrabold mt-1" dir="ltr">{{ number_format($total) }}</p></div>
            <div style="--d:1.2s" class="hero-float rounded-2xl bg-white/12 backdrop-blur-md ring-1 ring-white/25 p-4 ms-8"><p class="text-[11px] text-white/70">{{ __('متوسط الإيجار الشهري') }}</p><p class="text-2xl font-extrabold mt-1 text-brand" dir="ltr">{{ number_format($price['avg']) }} <span class="text-xs font-semibold text-white/70">{{ __('ج.م') }}</span></p></div>
        </div>
    </div>

    @if (count($hero))
    <div class="absolute inset-x-0 bottom-[170px] lg:bottom-[160px] z-10">
        <div class="{{ $wrap }} flex items-center justify-between gap-4">
            <div class="flex items-center gap-2" role="tablist">
                @foreach ($hero as $i => $h)
                    <button type="button" class="hero-dot {{ $i === 0 ? 'is-active' : '' }}" aria-label="{{ __('الصورة :n', ['n' => $i + 1]) }}"></button>
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
    <form action="{{ route('properties.index') }}" method="GET" data-live-count="{{ route('properties.count') }}" class="sb rounded-[1.75rem] shadow-2xl shadow-black/25 ring-1 ring-black/5 p-4 sm:p-6 lg:p-7">
        <div class="flex flex-wrap items-center justify-between gap-3 px-1 pb-5">
            <div class="flex items-center gap-3"><span class="w-10 h-10 rounded-xl bg-ink text-brand flex items-center justify-center"><x-icon name="search" class="w-5 h-5" stroke="2" /></span>
                <div><h2 class="text-lg font-extrabold text-ink leading-tight">{{ __('دوّر على عقارك') }}</h2><p class="text-xs text-slate-500">{{ __('فلتر وشوف النتيجة قدامك لحظة بلحظة') }}</p></div></div>
            <div class="flex flex-wrap gap-2" role="group" aria-label="{{ __('اختصارات') }}">
                @foreach ($govQuick ?? [] as $g => $n)<button type="button" class="sb-chip" data-chip="governorate" data-value="{{ $g }}">{{ __($g) }}</button>@endforeach
            </div>
        </div>

        <div class="grid lg:grid-cols-12 gap-x-6 gap-y-6">
            {{-- 1) المكان --}}
            <fieldset class="lg:col-span-5 sb-group">
                <legend class="sb-step"><b>1</b>{{ __('المكان') }}</legend>
                <div class="grid sm:grid-cols-5 gap-3">
                    <label class="sb-field sm:col-span-3"><x-icon name="search" class="w-5 h-5" /><span class="min-w-0 flex-1"><small>{{ __('ابحث بالمنطقة أو الاسم') }}</small><input name="q" autocomplete="off" placeholder="{{ __('مثال: مدينة نصر، المنصورة…') }}"></span></label>
                    <label class="sb-field sm:col-span-2"><x-icon name="pin" class="w-5 h-5" /><span class="min-w-0 flex-1"><small>{{ __('المحافظة') }}</small><select name="governorate"><option value="">{{ __('كل المحافظات') }}</option>@foreach (catalog('governorates') as $g)<option value="{{ $g }}">{{ __($g) }}</option>@endforeach</select></span></label>
                </div>
            </fieldset>
            {{-- 2) العقار --}}
            <fieldset class="lg:col-span-7 sb-group">
                <legend class="sb-step"><b>2</b>{{ __('العقار') }}</legend>
                <div class="grid sm:grid-cols-3 gap-3">
                    <label class="sb-field"><x-icon name="building" class="w-5 h-5" /><span class="min-w-0 flex-1"><small>{{ __('نوع العقار') }}</small><select name="type"><option value="">{{ __('الكل') }}</option>@foreach (catalog('property_types') as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></span></label>
                    <label class="sb-field"><x-icon name="layers" class="w-5 h-5" /><span class="min-w-0 flex-1"><small>{{ __('مرحلة التشطيب') }}</small><select name="stage"><option value="">{{ __('الكل') }}</option>@foreach ($stages as $k => $v)<option value="{{ $k }}">{{ $v['label'] }}</option>@endforeach</select></span></label>
                    <label class="sb-field"><x-icon name="target" class="w-5 h-5" /><span class="min-w-0 flex-1"><small>{{ __('الغرض') }}</small><select name="purpose"><option value="">{{ __('الكل') }}</option>@foreach (catalog('purposes') as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></span></label>
                </div>
            </fieldset>
            {{-- 3) السعر والمدة --}}
            <fieldset class="lg:col-span-12 sb-group">
                <legend class="sb-step"><b>3</b>{{ __('السعر والمدة') }}</legend>
                <div class="grid lg:grid-cols-12 gap-5 lg:gap-8 items-end">
                    <div class="lg:col-span-6">@include('partials.price-range', ['price' => $price])</div>
                    <label class="sb-field lg:col-span-3"><x-icon name="calendar" class="w-5 h-5" /><span class="min-w-0 flex-1"><small>{{ __('مدة العقد (سنين)') }}</small><input type="number" name="years" min="1" placeholder="{{ __('مثال: 5') }}"></span></label>
                    <div class="lg:col-span-3"><button class="sb-go"><span>{{ __('عرض') }}</span><b data-count-out>{{ $total }}</b><span>{{ __('عقار') }}</span><x-icon name="arrow-right" class="w-5 h-5" stroke="2.2" /></button></div>
                </div>
            </fieldset>
        </div>
    </form>
</div>

{{-- ============================== STATS ============================== --}}
<section class="{{ $wrap }} pt-16">
    <dl class="grid grid-cols-2 lg:grid-cols-4 gap-y-10 text-center">
        @foreach ([[$stats['properties'], __('عقار معروض')], [$stats['governorates'], __('محافظة')], [$stats['owners'], __('مالك مسجّل')], [$stats['tenants'], __('مستأجر مسجّل')]] as $i => [$n, $label])
            <div class="px-4 {{ $i > 0 ? 'lg:border-s border-slate-200 dark:border-slate-700' : '' }}">
                <dt class="text-3xl sm:text-4xl font-extrabold text-ink" dir="ltr"><span data-count="{{ $n }}">0</span><span class="text-brand">+</span></dt>
                <dd class="mt-3 text-base text-slate-500">{{ $label }}</dd>
            </div>
        @endforeach
    </dl>
</section>

{{-- ============================== ABOUT ============================== --}}
<section class="reveal {{ $wrap }} py-20">
    <div class="grid lg:grid-cols-2 gap-16 xl:gap-24 items-center">
        <div class="relative">
            <div class="absolute -top-5 -start-5 w-2/3 h-2/3 rounded-3xl border-2 border-brand/60" aria-hidden="true"></div>
            <img src="{{ $aboutImages[0] ?? asset('images/placeholder.svg') }}" alt="" loading="lazy" class="relative w-full h-[380px] sm:h-[500px] object-cover rounded-3xl shadow-2xl">
            @if (isset($aboutImages[1]))
                <img src="{{ $aboutImages[1] }}" alt="" loading="lazy" class="hidden sm:block absolute -bottom-10 -end-8 w-56 h-56 object-cover rounded-3xl ring-8 ring-white dark:ring-slate-900 shadow-xl">
            @endif
            <div class="absolute bottom-8 start-8 bg-ink text-white rounded-2xl px-6 py-5 shadow-xl">
                <p class="text-3xl font-extrabold text-brand" dir="ltr">{{ $ownerRate + $tenantRate }}%</p>
                <p class="text-xs text-white/70 mt-1">{{ __('إجمالي نسبة المنصة بس') }}</p>
            </div>
        </div>

        <div>
            <p class="eyebrow">{{ __('عن المنصة') }}</p>
            <h2 class="mt-4 text-2xl sm:text-3xl font-extrabold text-ink leading-tight">{{ __('عقارات واقفة من سنين، وناس محتاجة مكان.') }}</h2>
            <span class="bar mt-6"></span>
            <p class="mt-8 text-base text-slate-600 leading-9">{{ __('كتير من المباني والشقق فاضية من سنين من غير ما حد يستفيد منها، وفي نفس الوقت كتير ناس مستعدة تكمّل التشطيب وتستأجر لسنين طويلة لو السعر مناسب. «بيتك في ايدك» هي الوسيط اللي بيجمع الطرفين بشروط واضحة.') }}</p>
            <p class="mt-5 text-base text-slate-600 leading-9">{{ __('المالك بيعرض عقاره بكل تفاصيله، والمستأجر بيشوف بالظبط إيه اللي موجود وإيه اللي ناقص، والاتفاق على السعر والمدة بيتم جوه المنصة وبعقد.') }}</p>
            <ul class="mt-8 grid sm:grid-cols-2 gap-x-8 gap-y-4 text-base text-slate-700">
                @foreach ([__('عنوان بالتفصيل ومرحلة تشطيب واضحة'), __('جدول بنود التشطيب: تم / جزئي / ناقص'), __('تفاوض على الإيجار والمدة'), __('بيانات التواصل بتتفتح بعد الاتفاق')] as $point)
                    <li class="flex items-start gap-3"><span class="mt-3 w-2 h-2 rounded-full bg-brand shrink-0"></span>{{ $point }}</li>
                @endforeach
            </ul>
            <a href="{{ route('how') }}" class="btn btn-primary mt-10 !min-h-[46px] !px-10 !text-sm">{{ __('كيف تعمل المنصة') }}</a>
        </div>
    </div>
</section>

{{-- ============================== المراحل ============================== --}}
<section class="bg-slate-50 dark:bg-slate-900/40 py-20">
    <div class="{{ $wrap }}">
        <div class="reveal flex flex-wrap items-end justify-between gap-6 mb-10">
            <div class="max-w-2xl">
                <p class="eyebrow">{{ __('مراحل التشطيب') }}</p>
                <h2 class="mt-3 text-2xl sm:text-3xl font-extrabold text-ink">{{ __('ابدأ من المرحلة اللي تناسبك') }}</h2>
                <span class="bar mt-6"></span>
                <p class="mt-6 text-base text-slate-600 leading-8">{{ __('من الهيكل الخرساني لحد العقار المفروش. اختار الشغل اللي هتعمله بإيدك، والإيجار بيقل كل ما التشطيب الناقص يزيد.') }}</p>
            </div>
            <a href="{{ route('properties.index') }}" class="btn btn-ghost !min-h-[44px] !px-8">{{ __('كل العقارات') }}</a>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-3 auto-rows-[240px] sm:auto-rows-[320px] gap-5">
            @foreach ($stages as $key => $st)
                <a href="{{ route('properties.index', ['stage' => $key]) }}" style="--d:{{ $loop->index * 0.07 }}s"
                   class="reveal group relative rounded-3xl overflow-hidden bg-ink ">
                    @if ($stageImages[$key] ?? null)
                        <img src="{{ $stageImages[$key] }}" alt="{{ __($st['label']) }}" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-[1400ms] ease-out">
                    @endif
                    <span class="absolute inset-0 bg-gradient-to-t from-ink/85 via-ink/20 to-transparent"></span>
                    <span class="absolute inset-0 rounded-3xl ring-1 ring-inset ring-white/10 group-hover:ring-brand/80 transition"></span>
                    <span class="absolute bottom-0 inset-x-0 p-7 text-white">
                        <span class="block text-xs text-brand font-semibold tracking-wide">{{ __('المرحلة :n', ['n' => $loop->iteration]) }}</span>
                        <span class="block mt-1 text-lg sm:text-xl font-bold">{{ __($st['label']) }}</span>
                        <span class="mt-2 flex items-center justify-between text-sm text-white/75"><span>{{ __($st['hint']) }}</span><span class="text-brand font-semibold shrink-0 ms-3">{{ __(':n عقار', ['n' => $stageCounts[$key] ?? 0]) }}</span></span>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================== المحافظات ============================== --}}
<section class="{{ $wrap }} py-20">
    <div class="reveal text-center max-w-3xl mx-auto mb-12">
        <p class="eyebrow">{{ __('من كل ربوع مصر') }}</p>
        <h2 class="mt-3 text-2xl sm:text-3xl font-extrabold text-ink">{{ __('عقارات معطلة من الإسكندرية لأسوان') }}</h2>
        <span class="bar mx-auto mt-6"></span>
        <p class="mt-6 text-base text-slate-600 leading-8">{{ __('اختار محافظتك وشوف العقارات المتاحة فيها.') }}</p>
    </div>
    @php($spans = ['col-span-2 lg:row-span-2', '', '', '', ''])
    <div class="grid grid-cols-2 lg:grid-cols-4 auto-rows-[220px] sm:auto-rows-[270px] gap-5">
        @foreach ($govImages as $gov => $img)
            <a href="{{ route('properties.index', ['governorate' => $gov]) }}" style="--d:{{ $loop->index * 0.07 }}s"
               class="reveal group relative rounded-3xl overflow-hidden bg-ink {{ $spans[$loop->index] ?? '' }}">
                @if ($img)<img src="{{ $img }}" alt="{{ __($gov) }}" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-[1400ms] ease-out">@endif
                <span class="absolute inset-0 bg-gradient-to-t from-ink/85 via-ink/20 to-transparent"></span>
                <span class="absolute inset-0 rounded-3xl ring-1 ring-inset ring-white/10 group-hover:ring-brand/80 transition"></span>
                <span class="absolute bottom-0 inset-x-0 p-6 sm:p-7 text-white">
                    <span class="block text-lg sm:text-xl font-bold">{{ __($gov) }}</span>
                    <span class="block mt-1 text-sm text-brand">{{ __(':n عقار', ['n' => $govCounts[$gov]]) }}</span>
                </span>
            </a>
        @endforeach
    </div>
    @if ($govCounts->count() > 5)
        <div class="reveal mt-10 flex flex-wrap justify-center gap-3">
            @foreach ($govCounts->skip(5) as $gov => $count)
                <a href="{{ route('properties.index', ['governorate' => $gov]) }}" class="btn btn-ghost !rounded-full">{{ __($gov) }} <span class="text-xs text-slate-400">{{ $count }}</span></a>
            @endforeach
        </div>
    @endif
</section>

{{-- ============================== عقارات مميزة ============================== --}}
@if ($featured->count())
<section class="bg-slate-50 dark:bg-slate-900/40 py-20">
    <div class="{{ $wrap }}">
        <div class="reveal flex flex-wrap items-end justify-between gap-6 mb-12">
            <div>
                <p class="eyebrow">{{ __('اختياراتنا') }}</p>
                <h2 class="mt-3 text-2xl sm:text-3xl font-extrabold text-ink">{{ __('عقارات مميزة') }}</h2>
                <span class="bar mt-6"></span>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" data-snap-prev="featuredRow" class="h-12 px-6 rounded-full border border-slate-300 bg-white text-sm font-semibold text-ink hover:bg-ink hover:text-white hover:border-ink transition dark:bg-slate-800 dark:border-slate-600">{{ __('السابق') }}</button>
                <button type="button" data-snap-next="featuredRow" class="h-12 px-6 rounded-full border border-slate-300 bg-white text-sm font-semibold text-ink hover:bg-ink hover:text-white hover:border-ink transition dark:bg-slate-800 dark:border-slate-600">{{ __('التالي') }}</button>
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
<section class="{{ $wrap }} py-20">
    <div class="reveal relative overflow-hidden rounded-[2rem] hero-bg text-white px-8 py-16 sm:px-16 sm:py-20 grid lg:grid-cols-5 gap-10 items-center">
        <div class="lg:col-span-3">
            <p class="eyebrow !text-brand">{{ __('للملّاك') }}</p>
            <h2 class="mt-4 text-2xl sm:text-3xl font-extrabold leading-tight">{{ __('عندك مبنى أو شقة فاضية؟') }}</h2>
            <p class="mt-6 text-base text-white/75 leading-9 max-w-2xl">{{ __('بدل ما العقار يفضل مقفول، اعرضه مجاناً وخلّي مستأجر يكمّل تشطيبه ويدفعلك إيجار شهري لسنين. إنت اللي بتحدد السعر والمدة، وإحنا بنضمن حقك.') }}</p>
        </div>
        <div class="lg:col-span-2 flex flex-wrap gap-4 lg:justify-end">
            <a href="{{ route('register', ['role' => 'owner']) }}" class="btn btn-brand !min-h-[48px] !px-9 !text-sm">{{ __('اعرض عقارك دلوقتي') }}</a>
            <a href="{{ route('terms') }}" class="btn !min-h-[48px] !px-9 !text-sm border border-white/40 text-white hover:border-brand hover:text-brand">{{ __('اعرف نسبة المنصة') }}</a>
        </div>
    </div>
</section>

{{-- ============================== خطوات ============================== --}}
<section class="{{ $wrap }} pb-28">
    <div class="reveal text-center max-w-3xl mx-auto mb-12">
        <p class="eyebrow">{{ __('الخطوات') }}</p>
        <h2 class="mt-3 text-2xl sm:text-3xl font-extrabold text-ink">{{ __('إزاي بتشتغل المنصة؟') }}</h2>
        <span class="bar mx-auto mt-6"></span>
    </div>
    <ol class="grid md:grid-cols-2 xl:grid-cols-4 gap-x-10 gap-y-14">
        @foreach ([
            [__('المالك يعرض العقار'), __('صور، عنوان بالتفصيل، وكل بند تشطيب حالته إيه: تم، جزئي، ولا ناقص.')],
            [__('المستأجر يختار ويقدّم'), __('يحدد الغرض (سكن، شركة…)، الحالة الاجتماعية، والإيجار والمدة اللي تناسبه.')],
            [__('تفاوض وتوافق'), __('عرض وعرض مضاد على السعر والمدة لحد ما الطرفين يتفقوا.')],
            [__('عقد وبيانات تواصل'), __('بعد سداد نسبة المنصة بتتفتح بيانات التواصل الكاملة والعنوان.')],
        ] as $i => [$t, $d])
            <li class="reveal relative border-t-2 border-ink/10 dark:border-white/10 pt-8" style="--d:{{ $i * 0.1 }}s">
                <span class="block text-3xl font-extrabold text-brand/40 leading-none" dir="ltr">0{{ $i + 1 }}</span>
                <h3 class="mt-6 text-base font-bold text-ink">{{ $t }}</h3>
                <p class="mt-3 text-base text-slate-600 leading-8">{{ $d }}</p>
            </li>
        @endforeach
    </ol>
</section>

{{-- ============================== WHY US ============================== --}}
<section class="bg-ink text-white py-20">
    <div class="{{ $wrap }}">
        <div class="reveal grid lg:grid-cols-3 gap-10 mb-12 items-end">
            <div class="lg:col-span-2">
                <p class="eyebrow !text-brand">{{ __('ليه بيتك في ايدك') }}</p>
                <h2 class="mt-3 text-2xl sm:text-3xl font-extrabold leading-tight">{{ __('اتفاق واضح من أول يوم لآخر يوم.') }}</h2>
            </div>
            <p class="text-base text-white/70 leading-9">{{ __('مفيش وسيط بيخبّي حاجة. كل بند في العقار معروف، وكل نسبة معروفة، وكل خطوة بتتسجل.') }}</p>
        </div>
        <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-6">
            @foreach ([
                [__('تشطيب شفاف'), __('جدول بنود التشطيب لكل عقار: الكهرباء، السباكة، المحارة، السيراميك… كل بند «تم / جزئي / ناقص».')],
                [__('نسبة معلنة'), __('نسبة المنصة :owner% على المالك و:tenant% على المستأجر من إجمالي العقد. بتتثبت وقت الاتفاق ومبتتغيرش.', ['owner' => $ownerRate, 'tenant' => $tenantRate])],
                [__('تواصل محمي'), __('أرقام التليفون والإيميلات مخفية في الإعلانات والرسائل لحد ما العقد يتم، عشان حق الطرفين.')],
                [__('عقود طويلة'), __('مدة إيجار من سنة لـ 30 سنة، مع فترة سماح وزيادة سنوية متفق عليها من الأول.')],
            ] as $i => [$t, $d])
                <div class="reveal rounded-3xl border border-white/10 bg-white/5 p-8 hover:border-brand/60 transition" style="--d:{{ $i * 0.08 }}s">
                    <h3 class="text-base font-bold text-brand">{{ $t }}</h3>
                    <p class="mt-4 text-white/70 leading-8">{{ $d }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================== أحدث العقارات ============================== --}}
@if ($latest->count())
<section class="{{ $wrap }} py-20">
    <div class="reveal flex flex-wrap items-end justify-between gap-6 mb-12">
        <div>
            <p class="eyebrow">{{ __('جديد على المنصة') }}</p>
            <h2 class="mt-3 text-2xl sm:text-3xl font-extrabold text-ink">{{ __('أحدث العقارات') }}</h2>
            <span class="bar mt-6"></span>
        </div>
        <a href="{{ route('properties.index') }}" class="btn btn-ghost !min-h-[44px] !px-8">{{ __('عرض الكل') }}</a>
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
        <h2 class="reveal text-2xl sm:text-3xl font-extrabold text-ink mb-8">{{ __('تصفح حسب النوع') }}</h2>
        <div class="reveal flex flex-wrap gap-3">
            @foreach (catalog('property_types') as $k => $label)
                <a href="{{ route('properties.index', ['type' => $k]) }}" class="btn btn-ghost !rounded-full !px-7">{{ __($label) }} <span class="text-xs text-slate-400">{{ $typeCounts[$k] ?? 0 }}</span></a>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================== FAQ ============================== --}}
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-10 py-20">
    <div class="reveal text-center mb-10">
        <p class="eyebrow">{{ __('أسئلة شائعة') }}</p>
        <h2 class="mt-3 text-2xl sm:text-3xl font-extrabold text-ink">{{ __('محتاج تعرف إيه؟') }}</h2>
        <span class="bar mx-auto mt-6"></span>
    </div>
    <div class="space-y-4">
        @foreach ([
            [__('هل عرض العقار بفلوس؟'), __('عرض العقار على المنصة مجاني. المنصة بتاخد نسبة بس لما الاتفاق يتم: :owner% من المالك و:tenant% من المستأجر من إجمالي قيمة العقد.', ['owner' => $ownerRate, 'tenant' => $tenantRate])],
            [__('إزاي المالك بيحدد السعر؟'), __('بتكتب سعر السوق الحالي للعقار وسعر الإيجار اللي عايز تعرضه بيه، وتحدد أقل وأقصى مدة للعقد وفترة السماح والزيادة السنوية.')],
            [__('مين بيكمّل التشطيب الناقص؟'), __('ده بيتحدد في الإعلان: ممكن المالك أو المستأجر أو الاتنين بالاشتراك، وكل بند ناقص مكتوب بالتفصيل في جدول التشطيب.')],
            [__('إمتى بشوف رقم المالك أو عنوان العقار بالتفصيل؟'), __('بعد ما الاتفاق يتم ونسبة المنصة تتسدد من الطرفين. قبل كده التواصل بيكون جوه المنصة، وأي رقم في الرسائل بيتخفى.')],
            [__('ينفع أقارن بين أكتر من عقار؟'), __('أكيد. أضف أي عقار للمقارنة وشوف السعر والمساحة ومرحلة التشطيب جنب بعض. وكمان تقدر تحفظ المفضلة من حسابك كمستأجر.')],
        ] as $i => [$q, $a])
            <details class="reveal group rounded-2xl border border-slate-200 dark:border-slate-700 open:border-brand open:shadow-lg bg-white dark:bg-slate-900 transition" style="--d:{{ $i * 0.05 }}s" @if($i === 0) open @endif>
                <summary class="flex items-center justify-between gap-6 px-7 py-6 text-base font-bold text-ink">
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
                <p class="eyebrow !text-brand">{{ __('تواصل معنا') }}</p>
                <h2 class="mt-4 text-2xl sm:text-3xl font-extrabold leading-tight">{{ __('عندك سؤال؟ ابعتلنا.') }}</h2>
                <p class="mt-5 text-white/70 leading-8">{{ __('فريقنا بيرد عليك في أسرع وقت، سواء كنت مالك عايز تعرض عقارك أو مستأجر بيدور.') }}</p>
            </div>
            <dl class="space-y-5 text-base">
                <div><dt class="text-xs text-white/50">{{ __('الموبايل') }}</dt><dd class="mt-1 font-semibold" dir="ltr"><a href="tel:{{ preg_replace('/\s+/', '', config('site.phone')) }}" class="hover:text-brand">{{ config('site.phone') }}</a></dd></div>
                <div><dt class="text-xs text-white/50">{{ __('الإيميل') }}</dt><dd class="mt-1 font-semibold"><a href="mailto:{{ config('site.email') }}" class="hover:text-brand">{{ config('site.email') }}</a></dd></div>
                <div><dt class="text-xs text-white/50">{{ __('واتساب') }}</dt><dd class="mt-1 font-semibold"><a href="https://wa.me/{{ config('site.whatsapp') }}" target="_blank" rel="noopener" class="hover:text-brand">{{ __('ابدأ محادثة') }}</a></dd></div>
            </dl>
        </div>
        <form method="POST" action="{{ route('contact.send') }}" class="lg:col-span-3 p-10 sm:p-14 bg-white dark:bg-slate-900 text-slate-800 space-y-5">
            @csrf
            <div class="grid sm:grid-cols-2 gap-5">
                <div><label class="label">{{ __('الاسم *') }}</label><input name="name" value="{{ old('name') }}" class="field" required></div>
                <div><label class="label">{{ __('الموبايل') }}</label><input name="phone" value="{{ old('phone') }}" class="field" dir="ltr"></div>
            </div>
            <div><label class="label">{{ __('الإيميل') }}</label><input type="email" name="email" value="{{ old('email') }}" class="field" dir="ltr"></div>
            <div><label class="label">{{ __('رسالتك *') }}</label><textarea name="message" rows="5" class="field" required>{{ old('message') }}</textarea></div>
            <button class="btn btn-primary !min-h-[46px] !px-12 !text-sm">{{ __('إرسال الرسالة') }}</button>
        </form>
    </div>
</section>
@endsection
