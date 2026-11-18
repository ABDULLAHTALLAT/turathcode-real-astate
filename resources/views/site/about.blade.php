@extends('layouts.app')
@section('title', __('من نحن'))
@section('content')
<section class="bg-ink text-white"><div class="max-w-4xl mx-auto px-4 py-12"><h1 class="text-3xl font-extrabold">{{ __('من نحن') }}</h1></div></section>
<div class="max-w-4xl mx-auto px-4 py-12 space-y-5 text-slate-600 leading-9">
    <p><b class="text-ink">{{ __('«بيتك في ايدك»') }}</b> {{ __('منصة مصرية من تطوير') }} <b class="text-brand-dark">Turath Code</b>{{ __('، هدفها إحياء المباني والشقق المعطلة اللي واقفة من سنين من غير استخدام، وتحويلها لبيوت ومكاتب ومشاريع شغالة.') }}</p>
    <p>{{ __('بنربط المالك اللي عنده عقار فاضي بمستأجر مستعد يكمّل التشطيب ويستأجر لمدة طويلة. المالك بيكسب إيجار منتظم، والمستأجر بياخد سعر أقل وعقد طويل، والبلد بتكسب عقار رجع للحياة.') }}</p>
    <p>{{ __('بنوضح كل حاجة من الأول: عنوان بالتفصيل، مرحلة التشطيب، وحالة كل بند. ومفيش حاجة مخفية.') }}</p>
</div>
@endsection
