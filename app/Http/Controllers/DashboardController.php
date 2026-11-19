<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\RentalRequest;
use App\Models\User;
use App\Services\AdminAnalytics;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /** كل نوع حساب له داش بورد مختلف */
    public function __invoke(Request $request)
    {
        $user = $request->user();

        return match ($user->role) {
            'admin' => $this->admin($request),
            'owner' => $this->owner($user),
            default => $this->tenant($user),
        };
    }

    private function tenant(User $user)
    {
        $requests = $user->sentRequests()->with('property.images');

        return view('dashboards.tenant', [
            'user' => $user,
            'profile' => $user->profile,
            'favorites' => $user->favorites()->with('images')->latest('favorites.created_at')->take(4)->get(),
            'recent' => (clone $requests)->latest()->take(5)->get(),
            'counts' => [
                'total' => $user->sentRequests()->count(),
                'open' => $user->sentRequests()->whereIn('status', ['pending', 'negotiating'])->count(),
                'accepted' => $user->sentRequests()->whereIn('status', ['accepted', 'completed'])->count(),
                'favorites' => $user->favorites()->count(),
            ],
            'waiting' => $user->sentRequests()->where('status', 'negotiating')->where('last_offer_by', 'owner')->count(),
        ]);
    }

    private function owner(User $user)
    {
        return view('dashboards.owner', [
            'user' => $user,
            'counts' => [
                'properties' => $user->properties()->count(),
                'published' => $user->properties()->where('status', 'approved')->count(),
                'pending' => $user->properties()->where('status', 'pending')->count(),
                'rented' => $user->properties()->where('status', 'rented')->count(),
                'views' => (int) $user->properties()->sum('views'),
                'requests' => $user->receivedRequests()->count(),
            ],
            'needReply' => $user->receivedRequests()->where('status', 'pending')->count()
                + $user->receivedRequests()->where('status', 'negotiating')->where('last_offer_by', 'tenant')->count(),
            'recent' => $user->receivedRequests()->with(['property.images', 'tenant'])->latest()->take(6)->get(),
            'myProperties' => $user->properties()->with('images')->latest()->take(4)->get(),
            // إجمالي قيمة عقودك المكتملة وما تم دفعه كنسبة منصة
            'contractsValue' => (int) $user->receivedRequests()->where('status', 'completed')->sum('total_value'),
            'platformFees' => (int) $user->receivedRequests()->whereIn('status', ['accepted', 'completed'])->sum('owner_commission'),
        ]);
    }

    private function admin(Request $request)
    {
        $analytics = new AdminAnalytics((int) $request->query('range', 30));

        return view('dashboards.admin', ['a' => $analytics->build(), 'ranges' => AdminAnalytics::RANGES]);
    }
}
