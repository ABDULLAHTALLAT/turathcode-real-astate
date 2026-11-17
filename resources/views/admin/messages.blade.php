@extends('layouts.dashboard')
@section('title', 'رسائل التواصل')
@section('panel')
<h1 class="text-xl font-extrabold text-ink">رسائل التواصل</h1>
<div class="space-y-3">
    @forelse ($messages as $m)
        <div class="card p-5 {{ $m->is_read ? 'opacity-70' : 'border-brand' }}">
            <div class="flex flex-wrap items-center justify-between gap-2"><p class="font-bold text-ink">{{ $m->name }} <span class="text-xs font-normal text-slate-400" dir="ltr">{{ $m->phone }} {{ $m->email }}</span></p><span class="text-xs text-slate-400">{{ $m->created_at->diffForHumans() }}</span></div>
            <p class="text-sm text-slate-600 mt-2 leading-7 whitespace-pre-line">{{ $m->message }}</p>
            @unless ($m->is_read)<form method="POST" action="{{ route('admin.messages.read', $m) }}" class="mt-3">@csrf<button class="btn btn-ghost !py-1 !text-xs">تم القراءة</button></form>@endunless
        </div>
    @empty <div class="card p-12 text-center text-sm text-slate-500">لا توجد رسائل.</div> @endforelse
</div>
{{ $messages->links() }}
@endsection
