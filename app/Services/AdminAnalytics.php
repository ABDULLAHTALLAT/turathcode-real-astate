<?php

namespace App\Services;

use App\Models\ContactMessage;
use App\Models\Property;
use App\Models\RentalRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * أرقام ورسوم لوحة الأدمن. التجميع بيتم في PHP (مش بدوال تواريخ SQL)
 * عشان يشتغل نفس الشكل على MySQL و SQLite.
 */
class AdminAnalytics
{
    public const RANGES = [7, 30, 90, 365];

    private Carbon $from;
    private Carbon $to;
    private Carbon $prevFrom;
    private int $step;
    private int $buckets;

    public function __construct(private int $days)
    {
        $this->days = in_array($days, self::RANGES, true) ? $days : 30;
        $this->to = now()->endOfDay();
        $this->from = now()->subDays($this->days - 1)->startOfDay();
        $this->prevFrom = $this->from->copy()->subDays($this->days);
        $this->step = $this->days <= 31 ? 1 : ($this->days <= 100 ? 7 : 30);
        $this->buckets = (int) ceil($this->days / $this->step);
    }

    public function days(): int
    {
        return $this->days;
    }

    public function build(): array
    {
        $users = User::where('role', '!=', 'admin')->where('created_at', '>=', $this->prevFrom)->get(['id', 'role', 'created_at']);
        $props = Property::where('created_at', '>=', $this->prevFrom)->get(['id', 'created_at']);
        $reqs = RentalRequest::where('created_at', '>=', $this->prevFrom)->get(['id', 'status', 'purpose', 'created_at', 'accepted_at', 'completed_at']);
        $deals = RentalRequest::whereIn('status', ['accepted', 'completed'])->get();

        $inRange = fn (Collection $c, string $col = 'created_at') => $c->filter(fn ($m) => $m->$col && $m->$col->betweenIncluded($this->from, $this->to));
        $inPrev = fn (Collection $c, string $col = 'created_at') => $c->filter(fn ($m) => $m->$col && $m->$col->betweenIncluded($this->prevFrom, $this->from->copy()->subSecond()));

        $dealDate = fn ($r) => $r->accepted_at ?? $r->created_at;
        $dealsIn = $deals->filter(fn ($r) => $dealDate($r)->betweenIncluded($this->from, $this->to));
        $dealsPrev = $deals->filter(fn ($r) => $dealDate($r)->betweenIncluded($this->prevFrom, $this->from->copy()->subSecond()));
        $completed = fn (Collection $c) => $c->where('status', 'completed');

        $collected = $dealsIn->sum->paid_earnings;
        $collectedPrev = $dealsPrev->sum->paid_earnings;
        $pending = $dealsIn->sum->platform_earnings - $collected;

        $reqIn = $inRange($reqs);
        $reqPrev = $inPrev($reqs);
        $closed = $deals->filter(fn ($r) => $r->accepted_at);
        $avgClose = $closed->count() ? round($closed->avg(fn ($r) => $r->created_at->diffInHours($r->accepted_at) / 24), 1) : null;

        $published = Property::where('status', 'approved')->count();
        $rented = Property::where('status', 'rented')->count();
        $conv = fn (Collection $c) => $c->count() ? round($c->whereIn('status', ['accepted', 'completed'])->count() / $c->count() * 100, 1) : 0;

        $spark = fn (Collection $c, string $col = 'created_at') => $this->series($inRange($c, $col));

        $kpis = [
            ['key' => 'earnings', 'label' => 'أرباح المنصة المحصّلة', 'value' => (int) $collected, 'money' => true, 'delta' => $this->delta($collected, $collectedPrev), 'hint' => 'مستحق: '.egp($pending), 'spark' => $this->earningsSeries($dealsIn)['collected']],
            ['key' => 'users', 'label' => 'مستخدمين جدد', 'value' => $inRange($users)->count(), 'delta' => $this->delta($inRange($users)->count(), $inPrev($users)->count()), 'hint' => 'إجمالي '.User::where('role', '!=', 'admin')->count(), 'spark' => $spark($users)],
            ['key' => 'listings', 'label' => 'عقارات مضافة', 'value' => $inRange($props)->count(), 'delta' => $this->delta($inRange($props)->count(), $inPrev($props)->count()), 'hint' => $published.' منشور الآن', 'spark' => $spark($props)],
            ['key' => 'requests', 'label' => 'طلبات إيجار', 'value' => $reqIn->count(), 'delta' => $this->delta($reqIn->count(), $reqPrev->count()), 'hint' => 'مفتوح: '.RentalRequest::whereIn('status', ['pending', 'negotiating'])->count(), 'spark' => $spark($reqs)],
            ['key' => 'deals', 'label' => 'اتفاقات تمت', 'value' => $dealsIn->count(), 'delta' => $this->delta($dealsIn->count(), $dealsPrev->count()), 'hint' => $completed($dealsIn)->count().' مكتمل السداد', 'spark' => $this->series($dealsIn, 'accepted_at')],
            ['key' => 'conversion', 'label' => 'معدل التحويل', 'value' => $conv($reqIn), 'suffix' => '%', 'delta' => $this->delta($conv($reqIn), $conv($reqPrev)), 'hint' => 'من طلب لاتفاق', 'spark' => null],
            ['key' => 'occupancy', 'label' => 'نسبة التأجير', 'value' => ($published + $rented) ? round($rented / ($published + $rented) * 100, 1) : 0, 'suffix' => '%', 'delta' => null, 'hint' => $rented.' من '.($published + $rented).' عقار', 'spark' => null],
            ['key' => 'close', 'label' => 'متوسط مدة الاتفاق', 'value' => $avgClose ?? 0, 'suffix' => ' يوم', 'delta' => null, 'hint' => 'من الطلب للقبول', 'spark' => null],
        ];

        $stageLabels = catalog('finishing_stages');
        $live = Property::whereIn('status', ['approved', 'rented'])->get(['finishing_stage', 'property_type', 'governorate', 'market_rent', 'offered_rent']);

        $byStage = collect(array_keys($stageLabels))->map(function ($k) use ($live, $stageLabels) {
            $g = $live->where('finishing_stage', $k);

            return ['label' => $stageLabels[$k]['label'], 'count' => $g->count(),
                'market' => (int) round($g->avg('market_rent') ?? 0), 'offered' => (int) round($g->avg('offered_rent') ?? 0)];
        });

        $count = fn (Collection $c, array $labels, string $col) => collect($labels)->map(fn ($l, $k) => ['label' => $l, 'count' => $c->where($col, $k)->count()]);
        $types = $count($live, catalog('property_types'), 'property_type')->sortByDesc('count')->values();
        $govs = $live->groupBy('governorate')->map->count()->sortDesc()->take(8);

        $statusMap = catalog('property_statuses');
        $propStatus = Property::selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status');

        $reqStatus = catalog('request_statuses');
        $funnelKeys = ['pending', 'negotiating', 'accepted', 'completed', 'rejected', 'cancelled'];
        $funnel = collect($funnelKeys)->filter(fn ($k) => isset($reqStatus[$k]))->map(fn ($k) => ['label' => $reqStatus[$k]['label'], 'count' => $reqIn->where('status', $k)->count(), 'muted' => in_array($k, ['rejected', 'cancelled'])])->values();

        $weekdays = ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'];
        $wd = array_fill(0, 7, 0);
        foreach ($reqIn as $r) {
            $wd[(($r->created_at->dayOfWeek + 1) % 7)]++;
        }

        $purposes = catalog('purposes');
        $purposeCounts = collect($purposes)->map(fn ($l, $k) => ['label' => $l, 'count' => $reqIn->where('purpose', $k)->count()])->sortByDesc('count')->values()->filter(fn ($x) => $x['count'] > 0)->values();

        $labels = $this->labels();
        $growth = ['labels' => $labels,
            'owners' => $this->series($inRange($users)->where('role', 'owner')),
            'tenants' => $this->series($inRange($users)->where('role', 'tenant'))];
        $earn = $this->earningsSeries($dealsIn);

        return [
            'days' => $this->days,
            'kpis' => $kpis,
            'charts' => [
                'growth' => $growth,
                'revenue' => ['labels' => $labels, 'collected' => $earn['collected'], 'pending' => $earn['pending']],
                'requests' => ['labels' => $labels, 'created' => $this->series($reqIn), 'deals' => $this->series($dealsIn, 'accepted_at')],
                'funnel' => ['labels' => $funnel->pluck('label'), 'values' => $funnel->pluck('count'), 'muted' => $funnel->pluck('muted')],
                'weekday' => ['labels' => $weekdays, 'values' => $wd],
                'stages' => ['labels' => $byStage->pluck('label'), 'values' => $byStage->pluck('count')],
                'rent' => ['labels' => $byStage->pluck('label'), 'market' => $byStage->pluck('market'), 'offered' => $byStage->pluck('offered')],
                'types' => ['labels' => $types->pluck('label'), 'values' => $types->pluck('count')],
                'govs' => ['labels' => $govs->keys(), 'values' => $govs->values()],
                'status' => ['labels' => collect($statusMap)->map(fn ($s, $k) => $s['label'])->values(), 'values' => collect($statusMap)->map(fn ($s, $k) => (int) ($propStatus[$k] ?? 0))->values()],
                'purposes' => ['labels' => $purposeCounts->pluck('label'), 'values' => $purposeCounts->pluck('count')],
                'users' => ['labels' => ['ملّاك', 'مستأجرين'], 'values' => [User::where('role', 'owner')->count(), User::where('role', 'tenant')->count()]],
            ],
            'queues' => [
                'pendingProps' => Property::where('status', 'pending')->count(),
                'awaitingPay' => RentalRequest::where('status', 'accepted')->count(),
                'awaitingAmount' => (int) RentalRequest::where('status', 'accepted')->get()->sum(fn ($r) => $r->platform_earnings - $r->paid_earnings),
                'unread' => ContactMessage::where('is_read', false)->count(),
            ],
            'totals' => [
                'contracts' => (int) RentalRequest::where('status', 'completed')->sum('total_value'),
                'views' => (int) Property::sum('views'),
                'avgDiscount' => round((float) $live->filter(fn ($p) => $p->market_rent > 0)->avg(fn ($p) => ($p->market_rent - $p->offered_rent) / $p->market_rent * 100), 1),
            ],
            'topOwners' => $this->topOwners(),
            'topViewed' => Property::whereIn('status', ['approved', 'rented'])->orderByDesc('views')->with('images')->take(5)->get(),
            'activity' => $this->activity(),
            'pendingProperties' => Property::where('status', 'pending')->with(['images', 'owner'])->latest()->take(5)->get(),
            'awaitingPayment' => RentalRequest::where('status', 'accepted')->with(['property', 'tenant', 'owner'])->latest()->take(5)->get(),
        ];
    }

    /** عدّاد لكل فترة (يوم/أسبوع/شهر حسب المدى) */
    private function series(Collection $items, string $col = 'created_at'): array
    {
        $out = array_fill(0, $this->buckets, 0);
        foreach ($items as $m) {
            $d = $m->$col ?? $m->created_at;
            $i = intdiv((int) $this->from->diffInDays($d->copy()->startOfDay()), $this->step);
            if (isset($out[$i])) {
                $out[$i]++;
            }
        }

        return $out;
    }

    private function earningsSeries(Collection $deals): array
    {
        $col = array_fill(0, $this->buckets, 0);
        $pen = $col;
        foreach ($deals as $r) {
            $i = intdiv((int) $this->from->diffInDays(($r->accepted_at ?? $r->created_at)->copy()->startOfDay()), $this->step);
            if (isset($col[$i])) {
                $col[$i] += $r->paid_earnings;
                $pen[$i] += $r->platform_earnings - $r->paid_earnings;
            }
        }

        return ['collected' => $col, 'pending' => $pen];
    }

    private function labels(): array
    {
        return collect(range(0, $this->buckets - 1))->map(fn ($i) => $this->from->copy()->addDays($i * $this->step)->format('j/n'))->all();
    }

    private function delta(float|int $cur, float|int $prev): ?float
    {
        return $prev > 0 ? round(($cur - $prev) / $prev * 100) : null;
    }

    private function topOwners(): Collection
    {
        $value = RentalRequest::where('status', 'completed')->get(['owner_id', 'total_value'])->groupBy('owner_id')->map->sum('total_value');

        return User::where('role', 'owner')->withCount(['properties', 'properties as rented_count' => fn ($q) => $q->where('status', 'rented')])
            ->get()->map(function ($o) use ($value) {
                $o->contracts_value = (int) ($value[$o->id] ?? 0);

                return $o;
            })->sortByDesc(fn ($o) => [$o->contracts_value, $o->properties_count])->take(5)->values();
    }

    /** آخر الأحداث على المنصة (مستخدمين، عقارات، طلبات، رسائل) */
    private function activity(): Collection
    {
        $items = collect();
        foreach (User::where('role', '!=', 'admin')->latest()->take(5)->get() as $u) {
            $items->push(['type' => 'مستخدم جديد', 'title' => $u->name, 'meta' => $u->role_label, 'at' => $u->created_at, 'url' => route('admin.users')]);
        }
        foreach (Property::with('owner:id,name')->latest()->take(5)->get() as $p) {
            $items->push(['type' => 'عقار جديد', 'title' => $p->title, 'meta' => $p->owner?->name, 'at' => $p->created_at, 'url' => route('properties.show', $p)]);
        }
        foreach (RentalRequest::with(['property:id,title', 'tenant:id,name'])->latest()->take(5)->get() as $r) {
            $items->push(['type' => 'طلب إيجار', 'title' => $r->property?->title, 'meta' => $r->tenant?->name.' · '.$r->status_label, 'at' => $r->created_at, 'url' => route('admin.requests')]);
        }
        foreach (ContactMessage::latest()->take(3)->get() as $m) {
            $items->push(['type' => 'رسالة تواصل', 'title' => $m->name, 'meta' => \Illuminate\Support\Str::limit($m->message, 50), 'at' => $m->created_at, 'url' => route('admin.messages')]);
        }

        return $items->sortByDesc('at')->take(10)->values();
    }
}
