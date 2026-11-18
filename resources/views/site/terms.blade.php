@extends('layouts.app')
@section('title', __('الشروط ونسبة المنصة'))
@section('content')
<section class="bg-ink text-white"><div class="max-w-4xl mx-auto px-4 py-12"><h1 class="text-3xl font-extrabold">{{ __('الشروط ونسبة المنصة') }}</h1></div></section>
<div class="max-w-4xl mx-auto px-4 py-12 space-y-6 text-sm text-slate-600 leading-8">
    <div class="grid sm:grid-cols-2 gap-4">
        <div class="card p-6 text-center"><p class="text-xs text-slate-500">{{ __('نسبة المنصة من المالك') }}</p><p class="text-4xl font-extrabold text-brand-dark">{{ rtrim(rtrim(number_format($owner, 2), '0'), '.') }}%</p><p class="text-xs text-slate-500">{{ __('من إجمالي قيمة العقد') }}</p></div>
        <div class="card p-6 text-center"><p class="text-xs text-slate-500">{{ __('نسبة المنصة من المستأجر') }}</p><p class="text-4xl font-extrabold text-brand-dark">{{ rtrim(rtrim(number_format($tenant, 2), '0'), '.') }}%</p><p class="text-xs text-slate-500">{{ __('من إجمالي قيمة العقد') }}</p></div>
    </div>
    <ol class="list-decimal ps-6 space-y-3">
        <li><b class="text-ink">{{ __('إجمالي قيمة العقد') }}</b> {{ __('= الإيجار الشهري المتفق عليه × 12 × عدد سنين العقد.') }}</li>
        <li>{{ __('تُحسب نسبة المنصة من الطرفين') }} <b class="text-ink">{{ __('لحظة الاتفاق') }}</b> {{ __('وتُثبَّت بنفس النسبة السارية وقتها حتى لو اتغيّرت النسب بعد كده.') }}</li>
        <li>{{ __('بيانات التواصل الكاملة (الموبايل، الإيميل، العنوان التفصيلي)') }} <b class="text-ink">{{ __('لا تظهر') }}</b> {{ __('إلا بعد سداد النسبة من الطرفين.') }}</li>
        <li>{{ __('يُمنع نشر أرقام هواتف أو إيميلات أو روابط تواصل في الإعلانات والرسائل، وتُخفى تلقائياً.') }}</li>
        <li>{{ __('أي اتفاق تم التعرف على الطرفين من خلال المنصة تستحق عنه النسبة، حتى لو تم تحرير العقد خارجها.') }}</li>
        <li>{{ __('للمنصة حق مراجعة الإعلانات ورفض أو إيقاف أي إعلان أو حساب مخالف.') }}</li>
        <li>{{ __('الإعلان مسؤولية المالك، ويلتزم بصحة بيانات العقار وحالة التشطيب وملكيته أو تفويضه في التأجير.') }}</li>
    </ol>
</div>
@endsection
