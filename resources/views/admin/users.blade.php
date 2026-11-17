@extends('layouts.dashboard')
@section('title', 'المستخدمين')
@section('panel')
<h1 class="text-xl font-extrabold text-ink">المستخدمين</h1>
<form class="flex flex-wrap gap-2"><input name="q" value="{{ request('q') }}" class="field !w-60" placeholder="اسم / إيميل / موبايل">
    <select name="role" class="field !w-40"><option value="">الكل</option><option value="owner" @selected(request('role') === 'owner')>ملاك</option><option value="tenant" @selected(request('role') === 'tenant')>مستأجرين</option></select><button class="btn btn-primary">فلترة</button></form>
<div class="card overflow-x-auto"><table class="w-full text-sm min-w-[640px]">
    <thead class="text-xs text-slate-500 bg-slate-50 dark:bg-slate-800/50"><tr><th class="text-start p-3">المستخدم</th><th class="text-start p-3">النوع</th><th class="text-start p-3">التواصل</th><th class="text-start p-3">النشاط</th><th class="p-3"></th></tr></thead>
    <tbody class="divide-y divide-slate-100">@foreach ($users as $x)
        <tr><td class="p-3"><div class="flex items-center gap-2"><img src="{{ $x->avatar_url }}" class="w-8 h-8 rounded-full" alt=""><span class="font-semibold text-ink">{{ $x->name }}</span></div></td>
            <td class="p-3">{{ $x->role_label }}</td><td class="p-3 text-xs" dir="ltr">{{ $x->phone }}<br>{{ $x->email }}</td>
            <td class="p-3 text-xs">{{ $x->properties_count }} عقار · {{ $x->sent_requests_count }} طلب</td>
            <td class="p-3 text-end"><form method="POST" action="{{ route('admin.users.toggle', $x) }}">@csrf<button class="btn btn-ghost !py-1 !text-xs {{ $x->is_active ? '!text-rose-600' : '!text-emerald-600' }}">{{ $x->is_active ? 'إيقاف' : 'تفعيل' }}</button></form></td></tr>
    @endforeach</tbody></table></div>
{{ $users->links() }}
@endsection
