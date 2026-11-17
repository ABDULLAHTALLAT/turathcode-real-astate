<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    private const LIMIT = 4;

    public function index(Request $request)
    {
        $ids = $request->session()->get('compare', []);
        $properties = Property::with(['images', 'finishingItems'])->whereIn('id', $ids)->get();

        return view('account.compare', compact('properties'));
    }

    public function toggle(Request $request, Property $property)
    {
        $ids = $request->session()->get('compare', []);

        if (in_array($property->id, $ids)) {
            $ids = array_values(array_diff($ids, [$property->id]));
            $msg = 'تم إزالة العقار من المقارنة.';
        } elseif (count($ids) >= self::LIMIT) {
            return back()->with('error', 'تقدر تقارن لحد '.self::LIMIT.' عقارات بس.');
        } else {
            $ids[] = $property->id;
            $msg = 'تمت إضافة العقار للمقارنة.';
        }

        $request->session()->put('compare', $ids);

        return back()->with('success', $msg);
    }

    public function clear(Request $request)
    {
        $request->session()->forget('compare');

        return redirect()->route('compare');
    }
}
