@extends('layouts.app')
@section('title', __('مش مسموح لك تدخل هنا'))
@section('content')
<section class="max-w-xl mx-auto px-4 py-24 text-center">
    
    <p class="text-6xl font-extrabold text-ink mt-4">403</p>
    <h1 class="text-xl font-bold text-ink mt-2">{{ __('مش مسموح لك تدخل هنا') }}</h1>
    <p class="text-slate-500 mt-2">{{ __('الصفحة دي مخصصة لنوع حساب تاني.') }}</p>
    <div class="flex justify-center gap-3 mt-8"><a href="{{ route('home') }}" class="btn btn-primary">{{ __('الرئيسية') }}</a><a href="{{ route('properties.index') }}" class="btn btn-ghost">{{ __('تصفّح العقارات') }}</a></div>
</section>
@endsection
