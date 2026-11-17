@extends('layouts.auth')
@section('title', 'تسجيل الدخول')
@section('form')
<h1 class="text-2xl font-extrabold text-ink">أهلاً بيك تاني 👋</h1>
<p class="text-sm text-slate-500 mt-1 mb-6">ادخل بالإيميل أو رقم الموبايل.</p>
<form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf
    <div><label class="label">الإيميل أو رقم الموبايل</label><input name="login" value="{{ old('login') }}" class="field" required autofocus autocomplete="username"></div>
    <div><label class="label">كلمة المرور</label><div class="relative"><input id="pw" type="password" name="password" class="field pe-11" required autocomplete="current-password"><button type="button" class="absolute inset-y-0 end-0 px-3 text-slate-500" aria-label="إظهار كلمة المرور" onclick="var i=document.getElementById('pw');i.type=i.type==='password'?'text':'password'"><i class="bi bi-eye"></i></button></div></div>
    <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="remember" class="accent-sky-500"> خليني فاكرني</label>
    <button class="btn btn-primary w-full">دخول</button>
</form>
<p class="text-center text-sm text-slate-500 mt-6">لسه معندكش حساب؟
    <a href="{{ route('register', ['role' => 'tenant']) }}" class="text-brand-dark font-bold">مستأجر</a> أو
    <a href="{{ route('register', ['role' => 'owner']) }}" class="text-brand-dark font-bold">مالك عقار</a></p>
@endsection
