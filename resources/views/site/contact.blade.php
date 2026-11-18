@extends('layouts.app')
@section('title', __('تواصل معنا'))
@section('content')
<section class="bg-ink text-white"><div class="max-w-4xl mx-auto px-4 py-12"><h1 class="text-3xl font-extrabold">{{ __('تواصل معنا') }}</h1></div></section>
<div class="max-w-4xl mx-auto px-4 py-12 grid md:grid-cols-5 gap-8">
    <div class="md:col-span-2 space-y-4 text-sm">
        <a href="tel:{{ preg_replace('/\s+/', '', config('site.phone')) }}" class="card p-4 flex items-center gap-3 hover:border-brand"><span dir="ltr">{{ config('site.phone') }}</span></a>
        <a href="https://wa.me/{{ config('site.whatsapp') }}" target="_blank" class="card p-4 flex items-center gap-3 hover:border-brand">{{ __('واتساب') }}</a>
        <a href="mailto:{{ config('site.email') }}" class="card p-4 flex items-center gap-3 hover:border-brand">{{ config('site.email') }}</a>
    </div>
    <form method="POST" action="{{ route('contact.send') }}" class="md:col-span-3 card p-6 space-y-4">
        @csrf
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="label">{{ __('الاسم *') }}</label><input name="name" value="{{ old('name') }}" class="field" required></div>
            <div><label class="label">{{ __('الموبايل') }}</label><input name="phone" value="{{ old('phone') }}" class="field" dir="ltr"></div>
        </div>
        <div><label class="label">{{ __('الإيميل') }}</label><input type="email" name="email" value="{{ old('email') }}" class="field" dir="ltr"></div>
        <div><label class="label">{{ __('رسالتك *') }}</label><textarea name="message" rows="5" class="field" required>{{ old('message') }}</textarea></div>
        <button class="btn btn-primary">{{ __('إرسال') }}</button>
    </form>
</div>
@endsection
