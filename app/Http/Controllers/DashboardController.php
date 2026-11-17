<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\RentalRequest;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /** كل نوع حساب له داش بورد مختلف */
    public function __invoke(Request $request)
    {
        $user = $request->user();

        return match ($user->role) {
            'admin' => $this->admin(),
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

    private function admin()
    {
        $done = RentalRequest::where('status', 'completed');

        return view('dashboards.admin', [
            'counts' => [
                'users' => User::where('role', '!=', 'admin')->count(),
                'owners' => User::where('role', 'owner')->count(),
                'tenants' => User::where('role', 'tenant')->count(),
                'pendingProps' => Property::where('status', 'pending')->count(),
                'published' => Property::where('status', 'approved')->count(),
                'rented' => Property::where('status', 'rented')->count(),
                'openRequests' => RentalRequest::whereIn('status', ['pending', 'negotiating'])->count(),
                'awaitingPay' => RentalRequest::where('status', 'accepted')->count(),
            ],
            // أرباح المنصة
            'earnings' => [
                'collected' => RentalRequest::whereIn('status', ['accepted', 'completed'])->get()->sum->paid_earnings,
                'expected' => (int) RentalRequest::where('status', 'accepted')->selectRaw('sum(coalesce(owner_commission,0)+coalesce(tenant_commission,0)) s')->value('s')
                    - (int) RentalRequest::where('status', 'accepted')->get()->sum->paid_earnings,
                'contracts' => (int) $done->sum('total_value'),
            ],
            'pendingProperties' => Property::where('status', 'pending')->with(['images', 'owner'])->latest()->take(5)->get(),
            'awaitingPayment' => RentalRequest::where('status', 'accepted')->with(['property', 'tenant', 'owner'])->latest()->take(6)->get(),
            'latestUsers' => User::where('role', '!=', 'admin')->latest()->take(6)->get(),
        ]);
    }
}
