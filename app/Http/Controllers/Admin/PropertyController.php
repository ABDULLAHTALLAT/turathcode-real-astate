<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $properties = Property::with(['images', 'owner'])
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->query('q'), fn ($q, $v) => $q->where('title', 'like', "%$v%"))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.properties', compact('properties'));
    }

    public function approve(Property $property)
    {
        $property->update(['status' => 'approved', 'reject_reason' => null, 'approved_at' => now()]);

        return back()->with('success', 'تم نشر العقار.');
    }

    public function reject(Request $request, Property $property)
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:190']], [], ['reason' => 'سبب الرفض']);
        $property->update(['status' => 'rejected', 'reject_reason' => $data['reason']]);

        return back()->with('success', 'تم رفض العقار وإبلاغ المالك بالسبب.');
    }

    public function feature(Property $property)
    {
        $property->update(['is_featured' => ! $property->is_featured]);

        return back()->with('success', 'تم تحديث التمييز.');
    }

    public function destroy(Property $property)
    {
        $property->delete();

        return back()->with('success', 'تم حذف العقار.');
    }
}
