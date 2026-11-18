@extends('layouts.auth')
@section('title', __('تسجيل الدخول'))
@section('form')
<h1 class="text-2xl font-extrabold text-ink">{{ __('أهلاً بيك تاني 👋') }}</h1>
<p class="text-sm text-slate-500 mt-1 mb-6">{{ __('ادخل بالإيميل أو رقم الموبايل.') }}</p>
<form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf
    <div><label class="label">{{ __('الإيميل أو رقم الموبايل') }}</label><input name="login" value="{{ old('login') }}" class="field" required autofocus autocomplete="username"></div>
    <div><label class="label">{{ __('كلمة المرور') }}</label><div class="relative"><input id="pw" type="password" name="password" class="field pe-11" required autocomplete="current-password"><button type="button" class="absolute inset-y-0 end-0 px-3 text-slate-500" aria-label="{{ __('إظهار كلمة المرور') }}" onclick="var i=document.getElementById('pw');i.type=i.type==='password'?'text':'password'">{{ __('إظهار') }}</button></div></div>
    <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="remember" class="accent-sky-500"> {{ __('خليني فاكرني') }}</label>
    <button class="btn btn-primary w-full">{{ __('دخول') }}</button>
</form>
<p class="text-center text-sm text-slate-500 mt-6">{{ __('لسه معندكش حساب؟') }}
    <a href="{{ route('register', ['role' => 'tenant']) }}" class="text-brand-dark font-bold">{{ __('مستأجر') }}</a> {{ __('أو') }}
    <a href="{{ route('register', ['role' => 'owner']) }}" class="text-brand-dark font-bold">{{ __('مالك عقار') }}</a></p>
@endsection
