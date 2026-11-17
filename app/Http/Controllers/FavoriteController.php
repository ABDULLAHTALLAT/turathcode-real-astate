<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $properties = $request->user()->favorites()->with('images')->latest('favorites.created_at')->get();

        return view('account.favorites', compact('properties'));
    }

    public function toggle(Request $request, Property $property)
    {
        $request->user()->favorites()->toggle($property->id);

        return back()->with('success', 'تم تحديث المفضلة.');
    }
}
