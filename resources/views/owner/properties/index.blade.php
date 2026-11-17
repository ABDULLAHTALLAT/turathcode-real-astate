@extends('layouts.dashboard')
@section('title', 'عقاراتي')
@section('panel')
<div class="flex items-center justify-between"><h1 class="text-xl font-extrabold text-ink">عقاراتي</h1><a href="{{ route('owner.properties.create') }}" class="btn btn-brand"><i class="bi bi-plus-lg"></i> أضف عقار</a></div>
<div class="card divide-y divide-slate-100 overflow-hidden">
    @forelse ($properties as $p)
        <div class="p-4 flex flex-col sm:flex-row gap-4 sm:items-center">
            <img src="{{ $p->cover_url }}" class="w-full sm:w-28 h-24 rounded-xl object-cover shrink-0" alt="">
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2"><a href="{{ route('properties.show', $p) }}" class="font-bold text-ink hover:text-brand-dark">{{ $p->title }}</a>
                    @include('partials.badge', ['color' => $p->status_color, 'text' => $p->status_label])</div>
                <p class="text-xs text-slate-500 mt-1">{{ $p->city }}، {{ $p->governorate }} · {{ $p->stage_label }} · {{ egp($p->offered_rent) }} / شهر · {{ $p->views }} مشاهدة · {{ $p->requests_count }} طلب</p>
                @if ($p->status === 'rejected' && $p->reject_reason)<p class="text-xs text-rose-600 mt-1">سبب الرفض: {{ $p->reject_reason }}</p>@endif
                @if ($p->status === 'pending')<p class="text-xs text-amber-600 mt-1">بانتظار مراجعة الإدارة.</p>@endif
            </div>
            <div class="flex flex-wrap gap-2">
                @if (in_array($p->status, ['approved', 'hidden']))
                    <form method="POST" action="{{ route('owner.properties.toggle', $p) }}">@csrf<button class="btn btn-ghost !py-1.5 !text-xs">{{ $p->status === 'approved' ? 'إخفاء' : 'إظهار' }}</button></form>
                @endif
                @if ($p->status !== 'rented')<a href="{{ route('owner.properties.edit', $p) }}" class="btn btn-ghost !py-1.5 !text-xs"><i class="bi bi-pencil"></i> تعديل</a>@endif
                <form method="POST" action="{{ route('owner.properties.destroy', $p) }}" onsubmit="return confirm('حذف العقار نهائياً؟')">@csrf @method('DELETE')<button class="btn btn-ghost !py-1.5 !text-xs !text-rose-600"><i class="bi bi-trash"></i></button></form>
            </div>
        </div>
    @empty
        <div class="p-12 text-center text-sm text-slate-500">لسه ما أضفتش عقارات.</div>
    @endforelse
</div>
{{ $properties->links() }}
@endsection
