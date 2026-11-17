@extends('layouts.dashboard')
@section('title', 'الطلبات')
@section('panel')
@php($as = auth()->user()->isOwner() ? 'owner' : 'tenant')
<h1 class="text-xl font-extrabold text-ink">{{ $as === 'owner' ? 'الطلبات الواردة على عقاراتي' : 'طلباتي' }}</h1>
<div class="card divide-y divide-slate-100 overflow-hidden">
    @forelse ($requests as $r) @include('partials.request-row') @empty
        <div class="p-12 text-center text-sm text-slate-500">لا توجد طلبات حتى الآن.</div>
    @endforelse
</div>
{{ $requests->links() }}
@endsection
