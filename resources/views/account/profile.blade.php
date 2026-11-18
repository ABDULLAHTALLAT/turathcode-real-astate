@extends('layouts.dashboard')
@section('title', 'ملفي الشخصي')
@section('panel')
<h1 class="text-xl font-extrabold text-ink">ملفي الشخصي</h1>
<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf @method('PUT')
    <section class="card p-6 space-y-4">
        <div class="flex items-center gap-4"><img src="{{ $user->avatar_url }}" class="w-20 h-20 rounded-2xl object-cover" alt="">
            <div><label class="label">الصورة الشخصية</label><input type="file" name="avatar" accept="image/*" class="text-xs"></div></div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="label">الاسم *</label><input name="name" value="{{ old('name', $user->name) }}" class="field" required></div>
            <div><label class="label">رقم الموبايل *</label><input name="phone" value="{{ old('phone', $user->phone) }}" class="field" dir="ltr" required></div>
            <div><label class="label">الإيميل *</label><input type="email" name="email" value="{{ old('email', $user->email) }}" class="field" dir="ltr" required></div>
            <div><label class="label">المحافظة</label><select name="governorate" class="field"><option value="">—</option>@foreach (catalog('governorates') as $g)<option @selected(old('governorate', $user->governorate) === $g)>{{ $g }}</option>@endforeach</select></div>
            <div><label class="label">المدينة</label><input name="city" value="{{ old('city', $user->city) }}" class="field"></div>
        </div>
        <div><label class="label">نبذة عنك</label><textarea name="bio" rows="3" class="field">{{ old('bio', $user->bio) }}</textarea></div>
    </section>

    @if ($user->isTenant())
    <section class="card p-6 space-y-4">
        <h2 class="font-bold text-ink section-title">بيانات البحث (بتساعد المالك يطمّن لك)</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="label">الحالة الاجتماعية</label><select name="marital_status" class="field"><option value="">—</option>@foreach (catalog('marital_statuses') as $k => $l)<option value="{{ $k }}" @selected(old('marital_status', $profile?->marital_status) === $k)>{{ $l }}</option>@endforeach</select></div>
            <div><label class="label">عدد أفراد الأسرة</label><input type="number" name="family_members" value="{{ old('family_members', $profile?->family_members) }}" class="field"></div>
            <div><label class="label">الغرض من العقار</label><select name="purpose" class="field"><option value="">—</option>@foreach (catalog('purposes') as $k => $l)<option value="{{ $k }}" @selected(old('purpose', $profile?->purpose) === $k)>{{ $l }}</option>@endforeach</select></div>
            <div><label class="label">الوظيفة / النشاط</label><input name="job" value="{{ old('job', $profile?->job) }}" class="field"></div>
            <div><label class="label">أقل ميزانية شهرية</label><input type="number" name="budget_min" value="{{ old('budget_min', $profile?->budget_min) }}" class="field"></div>
            <div><label class="label">أقصى ميزانية شهرية</label><input type="number" name="budget_max" value="{{ old('budget_max', $profile?->budget_max) }}" class="field"></div>
            <div><label class="label">المحافظة المفضلة</label><select name="preferred_governorate" class="field"><option value="">—</option>@foreach (catalog('governorates') as $g)<option @selected(old('preferred_governorate', $profile?->preferred_governorate) === $g)>{{ $g }}</option>@endforeach</select></div>
            <div><label class="label">مدة الإيجار المفضلة (سنين)</label><input type="number" name="preferred_lease_years" value="{{ old('preferred_lease_years', $profile?->preferred_lease_years) }}" class="field"></div>
        </div>
    </section>
    @endif

    <section class="card p-6 space-y-4">
        <h2 class="font-bold text-ink section-title">تغيير كلمة المرور (اختياري)</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="label">كلمة مرور جديدة</label><input type="password" name="password" class="field" autocomplete="new-password"></div>
            <div><label class="label">تأكيدها</label><input type="password" name="password_confirmation" class="field" autocomplete="new-password"></div>
        </div>
    </section>
    <button class="btn btn-brand">حفظ</button>
</form>
@endsection
