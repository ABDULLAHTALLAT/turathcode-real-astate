{{-- يعرض بنود التشطيب مجمّعة. المتغير: $property --}}
@php
    $items = $property->finishingItems->keyBy('item_key');
    $groups = collect(catalog('finishing_items'))->groupBy('group');
    $st = catalog('item_statuses');
@endphp
@if ($items->isEmpty())
    <p class="text-sm text-slate-500">لم يحدد المالك تفاصيل البنود.</p>
@else
    <div class="grid md:grid-cols-2 gap-4">
        @foreach ($groups as $group => $defs)
            @php($rows = $defs->filter(fn ($d, $k) => $items->has($k)))
            @continue($rows->isEmpty())
            <div class="rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <p class="bg-slate-50 dark:bg-slate-800/60 text-xs font-bold text-slate-600 px-4 py-2">{{ $group }}</p>
                <ul class="divide-y divide-slate-100">
                    @foreach ($rows as $key => $def)
                        @php($it = $items[$key])
                        <li class="px-4 py-2.5 flex items-center justify-between gap-3 text-sm">
                            <span>{{ $def['label'] }}@if ($it->note)<span class="block text-[11px] text-slate-400">{{ $it->note }}</span>@endif</span>
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-{{ $st[$it->status]['color'] }}-100 text-{{ $st[$it->status]['color'] }}-700 whitespace-nowrap">{{ $st[$it->status]['label'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
@endif
