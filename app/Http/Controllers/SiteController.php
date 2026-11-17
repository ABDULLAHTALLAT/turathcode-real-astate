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
        $with = ['images', 'owner:id,name'];

        return view('site.home', [
            'featured' => Property::published()->with($with)->where('is_featured', true)->latest()->take(6)->get(),
            'latest' => Property::published()->with($with)->latest()->take(8)->get(),
            'stats' => [
                'properties' => Property::whereIn('status', ['approved', 'rented'])->count(),
                'rented' => Property::where('status', 'rented')->count(),
                'owners' => User::where('role', 'owner')->count(),
                'tenants' => User::where('role', 'tenant')->count(),
            ],
            // الأقسام: الدخول من الهوم بيدج عن طريق نوع العقار / مرحلة التشطيب
            'govCounts' => Property::published()->selectRaw('governorate, count(*) c')->groupBy('governorate')->orderByDesc('c')->limit(12)->pluck('c', 'governorate'),
            'typeCounts' => Property::published()->selectRaw('property_type, count(*) c')->groupBy('property_type')->pluck('c', 'property_type'),
            'stageCounts' => Property::published()->selectRaw('finishing_stage, count(*) c')->groupBy('finishing_stage')->pluck('c', 'finishing_stage'),
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
