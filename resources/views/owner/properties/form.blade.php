@extends('layouts.dashboard')
@php($editing = $property->exists)
@section('title', $editing ? 'تعديل عقار' : 'إضافة عقار')
@section('panel')
@php($v = fn ($k, $d = null) => old($k, $property->{$k} ?? $d))
<div><h1 class="text-xl font-extrabold text-ink">{{ $editing ? 'تعديل العقار' : 'إضافة عقار جديد' }}</h1>
    <p class="text-sm text-slate-500">كل ما كانت التفاصيل أدق، كل ما رد المستأجرين أسرع وأجدّ.</p></div>

<form method="POST" enctype="multipart/form-data" action="{{ $editing ? route('owner.properties.update', $property) : route('owner.properties.store') }}" class="space-y-6">
    @csrf @if ($editing) @method('PUT') @endif

    <section class="card p-6 space-y-4">
        <h2 class="font-bold text-ink section-title">1) البيانات الأساسية</h2>
        <div><label class="label">عنوان الإعلان *</label><input name="title" value="{{ $v('title') }}" class="field" placeholder="مثال: شقة 150م على المحارة بمدينة نصر" required></div>
        <div class="grid sm:grid-cols-3 gap-4">
            <div><label class="label">نوع العقار *</label><select name="property_type" class="field" required>@foreach (catalog('property_types') as $k => $l)<option value="{{ $k }}" @selected($v('property_type') === $k)>{{ $l }}</option>@endforeach</select></div>
            <div><label class="label">المساحة (م²) *</label><input type="number" name="area_m2" value="{{ $v('area_m2') }}" class="field" required></div>
            <div><label class="label">معطل من (سنين)</label><input type="number" name="vacant_since_years" value="{{ $v('vacant_since_years') }}" class="field"></div>
            <div><label class="label">عدد الغرف</label><input type="number" name="rooms" value="{{ $v('rooms') }}" class="field"></div>
            <div><label class="label">الحمامات</label><input type="number" name="bathrooms" value="{{ $v('bathrooms') }}" class="field"></div>
            <div><label class="label">عدد الأدوار (للمبنى)</label><input type="number" name="floors_count" value="{{ $v('floors_count') }}" class="field"></div>
        </div>
        <div><label class="label">وصف العقار</label><textarea name="description" rows="4" class="field" placeholder="اكتب كل ما يهم المستأجر: الموقع، الجيران، المميزات…">{{ $v('description') }}</textarea>
            <p class="text-[11px] text-slate-400 mt-1">أرقام التليفون والإيميلات بتتحجب تلقائياً.</p></div>
    </section>

    <section class="card p-6 space-y-4">
        <h2 class="font-bold text-ink section-title">2) العنوان بالتفصيل</h2>
        <div class="grid sm:grid-cols-3 gap-4">
            <div><label class="label">المحافظة *</label><select name="governorate" class="field" required><option value="">اختر</option>@foreach (catalog('governorates') as $g)<option @selected($v('governorate') === $g)>{{ $g }}</option>@endforeach</select></div>
            <div><label class="label">المدينة / المركز *</label><input name="city" value="{{ $v('city') }}" class="field" required></div>
            <div><label class="label">الحي / المنطقة</label><input name="district" value="{{ $v('district') }}" class="field"></div>
            <div class="sm:col-span-3"><label class="label">الشارع</label><input name="street" value="{{ $v('street') }}" class="field"></div>
            <div><label class="label">رقم العقار</label><input name="building_number" value="{{ $v('building_number') }}" class="field"></div>
            <div><label class="label">الدور</label><input name="floor_number" value="{{ $v('floor_number') }}" class="field"></div>
            <div><label class="label">رقم الشقة / الوحدة</label><input name="unit_number" value="{{ $v('unit_number') }}" class="field"></div>
            <div class="sm:col-span-3"><label class="label">علامة مميزة</label><input name="landmark" value="{{ $v('landmark') }}" class="field" placeholder="بجوار / أمام…"></div>
            <div><label class="label">خط العرض (اختياري)</label><input name="latitude" value="{{ $v('latitude') }}" class="field" dir="ltr"></div>
            <div><label class="label">خط الطول (اختياري)</label><input name="longitude" value="{{ $v('longitude') }}" class="field" dir="ltr"></div>
        </div>
        <p class="text-xs bg-brand-50 dark:bg-slate-800/60 rounded-lg px-3 py-2 text-slate-600"><i class="bi bi-shield-lock-fill text-brand-dark"></i> رقم العقار والدور والوحدة والعلامة المميزة مش بتظهر للزوار — بتظهر بس بعد إتمام الاتفاق وسداد نسبة المنصة.</p>
    </section>

    <section class="card p-6 space-y-5">
        <h2 class="font-bold text-ink section-title">3) حالة التشطيب</h2>
        <div><label class="label">المرحلة الحالية للعقار *</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach (catalog('finishing_stages') as $k => $st)
                    <label class="cursor-pointer"><input type="radio" name="finishing_stage" value="{{ $k }}" class="peer sr-only" @checked($v('finishing_stage') === $k) required>
                        <span class="block rounded-xl border-2 border-slate-200 dark:border-slate-700 p-3 peer-checked:border-brand peer-checked:bg-brand-50 dark:peer-checked:bg-slate-800 transition">
                            <b class="text-ink text-sm">{{ $st['label'] }}</b><span class="block text-[11px] text-slate-500">{{ $st['hint'] }}</span></span></label>
                @endforeach
            </div></div>

        <div>
            <label class="label">تفاصيل كل بند (اختار حالته)</label>
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 divide-y divide-slate-100 overflow-hidden">
                @foreach (catalog('finishing_items') as $key => $def)
                    @php($cur = old("finishing.$key.status", $items[$key]->status ?? ''))
                    <div class="p-3 grid sm:grid-cols-12 gap-2 items-center">
                        <div class="sm:col-span-4 text-sm font-semibold text-ink">{{ $def['label'] }} <span class="text-[10px] text-slate-400 font-normal">· {{ $def['group'] }}</span></div>
                        <div class="sm:col-span-5 flex flex-wrap gap-1.5">
                            @foreach (catalog('item_statuses') as $sk => $sd)
                                <label class="cursor-pointer"><input type="radio" name="finishing[{{ $key }}][status]" value="{{ $sk }}" class="peer sr-only" @checked($cur === $sk)>
                                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full border border-slate-200 dark:border-slate-700 text-slate-500 peer-checked:bg-{{ $sd['color'] }}-100 peer-checked:text-{{ $sd['color'] }}-700 peer-checked:border-{{ $sd['color'] }}-300 transition">{{ $sd['label'] }}</span></label>
                            @endforeach
                        </div>
                        <input name="finishing[{{ $key }}][note]" value="{{ old("finishing.$key.note", $items[$key]->note ?? '') }}" placeholder="ملاحظة (اختياري)" class="field sm:col-span-3 !py-1.5 !text-xs">
                    </div>
                @endforeach
            </div>
            <p class="text-[11px] text-slate-400 mt-1">سيب البند من غير اختيار لو مش عايز تذكره.</p>
        </div>
        <div><label class="label">ملاحظات عامة على التشطيب</label><textarea name="finishing_notes" rows="3" class="field" placeholder="مثال: الواجهة محتاجة ترميم، والمصعد جاهز بدون تشغيل…">{{ $v('finishing_notes') }}</textarea></div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="label">مين هيكمّل التشطيب؟ *</label><select name="finishing_by" class="field">@foreach (catalog('finishing_by') as $k => $l)<option value="{{ $k }}" @selected($v('finishing_by') === $k)>{{ $l }}</option>@endforeach</select></div>
        </div>
        <div><label class="label">المرافق المتوفرة</label><div class="flex flex-wrap gap-2">
            @foreach (catalog('utilities') as $k => $l)<label class="cursor-pointer"><input type="checkbox" name="utilities[]" value="{{ $k }}" class="peer sr-only" @checked(in_array($k, (array) old('utilities', $property->utilities ?? [])))><span class="text-xs px-3 py-1.5 rounded-full border border-slate-200 dark:border-slate-700 peer-checked:bg-emerald-100 peer-checked:text-emerald-700 peer-checked:border-emerald-300 block">{{ $l }}</span></label>@endforeach</div></div>
        <div><label class="label">الأغراض المسموحة (الغرض من الشقة) *</label><div class="flex flex-wrap gap-2">
            @foreach (catalog('purposes') as $k => $l)<label class="cursor-pointer"><input type="checkbox" name="allowed_purposes[]" value="{{ $k }}" class="peer sr-only" @checked(in_array($k, (array) old('allowed_purposes', $property->allowed_purposes ?? [])))><span class="text-xs px-3 py-1.5 rounded-full border border-slate-200 dark:border-slate-700 peer-checked:bg-brand-100 peer-checked:text-brand-dark peer-checked:border-brand block">{{ $l }}</span></label>@endforeach</div></div>
    </section>

    <section class="card p-6 space-y-4">
        <h2 class="font-bold text-ink section-title">4) السعر والمدة</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <div><label class="label">إيجارها الحالي في السوق / شهر (ج.م) *</label><input type="number" name="market_rent" value="{{ $v('market_rent') }}" class="field" placeholder="مثال: 20000" required></div>
            <div><label class="label">السعر اللي هتأجرها بيه / شهر (ج.م) *</label><input type="number" name="offered_rent" value="{{ $v('offered_rent') }}" class="field" placeholder="مثال: 15000" required></div>
            <div><label class="label">أقل مدة عقد (سنين) *</label><input type="number" name="min_lease_years" value="{{ $v('min_lease_years', 1) }}" class="field" required></div>
            <div><label class="label">أقصى مدة عقد (سنين) *</label><input type="number" name="max_lease_years" value="{{ $v('max_lease_years', 5) }}" class="field" required></div>
            <div><label class="label">فترة سماح بدون إيجار (شهور)</label><input type="number" name="rent_free_months" value="{{ $v('rent_free_months', 0) }}" class="field"><p class="text-[11px] text-slate-400 mt-1">وقت تشطيب المستأجر مثلاً.</p></div>
            <div><label class="label">زيادة سنوية (%)</label><input type="number" name="annual_increase_percent" value="{{ $v('annual_increase_percent', 0) }}" class="field"></div>
        </div>
    </section>

    <section class="card p-6 space-y-4">
        <h2 class="font-bold text-ink section-title">5) الصور</h2>
        @if ($editing && $property->images->count())
            <div class="grid grid-cols-3 sm:grid-cols-5 gap-3">
                @foreach ($property->images as $img)
                    <label class="relative block"><img src="{{ $img->url }}" class="w-full h-24 object-cover rounded-lg" alt="">
                        <span class="absolute bottom-1 start-1 bg-white/90 text-rose-600 text-[10px] font-bold rounded px-1.5 py-0.5 flex items-center gap-1"><input type="checkbox" name="remove_images[]" value="{{ $img->id }}"> حذف</span></label>
                @endforeach
            </div>
        @endif
        <div><label class="label">{{ $editing ? 'إضافة صور جديدة' : 'صور العقار * (حتى 12 صورة)' }}</label>
            <input type="file" name="images[]" accept="image/*" multiple class="field" {{ $editing ? '' : 'required' }}>
            <p class="text-[11px] text-slate-400 mt-1">الصورة الأولى هي الغلاف. الحد الأقصى 5MB للصورة.</p></div>
    </section>

    <section class="card p-6">
        <label class="flex gap-2 items-start text-sm text-slate-600 leading-7"><input type="checkbox" name="terms" value="1" class="mt-1.5 accent-sky-500" required @checked($editing)>
            <span>أقرّ بصحة البيانات وإني المالك أو مفوّض بالتأجير، وأوافق على <a href="{{ route('terms') }}" target="_blank" class="text-brand-dark font-bold underline">شروط المنصة</a>، ونسبتها <b>{{ rtrim(rtrim(number_format($commission, 2), '0'), '.') }}%</b> من إجمالي قيمة العقد تُسدد عند إتمام الاتفاق.</span></label>
        <div class="flex gap-3 mt-5"><button class="btn btn-brand"><i class="bi bi-check2-circle"></i> {{ $editing ? 'حفظ التعديلات' : 'إرسال للمراجعة' }}</button><a href="{{ route('owner.properties.index') }}" class="btn btn-ghost">إلغاء</a></div>
    </section>
</form>
@endsection
