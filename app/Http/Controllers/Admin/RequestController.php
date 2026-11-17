<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentalRequest;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $requests = RentalRequest::with(['property', 'tenant', 'owner'])
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.requests', compact('requests'));
    }

    /** تسجيل سداد نسبة المنصة من المالك أو المستأجر */
    public function markPaid(Request $request, RentalRequest $rentalRequest)
    {
        $party = $request->validate(['party' => ['required', 'in:owner,tenant']])['party'];
        abort_unless($rentalRequest->status === 'accepted', 403);

        $rentalRequest->markPaid($party);

        return back()->with('success', $rentalRequest->fresh()->status === 'completed'
            ? 'تم سداد الطرفين: اكتمل الاتفاق وتم إظهار بيانات التواصل وتأجير العقار.'
            : 'تم تسجيل السداد. في انتظار الطرف الآخر.');
    }

    public function note(Request $request, RentalRequest $rentalRequest)
    {
        $rentalRequest->update(['admin_note' => $request->validate(['admin_note' => ['nullable', 'string', 'max:2000']])['admin_note']]);

        return back()->with('success', 'تم حفظ الملاحظة.');
    }
}
