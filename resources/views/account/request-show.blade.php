@extends('layouts.dashboard')
@section('title', 'طلب '.$rr->code)
@section('panel')
@php
    $me = auth()->user();
    $isOwner = $me->id === $rr->owner_id;
    $isTenant = $me->id === $rr->tenant_id;
    $p = $rr->property;
    $steps = ['pending' => 'تقديم الطلب', 'negotiating' => 'التفاوض', 'accepted' => 'الاتفاق', 'completed' => 'سداد النسبة وفتح التواصل'];
    $order = array_keys($steps);
    $cur = $rr->status === 'completed' ? 3 : ($rr->status === 'accepted' ? 2 : ($rr->status === 'negotiating' ? 1 : 0));
    $dead = in_array($rr->status, ['rejected', 'cancelled']);
@endphp

<div class="flex flex-wrap items-center justify-between gap-3">
    <div><p class="text-xs text-slate-400">طلب رقم</p><h1 class="text-xl font-extrabold text-ink" dir="ltr">{{ $rr->code }}</h1></div>
    @include('partials.badge', ['color' => $rr->status_color, 'text' => $rr->status_label])
</div>

{{-- الخطوات --}}
@if (! $dead)
<div class="card p-5"><ol class="grid grid-cols-4 gap-2 text-center">
    @foreach ($steps as $k => $label)
        @php($i = array_search($k, $order))
        <li><span class="mx-auto w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $i <= $cur ? 'brand-gradient text-ink' : 'bg-slate-100 text-slate-400 dark:bg-slate-800' }}">{{ $i < $cur ? '✓' : $i + 1 }}</span>
            <span class="block text-[11px] mt-1.5 {{ $i <= $cur ? 'text-ink font-semibold' : 'text-slate-400' }}">{{ $label }}</span></li>
    @endforeach
</ol></div>
@endif

{{-- العقار --}}
<a href="{{ route('properties.show', $p) }}" class="card p-4 flex gap-4 items-center hover:border-brand transition">
    <img src="{{ $p->cover_thumb }}" class="w-24 h-20 rounded-xl object-cover" alt="">
    <div><p class="font-bold text-ink">{{ $p->title }}</p><p class="text-xs text-slate-500 mt-1">{{ $p->public_address }}</p><p class="text-xs text-slate-500">السعر المعروض: {{ egp($p->offered_rent) }} / شهر</p></div>
</a>

<div class="grid md:grid-cols-2 gap-6">
    {{-- العرض الحالي --}}
    <section class="card p-6">
        <h2 class="font-bold text-ink mb-4">{{ $rr->status === 'accepted' || $rr->status === 'completed' ? 'الشروط المتفق عليها' : 'العرض الحالي' }}</h2>
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between"><dt class="text-slate-500">الإيجار الشهري</dt><dd class="font-extrabold text-ink">{{ egp($rr->proposed_rent) }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">مدة العقد</dt><dd class="font-bold text-ink">{{ $rr->lease_years }} سنة</dd></div>
            <div class="flex justify-between border-t border-slate-100 dark:border-slate-700 pt-3"><dt class="text-slate-500">إجمالي قيمة العقد</dt><dd class="font-extrabold text-brand-dark">{{ egp($rr->total_value ?? $rr->currentTotal()) }}</dd></div>
            @if (in_array($rr->status, ['pending', 'negotiating']))
                <div class="flex justify-between text-xs"><dt class="text-slate-400">آخر عرض من</dt><dd class="text-slate-500">{{ $rr->last_offer_by === 'owner' ? 'المالك' : 'المستأجر' }}</dd></div>
            @endif
        </dl>

        @if ($rr->canRespond($me) && $p->status === 'approved')
            <div class="mt-6 space-y-3">
                <form method="POST" action="{{ route('requests.accept', $rr) }}">@csrf<button class="btn btn-brand w-full" onclick="return confirm('موافقة نهائية على الإيجار {{ egp($rr->proposed_rent) }} لمدة {{ $rr->lease_years }} سنة؟')">أوافق على العرض</button></form>
                <details class="rounded-xl border border-slate-200 dark:border-slate-700 p-3">
                    <summary class="cursor-pointer text-sm font-semibold text-ink">عرض مضاد (تعديل السعر / المدة)</summary>
                    <form method="POST" action="{{ route('requests.counter', $rr) }}" class="mt-3 grid grid-cols-2 gap-3">@csrf
                        <div><label class="label">إيجار شهري</label><input type="number" name="proposed_rent" value="{{ $rr->proposed_rent }}" class="field" min="1" required></div>
                        <div><label class="label">المدة (سنين)</label><input type="number" name="lease_years" value="{{ $rr->lease_years }}" min="{{ $p->min_lease_years }}" max="{{ $p->max_lease_years }}" class="field" required></div>
                        <button class="btn btn-primary col-span-2">إرسال العرض</button>
                    </form>
                </details>
                <form method="POST" action="{{ route('requests.reject', $rr) }}">@csrf<button class="btn btn-ghost w-full !text-rose-600" onclick="return confirm('رفض الطلب؟')">رفض</button></form>
            </div>
        @elseif (in_array($rr->status, ['pending', 'negotiating']))
            <p class="mt-5 text-xs text-slate-500 bg-slate-50 dark:bg-slate-800/50 rounded-lg px-3 py-2">في انتظار رد {{ $rr->awaitingParty() === 'owner' ? 'المالك' : 'المستأجر' }}.</p>
            @if ($isTenant)<form method="POST" action="{{ route('requests.cancel', $rr) }}" class="mt-3">@csrf<button class="btn btn-ghost !text-xs" onclick="return confirm('إلغاء الطلب؟')">إلغاء الطلب</button></form>@endif
        @endif
    </section>

    {{-- بيانات الطرف الآخر --}}
    <section class="card p-6">
        <h2 class="font-bold text-ink mb-4">{{ $isOwner ? 'بيانات المستأجر' : ($isTenant ? 'بيانات المالك' : 'الأطراف') }}</h2>
        @php($released = $rr->contactsReleased() || $me->isAdmin())

        @if ($isOwner || $me->isAdmin())
            <div class="flex items-center gap-3 mb-4"><img src="{{ $rr->tenant->avatar_url }}" class="w-12 h-12 rounded-full object-cover" alt=""><div><p class="font-bold text-ink">{{ $rr->tenant->name }}</p><p class="text-xs text-slate-500">مستأجر</p></div></div>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div><dt class="text-xs text-slate-400">الغرض</dt><dd class="font-semibold text-ink">{{ $rr->purpose_label }}</dd></div>
                <div><dt class="text-xs text-slate-400">الحالة الاجتماعية</dt><dd class="font-semibold text-ink">{{ $rr->marital_label }}</dd></div>
                <div><dt class="text-xs text-slate-400">عدد الأفراد</dt><dd class="font-semibold text-ink">{{ $rr->occupants ?? '—' }}</dd></div>
                <div><dt class="text-xs text-slate-400">تاريخ الاستلام</dt><dd class="font-semibold text-ink">{{ $rr->start_date?->format('Y/m/d') ?? '—' }}</dd></div>
                @if ($rr->tenant->profile?->job)<div class="col-span-2"><dt class="text-xs text-slate-400">الوظيفة</dt><dd class="font-semibold text-ink">{{ $rr->tenant->profile->job }}</dd></div>@endif
            </dl>
            @if ($rr->message)<p class="mt-4 text-sm bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 leading-7">“{{ $rr->message }}”</p>@endif
        @else
            <div class="flex items-center gap-3 mb-4"><img src="{{ $rr->owner->avatar_url }}" class="w-12 h-12 rounded-full object-cover" alt=""><div><p class="font-bold text-ink">{{ $released ? $rr->owner->name : \Illuminate\Support\Str::before($rr->owner->name, ' ').' …' }}</p><p class="text-xs text-slate-500">مالك العقار</p></div></div>
        @endif

        @if ($released)
            <div class="mt-4 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm dark:bg-emerald-950/30 dark:border-emerald-900">
                <p class="font-bold text-emerald-700 mb-2">بيانات التواصل</p>
                @php($other = $isOwner ? $rr->tenant : $rr->owner)
                <p dir="ltr" class="text-end"><a href="tel:{{ $other->phone }}" class="font-bold">{{ $other->phone }}</a></p>
                <p dir="ltr" class="text-end">{{ $other->email }}</p>
                @if ($isTenant || $me->isAdmin())<p class="mt-2 text-slate-600"><b>العنوان الكامل:</b> {{ $p->full_address }}</p>@endif
            </div>
        @else
            <p class="mt-4 text-xs text-slate-500 bg-brand-50 dark:bg-slate-800/50 rounded-lg px-3 py-2 leading-6">رقم الموبايل والإيميل {{ $isTenant ? 'والعنوان الكامل' : '' }} بيظهروا بعد الاتفاق وسداد نسبة المنصة من الطرفين.</p>
        @endif
    </section>
</div>

{{-- نسبة المنصة --}}
@if (in_array($rr->status, ['accepted', 'completed']))
<section class="card p-6 border-brand/50">
    <h2 class="font-bold text-ink mb-1">نسبة المنصة</h2>
    <p class="text-xs text-slate-500 mb-5">محسوبة من إجمالي قيمة العقد {{ egp($rr->total_value) }}.</p>
    <div class="grid sm:grid-cols-2 gap-4">
        @foreach (['owner' => 'المالك', 'tenant' => 'المستأجر'] as $party => $lbl)
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 {{ ($party === 'owner' && $isOwner) || ($party === 'tenant' && $isTenant) ? 'ring-2 ring-brand' : '' }}">
                <p class="text-xs text-slate-500">{{ $lbl }} ({{ rtrim(rtrim(number_format($rr->{$party.'_commission_percent'}, 2), '0'), '.') }}%)</p>
                <p class="text-xl font-extrabold text-ink mt-1">{{ egp($rr->{$party.'_commission'}) }}</p>
                <p class="mt-2 text-xs font-bold {{ $rr->{$party.'_commission_paid_at'} ? 'text-emerald-600' : 'text-amber-600' }}">{{ $rr->{$party.'_commission_paid_at'} ? '✓ تم السداد '.$rr->{$party.'_commission_paid_at'}->format('Y/m/d') : 'في انتظار السداد' }}</p>
                @if ($me->isAdmin() && ! $rr->{$party.'_commission_paid_at'} && $rr->status === 'accepted')
                    <form method="POST" action="{{ route('admin.requests.paid', $rr) }}" class="mt-3">@csrf<input type="hidden" name="party" value="{{ $party }}"><button class="btn btn-brand !py-1.5 !text-xs" onclick="return confirm('تأكيد استلام النسبة؟')">تسجيل سداد {{ $lbl }}</button></form>
                @endif
            </div>
        @endforeach
    </div>
    @if ($rr->status === 'accepted' && ! $me->isAdmin())
        <p class="mt-5 text-sm text-slate-600 leading-7">لسداد نسبتك تواصل مع إدارة المنصة على <a class="text-brand-dark font-bold" href="https://wa.me/{{ config('site.whatsapp') }}" target="_blank">واتساب</a> أو <a class="text-brand-dark font-bold" dir="ltr" href="tel:{{ config('site.phone') }}">{{ config('site.phone') }}</a> وذكر رقم الطلب <b dir="ltr">{{ $rr->code }}</b>. بعد تأكيد السداد من الطرفين بتتفتح بيانات التواصل وبيتحول العقار لـ «تم التأجير».</p>
    @endif
    @if ($me->isAdmin())
        <form method="POST" action="{{ route('admin.requests.note', $rr) }}" class="mt-5">@csrf<label class="label">ملاحظة إدارية</label><textarea name="admin_note" rows="2" class="field">{{ $rr->admin_note }}</textarea><button class="btn btn-ghost mt-2 !text-xs">حفظ الملاحظة</button></form>
    @endif
</section>
@endif
@endsection
