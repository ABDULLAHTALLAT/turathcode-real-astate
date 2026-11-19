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

    /** تصدير الطلبات والعمولات CSV (يفتح في Excel بالعربي) */
    public function export(Request $request)
    {
        $rows = RentalRequest::with(['property', 'tenant', 'owner'])
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))->latest()->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['الكود', 'العقار', 'المالك', 'المستأجر', 'الحالة', 'الإيجار الشهري', 'السنين', 'إجمالي العقد', 'نسبة المالك', 'نسبة المستأجر', 'المحصّل', 'تاريخ الطلب']);
            foreach ($rows as $r) {
                fputcsv($out, [$r->code, $r->property?->title, $r->owner?->name, $r->tenant?->name, $r->status_label, $r->proposed_rent, $r->lease_years,
                    $r->total_value, $r->owner_commission, $r->tenant_commission, $r->paid_earnings, $r->created_at->format('Y-m-d')]);
            }
            fclose($out);
        }, 'bayt-requests-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
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
