@extends('layouts.app')
@section('title', 'كيف تعمل المنصة')
@section('content')
<section class="bg-ink text-white"><div class="max-w-4xl mx-auto px-4 py-12"><h1 class="text-3xl font-extrabold">كيف تعمل «بيتك في ايدك»؟</h1><p class="text-white/70 mt-3 leading-8">فكرتنا بسيطة: كتير من المباني والشقق فاضية من سنين، وكتير ناس مستعدة تكمّل تشطيبها وتستأجرها لسنين طويلة لو السعر مناسب. إحنا الوسيط اللي بيضمن حق الطرفين.</p></div></section>
<div class="max-w-4xl mx-auto px-4 py-12 space-y-6">
    @foreach ([
        ['للمالك', 'bi-building', ['سجّل كمالك وأضف عقارك: صور، عنوان بالتفصيل، المساحة، والمرافق.', 'حدد مرحلة التشطيب (هيكل، طوب، محارة…) وحالة كل بند: تم / جزئي / ناقص.', 'حدد السعر الحالي في السوق (مثلاً 20 ألف) والسعر اللي هتأجره بيه (مثلاً 15 ألف) والمدة المناسبة ليك.', 'الإدارة بتراجع الإعلان وتنشره.', 'بتوصلك طلبات الإيجار: توافق، ترفض، أو تفاوض بعرض مضاد.']],
        ['للمستأجر', 'bi-person-check', ['سجّل وكمّل بياناتك: الحالة الاجتماعية، الغرض من العقار، الميزانية.', 'دوّر بالفلاتر وقارن بين العقارات واحفظ المفضلة.', 'قدّم طلب بإيجارك المقترح ومدة العقد.', 'تفاوض مع المالك لحد ما توصلوا لاتفاق.']],
        ['إتمام الاتفاق وحقوق المنصة', 'bi-shield-check', ['بعد الاتفاق على السعر والمدة، المنصة بتحسب إجمالي العقد ونسبتها من الطرفين.', 'بعد سداد النسبة من المالك والمستأجر بتتفتح بيانات التواصل والعنوان الكامل ويتحول العقار لـ «تم التأجير».', 'لحد ذلك، أرقام التليفون والإيميلات بتتحجب من الرسائل والوصف عشان التواصل يفضل جوه المنصة.']],
    ] as [$t, $ic, $steps])
        <section class="card p-7">
            <h2 class="text-xl font-extrabold text-ink flex items-center gap-3"><span class="w-11 h-11 rounded-xl brand-gradient text-ink flex items-center justify-center"></span> {{ $t }}</h2>
            <ol class="mt-5 space-y-3 text-sm text-slate-600 leading-7">
                @foreach ($steps as $i => $s)<li class="flex gap-3"><span class="shrink-0 w-6 h-6 rounded-full bg-brand-50 text-brand-dark text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</span>{{ $s }}</li>@endforeach
            </ol>
        </section>
    @endforeach
</div>
@endsection
