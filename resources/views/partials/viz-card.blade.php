{{-- كارت رسم: عنوان + معلومة سريعة + رسم + جدول بديل. $key, $title, $note, $labels, $series (اسم => قيم), $h, $money --}}
<section class="card p-5 flex flex-col {{ $class ?? '' }}">
    <div class="mb-3"><h3 class="font-bold text-ink text-sm">{{ $title }}</h3>@isset($note)<p class="text-xs text-slate-500 mt-0.5">{{ $note }}</p>@endisset</div>
    <div class="relative flex-1" style="height: {{ $h ?? 240 }}px"><canvas data-chart="{{ $key }}" role="img" aria-label="{{ $title }}"></canvas></div>
    <details class="mt-3 text-xs">
        <summary class="text-brand-dark font-semibold inline-block">عرض البيانات كجدول</summary>
        <div class="mt-2 max-h-48 overflow-auto rounded-lg border border-slate-100 dark:border-slate-700">
            <table class="w-full text-start">
                <thead class="bg-slate-50 dark:bg-slate-800 sticky top-0"><tr><th class="p-2 text-start font-semibold">&nbsp;</th>@foreach ($series as $name => $vals)<th class="p-2 text-start font-semibold">{{ $name }}</th>@endforeach</tr></thead>
                <tbody>@foreach ($labels as $i => $l)<tr class="border-t border-slate-100 dark:border-slate-700"><td class="p-2">{{ $l }}</td>@foreach ($series as $vals)<td class="p-2">{{ !empty($money) ? egp($vals[$i] ?? 0) : ($vals[$i] ?? 0) }}</td>@endforeach</tr>@endforeach</tbody>
            </table>
        </div>
    </details>
</section>
