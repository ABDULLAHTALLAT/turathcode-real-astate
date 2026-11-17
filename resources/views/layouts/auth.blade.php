@extends('layouts.app')
@section('content')
<section class="hero-bg min-h-[70vh] py-12 px-4">
    <div class="max-w-lg mx-auto">
        <div class="card p-7 sm:p-9 shadow-2xl shadow-black/30 rise">
            @yield('form')
        </div>
    </div>
</section>
@endsection
