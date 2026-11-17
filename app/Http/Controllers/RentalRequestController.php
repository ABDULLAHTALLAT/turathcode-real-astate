<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\RentalRequest;
use Illuminate\Http\Request;

class RentalRequestController extends Controller
{
    /** قائمة الطلبات (للمستأجر: اللي بعتها، للمالك: الواردة) */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = $user->isOwner() ? $user->receivedRequests() : $user->sentRequests();

        return view('account.requests', [
            'requests' => $query->with(['property.images', 'tenant', 'owner'])->latest()->paginate(10),
        ]);
    }

    /** المستأجر يقدم طلب إيجار بعرضه ومدته والغرض من الشقة */
    public function store(Request $request, Property $property)
    {
        $user = $request->user();
        abort_unless($user->isTenant(), 403, 'التقديم متاح لحسابات المستأجرين فقط.');
        abort_unless($property->status === 'approved', 404);

        $data = $request->validate([
            'purpose' => ['required', 'in:'.implode(',', array_keys(catalog('purposes')))],
            'marital_status' => ['required', 'in:single,married'],
            'occupants' => ['nullable', 'integer', 'min:1', 'max:30'],
            'proposed_rent' => ['required', 'integer', 'min:1'],
            'lease_years' => ['required', 'integer', 'min:'.$property->min_lease_years, 'max:'.$property->max_lease_years],
            'start_date' => ['nullable', 'date', 'after_or_equal:today'],
            'message' => ['nullable', 'string', 'max:1500'],
            'terms' => ['accepted'],
        ], [
            'lease_years.min' => 'أقل مدة إيجار لهذا العقار '.$property->min_lease_years.' سنة.',
            'lease_years.max' => 'أقصى مدة إيجار لهذا العقار '.$property->max_lease_years.' سنة.',
            'terms.accepted' => 'لازم توافق على شروط المنصة ونسبتها.',
        ], [
            'purpose' => 'الغرض من العقار', 'marital_status' => 'الحالة الاجتماعية',
            'proposed_rent' => 'الإيجار الشهري المقترح', 'lease_years' => 'مدة الإيجار',
        ]);

        if ($property->allowed_purposes && ! in_array($data['purpose'], $property->allowed_purposes, true)) {
            return back()->withErrors(['purpose' => 'الغرض ده مش مسموح بيه في العقار ده.'])->withInput();
        }

        if ($user->sentRequests()->where('property_id', $property->id)->whereIn('status', ['pending', 'negotiating', 'accepted'])->exists()) {
            return back()->with('error', 'عندك طلب شغال بالفعل على العقار ده.');
        }

        RentalRequest::create([
            'property_id' => $property->id,
            'tenant_id' => $user->id,
            'owner_id' => $property->owner_id,
            'purpose' => $data['purpose'],
            'marital_status' => $data['marital_status'],
            'occupants' => $data['occupants'] ?? null,
            'proposed_rent' => $data['proposed_rent'],
            'lease_years' => $data['lease_years'],
            'start_date' => $data['start_date'] ?? null,
            'message' => mask_contacts($data['message'] ?? null),
            'last_offer_by' => 'tenant',
            'status' => 'pending',
            'tenant_terms_accepted_at' => now(),
        ]);

        return redirect()->route('requests.index')->with('success', 'تم إرسال طلبك للمالك. هتوصلك النتيجة هنا في لوحتك.');
    }

    public function show(Request $request, RentalRequest $rentalRequest)
    {
        $this->authorizeParty($request, $rentalRequest);
        $rentalRequest->load(['property.images', 'tenant.profile', 'owner']);

        return view('account.request-show', ['rr' => $rentalRequest]);
    }

    /** موافقة الطرف المطلوب منه الرد على آخر عرض */
    public function accept(Request $request, RentalRequest $rentalRequest)
    {
        $this->authorizeParty($request, $rentalRequest);
        abort_unless($rentalRequest->canRespond($request->user()), 403, 'مش دورك في الرد على الطلب ده.');
        abort_if($rentalRequest->property->status !== 'approved', 403, 'العقار لم يعد متاحاً.');

        $rentalRequest->accept();

        return back()->with('success', 'تم الاتفاق على الشروط! الخطوة الجاية: سداد نسبة المنصة لإتمام العقد وفتح بيانات التواصل.');
    }

    /** عرض مضاد: يعدّل الإيجار والمدة والدور يروح للطرف التاني */
    public function counter(Request $request, RentalRequest $rentalRequest)
    {
        $this->authorizeParty($request, $rentalRequest);
        abort_unless($rentalRequest->canRespond($request->user()), 403);
        $property = $rentalRequest->property;

        $data = $request->validate([
            'proposed_rent' => ['required', 'integer', 'min:1'],
            'lease_years' => ['required', 'integer', 'min:'.$property->min_lease_years, 'max:'.$property->max_lease_years],
        ], [], ['proposed_rent' => 'الإيجار الشهري', 'lease_years' => 'المدة']);

        $rentalRequest->update($data + [
            'status' => 'negotiating',
            'last_offer_by' => $request->user()->id === $rentalRequest->owner_id ? 'owner' : 'tenant',
        ]);

        return back()->with('success', 'تم إرسال العرض الجديد للطرف الآخر.');
    }

    public function reject(Request $request, RentalRequest $rentalRequest)
    {
        $this->authorizeParty($request, $rentalRequest);
        abort_unless($rentalRequest->canRespond($request->user()), 403);

        $rentalRequest->update(['status' => 'rejected']);

        return back()->with('success', 'تم رفض الطلب.');
    }

    public function cancel(Request $request, RentalRequest $rentalRequest)
    {
        abort_unless($rentalRequest->tenant_id === $request->user()->id, 403);
        abort_unless(in_array($rentalRequest->status, ['pending', 'negotiating'], true), 403);

        $rentalRequest->update(['status' => 'cancelled']);

        return back()->with('success', 'تم إلغاء الطلب.');
    }

    private function authorizeParty(Request $request, RentalRequest $rr): void
    {
        $u = $request->user();
        abort_unless($u->isAdmin() || $u->id === $rr->owner_id || $u->id === $rr->tenant_id, 403);
    }
}
