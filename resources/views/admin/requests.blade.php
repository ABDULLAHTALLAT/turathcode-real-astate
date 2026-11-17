@extends('layouts.dashboard')
@section('title', 'الطلبات والعمولات')
@section('panel')
<h1 class="text-xl font-extrabold text-ink">الطلبات والعمولات</h1>
<form class="flex gap-2"><select name="status" class="field !w-56"><option value="">كل الحالات</option>@foreach (catalog('request_statuses') as $k => $s)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $s['label'] }}</option>@endforeach</select><button class="btn btn-primary">فلترة</button></form>
<div class="card overflow-x-auto"><table class="w-full text-sm min-w-[760px]">
    <thead class="text-xs text-slate-500 bg-slate-50 dark:bg-slate-800/50"><tr><th class="text-start p-3">الطلب</th><th class="text-start p-3">العقار</th><th class="text-start p-3">الأطراف</th><th class="text-start p-3">القيمة</th><th class="text-start p-3">نسبة المنصة</th><th class="text-start p-3">الحالة</th></tr></thead>
    <tbody class="divide-y divide-slate-100">@forelse ($requests as $r)
        <tr><td class="p-3"><a href="{{ route('requests.show', $r) }}" class="font-bold text-brand-dark" dir="ltr">{{ $r->code }}</a></td>
            <td class="p-3 max-w-[200px] truncate">{{ $r->property->title }}</td>
            <td class="p-3 text-xs">م: {{ $r->owner->name }}<br>ج: {{ $r->tenant->name }}</td>
            <td class="p-3 text-xs">{{ egp($r->proposed_rent) }} × {{ $r->lease_years }} سنة<br><b>{{ egp($r->total_value ?? $r->currentTotal()) }}</b></td>
            <td class="p-3 text-xs">@if ($r->owner_commission !== null){{ egp($r->platform_earnings) }}<br><span class="text-slate-400">محصّل {{ egp($r->paid_earnings) }}</span>@else — @endif</td>
            <td class="p-3">@include('partials.badge', ['color' => $r->status_color, 'text' => $r->status_label])</td></tr>
    @empty <tr><td colspan="6" class="p-10 text-center text-slate-500">لا توجد طلبات.</td></tr> @endforelse</tbody></table></div>
{{ $requests->links() }}
@endsection
