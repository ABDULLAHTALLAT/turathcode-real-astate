@extends('layouts.dashboard')
@section('title', 'إدارة العقارات')
@section('panel')
<h1 class="text-xl font-extrabold text-ink">العقارات</h1>
<form class="flex flex-wrap gap-2"><input name="q" value="{{ request('q') }}" class="field !w-60" placeholder="بحث بالعنوان">
    <select name="status" class="field !w-44"><option value="">كل الحالات</option>@foreach (catalog('property_statuses') as $k => $s)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $s['label'] }}</option>@endforeach</select><button class="btn btn-primary">فلترة</button></form>
<div class="card divide-y divide-slate-100 overflow-hidden">
    @forelse ($properties as $p)
        <div class="p-4 flex flex-col lg:flex-row gap-4 lg:items-center">
            <img src="{{ $p->cover_url }}" class="w-full lg:w-24 h-20 rounded-xl object-cover" alt="">
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap gap-2 items-center"><a href="{{ route('properties.show', $p) }}" class="font-bold text-ink hover:text-brand-dark">{{ $p->title }}</a>@include('partials.badge', ['color' => $p->status_color, 'text' => $p->status_label])@if ($p->is_featured)<span class="text-[11px] font-bold bg-brand text-ink px-2 py-0.5 rounded-full">مميز</span>@endif</div>
                <p class="text-xs text-slate-500 mt-1">المالك: {{ $p->owner->name }} · {{ $p->city }}، {{ $p->governorate }} · {{ egp($p->offered_rent) }} (السوق {{ egp($p->market_rent) }})</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ $p->full_address }}</p>
            </div>
            <div class="flex flex-wrap gap-2 items-center">
                @if ($p->status !== 'approved' && $p->status !== 'rented')<form method="POST" action="{{ route('admin.properties.approve', $p) }}">@csrf<button class="btn btn-brand !py-1.5 !text-xs">نشر</button></form>@endif
                @if ($p->status === 'pending' || $p->status === 'approved')
                    <form method="POST" action="{{ route('admin.properties.reject', $p) }}" class="flex gap-1">@csrf<input name="reason" required placeholder="سبب الرفض" class="field !py-1.5 !text-xs !w-32"><button class="btn btn-ghost !py-1.5 !text-xs !text-rose-600">رفض</button></form>
                @endif
                <form method="POST" action="{{ route('admin.properties.feature', $p) }}">@csrf<button class="btn btn-ghost !py-1.5 !text-xs" title="تمييز"><i class="bi bi-star{{ $p->is_featured ? '-fill text-amber-500' : '' }}"></i></button></form>
                <form method="POST" action="{{ route('admin.properties.destroy', $p) }}" onsubmit="return confirm('حذف العقار؟')">@csrf @method('DELETE')<button class="btn btn-ghost !py-1.5 !text-xs !text-rose-600"><i class="bi bi-trash"></i></button></form>
            </div>
        </div>
    @empty <div class="p-12 text-center text-sm text-slate-500">لا توجد عقارات.</div> @endforelse
</div>
{{ $properties->links() }}
@endsection
