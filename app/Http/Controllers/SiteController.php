<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Property;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function home()
    {
        $with = ['images', 'owner:id,name', 'finishingItems'];
        $published = fn () => Property::published()->whereHas('images');

        // صور الهيرو: واجهات عقارات حقيقية بجودة مناسبة للعرض الكبير
        $hero = [];
        foreach ($published()->with('images')->latest()->take(40)->get() as $p) {
            $img = $p->images->first();
            $file = $img ? public_path($img->path.'-large.webp') : null;
            if ($file && is_file($file) && (@getimagesize($file)[0] ?? 0) >= 1000) {
                $hero[] = ['url' => $img->large_url, 'caption' => $p->city.'، '.$p->governorate, 'slug' => $p->slug];
            }
            if (count($hero) === 4) {
                break;
            }
        }

        $govCounts = Property::published()->selectRaw('governorate, count(*) c')->groupBy('governorate')->orderByDesc('c')->limit(12)->pluck('c', 'governorate');
        $stageCounts = Property::published()->selectRaw('finishing_stage, count(*) c')->groupBy('finishing_stage')->pluck('c', 'finishing_stage');

        // صورة لكل محافظة / مرحلة تشطيب (من عقار فعلي)
        $govImages = [];
        foreach ($govCounts->keys()->take(5) as $gov) {
            $govImages[$gov] = optional($published()->with('images')->where('governorate', $gov)->latest()->first())->coverUrl('medium');
        }
        $stageImages = [];
        foreach (array_keys(catalog('finishing_stages')) as $stage) {
            $stageImages[$stage] = optional($published()->with('images')->where('finishing_stage', $stage)->latest()->first())->coverUrl('medium');
        }

        $anyImage = collect($govImages)->filter()->first();
        $stageImages = array_map(fn ($u) => $u ?: $anyImage, $stageImages);

        return view('site.home', [
            'hero' => $hero,
            'featured' => Property::published()->with($with)->where('is_featured', true)->latest()->take(6)->get(),
            'latest' => Property::published()->with($with)->latest()->take(8)->get(),
            'stats' => [
                'properties' => Property::whereIn('status', ['approved', 'rented'])->count(),
                'rented' => Property::where('status', 'rented')->count(),
                'owners' => User::where('role', 'owner')->count(),
                'tenants' => User::where('role', 'tenant')->count(),
                'governorates' => Property::published()->distinct('governorate')->count('governorate'),
            ],
            'govCounts' => $govCounts,
            'govImages' => $govImages,
            'typeCounts' => Property::published()->selectRaw('property_type, count(*) c')->groupBy('property_type')->pluck('c', 'property_type'),
            'stageCounts' => $stageCounts,
            'stageImages' => $stageImages,
            'aboutImages' => $published()->with('images')->latest('id')->skip(3)->take(2)->get()->map->coverUrl('large')->all(),
            'ownerRate' => (float) (Setting::get('commission_owner_percent', config('site.commission_owner_percent'))),
            'tenantRate' => (float) (Setting::get('commission_tenant_percent', config('site.commission_tenant_percent'))),
        ]);
    }

    public function index(Request $request)
    {
        $sort = $request->query('sort', 'latest');
        $query = Property::published()->with(['images', 'finishingItems'])->filter($request->query());

        match ($sort) {
            'price_asc' => $query->orderBy('offered_rent'),
            'price_desc' => $query->orderByDesc('offered_rent'),
            'area' => $query->orderByDesc('area_m2'),
            default => $query->orderByDesc('is_featured')->latest(),
        };

        return view('site.properties', [
            'properties' => $query->paginate(12)->withQueryString(),
            'filters' => $request->query(),
        ]);
    }

    public function show(Property $property)
    {
        $viewer = auth()->user();
        $canSee = $property->status === 'approved' || $property->status === 'rented'
            || ($viewer && ($viewer->isAdmin() || $viewer->id === $property->owner_id));
        abort_unless($canSee, 404);

        if ($property->status === 'approved') {
            $property->increment('views');
        }

        $property->load(['images', 'finishingItems', 'owner:id,name,avatar,created_at']);

        $myRequest = $viewer && $viewer->isTenant()
            ? $viewer->sentRequests()->where('property_id', $property->id)->latest()->first()
            : null;

        return view('site.property', [
            'property' => $property,
            'similar' => Property::published()->with('images')->where('id', '!=', $property->id)
                ->where('governorate', $property->governorate)->take(3)->get(),
            'myRequest' => $myRequest,
            'profile' => $viewer?->profile,
            'isFav' => $property->isFavoritedBy($viewer),
        ]);
    }

    public function how() { return view('site.how'); }
    public function about() { return view('site.about'); }
    public function terms() { return view('site.terms', ['owner' => Setting::commission('owner'), 'tenant' => Setting::commission('tenant')]); }
    public function contact() { return view('site.contact'); }

    public function contactSend(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:190'],
            'message' => ['required', 'string', 'max:3000'],
        ], [], ['name' => 'الاسم', 'message' => 'الرسالة']);

        ContactMessage::create($data);

        return back()->with('success', 'وصلتنا رسالتك، هنرد عليك في أقرب وقت.');
    }
}
