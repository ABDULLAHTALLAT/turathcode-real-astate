@extends('layouts.auth')
@section('title', 'حساب جديد')
@section('form')
@php($r = old('role', $role))
<h1 class="text-2xl font-extrabold text-ink">إنشاء حساب جديد</h1>
<p class="text-sm text-slate-500 mt-1 mb-6">التسجيل مجاني. اختار نوع حسابك:</p>
<form method="POST" action="{{ route('register') }}" class="space-y-4">
    @csrf
    <div class="grid grid-cols-2 gap-3">
        @foreach (['tenant' => ['bi-search-heart', 'مستأجر', 'بدوّر على عقار'], 'owner' => ['bi-building', 'مالك عقار', 'عندي عقار معطل']] as $k => [$ic, $t, $s])
            <label class="cursor-pointer">
                <input type="radio" name="role" value="{{ $k }}" class="peer sr-only" @checked($r === $k)>
                <span class="block rounded-xl border-2 border-slate-200 p-4 text-center transition peer-checked:border-brand peer-checked:bg-brand-50 dark:border-slate-700 dark:peer-checked:bg-slate-800">
                    
                    <span class="block font-bold text-ink mt-1">{{ $t }}</span><span class="block text-[11px] text-slate-500">{{ $s }}</span>
                </span>
            </label>
        @endforeach
    </div>
    <div><label class="label">الاسم بالكامل *</label><input name="name" value="{{ old('name') }}" class="field" required></div>
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="label">رقم الموبايل *</label><input name="phone" value="{{ old('phone') }}" inputmode="numeric" maxlength="11" placeholder="01012345678" class="field" dir="ltr" required></div>
        <div><label class="label">الإيميل *</label><input type="email" name="email" value="{{ old('email') }}" class="field" dir="ltr" required></div>
    </div>
    <div><label class="label">المحافظة</label><select name="governorate" class="field"><option value="">—</option>@foreach (catalog('governorates') as $g)<option @selected(old('governorate') === $g)>{{ $g }}</option>@endforeach</select></div>
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="label">كلمة المرور *</label><input type="password" name="password" class="field" required autocomplete="new-password"></div>
        <div><label class="label">تأكيد كلمة المرور *</label><input type="password" name="password_confirmation" class="field" required autocomplete="new-password"></div>
    </div>
    <label class="flex gap-2 items-start text-xs text-slate-600 leading-6"><input type="checkbox" name="terms" value="1" class="mt-1 accent-sky-500" required>
        <span>أوافق على <a href="{{ route('terms') }}" target="_blank" class="text-brand-dark font-bold underline">الشروط وسياسة نسبة المنصة</a>.</span></label>
    <button class="btn btn-brand w-full">إنشاء الحساب</button>
</form>
<p class="text-center text-sm text-slate-500 mt-6">عندك حساب؟ <a href="{{ route('login') }}" class="text-brand-dark font-bold">سجّل دخولك</a></p>
@endsection
