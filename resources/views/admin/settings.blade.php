@extends('layouts.dashboard')
@section('title', 'إعدادات النسبة')
@section('panel')
<h1 class="text-xl font-extrabold text-ink">نسبة المنصة</h1>
<form method="POST" action="{{ route('admin.settings.update') }}" class="card p-6 space-y-5 max-w-xl">
    @csrf @method('PUT')
    <p class="text-sm text-slate-600 leading-7">النسبة بتتحسب من <b>إجمالي قيمة العقد</b> (الإيجار الشهري × 12 × السنين) وبتتثبت وقت الاتفاق، فتغييرها هنا يسري على الاتفاقات الجديدة فقط.</p>
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="label">نسبتك من المالك (%)</label><input type="number" step="0.01" min="0" max="50" name="commission_owner_percent" value="{{ old('commission_owner_percent', $owner) }}" class="field" required></div>
        <div><label class="label">نسبتك من المستأجر (%)</label><input type="number" step="0.01" min="0" max="50" name="commission_tenant_percent" value="{{ old('commission_tenant_percent', $tenant) }}" class="field" required></div>
    </div>
    <div class="rounded-xl bg-brand-50 dark:bg-slate-800/50 p-4 text-sm text-slate-600 leading-7">مثال: إيجار 15,000 ج.م × 12 شهر × 5 سنين = <b>900,000 ج.م</b>.<br>بنسبة {{ $owner }}% + {{ $tenant }}% = <b>{{ egp(900000 * ($owner + $tenant) / 100) }}</b> ليك من الاتفاق.</div>
    <button class="btn btn-brand">حفظ</button>
</form>
@endsection
