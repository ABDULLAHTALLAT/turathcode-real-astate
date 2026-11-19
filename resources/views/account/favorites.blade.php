@extends('layouts.dashboard')
@section('title', 'المفضلة')
@section('panel')
<h1 class="text-xl font-extrabold text-ink inline-flex items-center gap-2"><x-icon name="heart" class="w-6 h-6 text-rose-500" />المفضلة</h1>
@if ($properties->isEmpty())
    <div class="card p-12 text-center text-sm text-slate-500"><p class="mt-3">لسه ما حفظتش أي عقار. <a href="{{ route('properties.index') }}" class="text-brand-dark font-bold">تصفح العقارات</a></p></div>
@else
    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">@foreach ($properties as $property) @include('partials.property-card') @endforeach</div>
@endif
@endsection
