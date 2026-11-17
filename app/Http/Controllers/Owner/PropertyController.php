<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\FinishingItem;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $properties = $request->user()->properties()->with('images')->withCount('requests')->latest()->paginate(10);

        return view('owner.properties.index', compact('properties'));
    }

    public function create()
    {
        return view('owner.properties.form', [
            'property' => new Property(['min_lease_years' => 1, 'max_lease_years' => 5, 'finishing_by' => 'tenant']),
            'items' => [],
            'commission' => Setting::commission('owner'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, true);
        $property = $request->user()->properties()->create($data + ['status' => 'pending', 'terms_accepted_at' => now()]);

        $this->syncFinishing($property, $request);
        $this->storeImages($property, $request);

        return redirect()->route('owner.properties.index')
            ->with('success', 'تم إرسال العقار للمراجعة. هيظهر للمستأجرين بمجرد موافقة إدارة المنصة.');
    }

    public function edit(Request $request, Property $property)
    {
        $this->authorizeOwner($request, $property);

        return view('owner.properties.form', [
            'property' => $property->load('images'),
            'items' => $property->finishingItems()->get()->keyBy('item_key'),
            'commission' => Setting::commission('owner'),
        ]);
    }

    public function update(Request $request, Property $property)
    {
        $this->authorizeOwner($request, $property);
        abort_if($property->status === 'rented', 403, 'العقار متأجر بالفعل ولا يمكن تعديله.');

        $property->update($this->validated($request, false) + ['status' => 'pending', 'reject_reason' => null]);

        $this->syncFinishing($property, $request);
        foreach ($property->images()->whereIn('id', (array) $request->input('remove_images', []))->get() as $img) {
            Storage::disk('public')->delete($img->path);
            $img->delete();
        }
        $this->storeImages($property, $request);

        return redirect()->route('owner.properties.index')->with('success', 'تم حفظ التعديلات وإرسالها للمراجعة من جديد.');
    }

    public function destroy(Request $request, Property $property)
    {
        $this->authorizeOwner($request, $property);
        abort_if($property->requests()->whereIn('status', ['accepted', 'completed'])->exists(), 403, 'عليه اتفاق جاري، تواصل مع الإدارة.');
        $property->delete();

        return back()->with('success', 'تم حذف العقار.');
    }

    public function toggleHidden(Request $request, Property $property)
    {
        $this->authorizeOwner($request, $property);
        abort_unless(in_array($property->status, ['approved', 'hidden']), 403);
        $property->update(['status' => $property->status === 'approved' ? 'hidden' : 'approved']);

        return back()->with('success', 'تم تحديث حالة الظهور.');
    }

    // ------------------------------------------------------------------

    private function authorizeOwner(Request $request, Property $property): void
    {
        abort_unless($property->owner_id === $request->user()->id, 403);
    }

    private function validated(Request $request, bool $creating): array
    {
        $c = fn (string $k) => implode(',', array_keys(catalog($k)));

        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'property_type' => ['required', 'in:'.$c('property_types')],
            'governorate' => ['required', 'in:'.implode(',', catalog('governorates'))],
            'city' => ['required', 'string', 'max:80'],
            'district' => ['nullable', 'string', 'max:120'],
            'street' => ['nullable', 'string', 'max:190'],
            'building_number' => ['nullable', 'string', 'max:30'],
            'floor_number' => ['nullable', 'string', 'max:30'],
            'unit_number' => ['nullable', 'string', 'max:30'],
            'landmark' => ['nullable', 'string', 'max:190'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'area_m2' => ['required', 'integer', 'min:10', 'max:100000'],
            'rooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'bathrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'floors_count' => ['nullable', 'integer', 'min:1', 'max:60'],
            'finishing_stage' => ['required', 'in:'.$c('finishing_stages')],
            'finishing_notes' => ['nullable', 'string', 'max:2000'],
            'description' => ['nullable', 'string', 'max:4000'],
            'utilities' => ['nullable', 'array'],
            'utilities.*' => ['in:'.$c('utilities')],
            'allowed_purposes' => ['required', 'array', 'min:1'],
            'allowed_purposes.*' => ['in:'.$c('purposes')],
            'vacant_since_years' => ['nullable', 'integer', 'min:0', 'max:80'],
            'market_rent' => ['required', 'integer', 'min:1'],
            'offered_rent' => ['required', 'integer', 'min:1', 'lte:market_rent'],
            'min_lease_years' => ['required', 'integer', 'min:1', 'max:30'],
            'max_lease_years' => ['required', 'integer', 'gte:min_lease_years', 'max:30'],
            'rent_free_months' => ['nullable', 'integer', 'min:0', 'max:24'],
            'annual_increase_percent' => ['nullable', 'integer', 'min:0', 'max:30'],
            'finishing_by' => ['required', 'in:'.$c('finishing_by')],
            'images' => [$creating ? 'required' : 'nullable', 'array', 'max:12'],
            'images.*' => ['image', 'max:5120'],
            'terms' => ['accepted'],
        ], [
            'offered_rent.lte' => 'السعر المعروض لازم يكون أقل من أو يساوي سعر السوق.',
            'images.required' => 'ضيف صورة واحدة على الأقل للعقار.',
            'terms.accepted' => 'لازم توافق على نسبة المنصة وشروط الإيجار عن طريقها.',
            'allowed_purposes.required' => 'اختار الغرض (أو الأغراض) المسموح بيها للعقار.',
        ], [
            'title' => 'عنوان الإعلان', 'governorate' => 'المحافظة', 'city' => 'المدينة / المركز',
            'area_m2' => 'المساحة', 'market_rent' => 'سعر السوق الحالي', 'offered_rent' => 'السعر المعروض', 'finishing_stage' => 'مرحلة التشطيب',
            'property_type' => 'نوع العقار',
        ]);

        // التواصل داخل المنصة فقط
        foreach (['title', 'description', 'finishing_notes', 'landmark'] as $f) {
            $data[$f] = mask_contacts($data[$f] ?? null);
        }
        $data['utilities'] = array_values($data['utilities'] ?? []);
        $data['rent_free_months'] = $data['rent_free_months'] ?? 0;
        $data['annual_increase_percent'] = $data['annual_increase_percent'] ?? 0;

        unset($data['images'], $data['terms']);

        return $data;
    }

    private function syncFinishing(Property $property, Request $request): void
    {
        $rows = (array) $request->input('finishing', []);

        foreach (catalog('finishing_items') as $key => $_) {
            $status = $rows[$key]['status'] ?? null;
            if (! in_array($status, array_keys(catalog('item_statuses')), true)) {
                FinishingItem::where(['property_id' => $property->id, 'item_key' => $key])->delete();
                continue;
            }
            FinishingItem::updateOrCreate(
                ['property_id' => $property->id, 'item_key' => $key],
                ['status' => $status, 'note' => mask_contacts(mb_substr((string) ($rows[$key]['note'] ?? ''), 0, 190)) ?: null]
            );
        }
    }

    private function storeImages(Property $property, Request $request): void
    {
        $sort = (int) $property->images()->max('sort');
        $hasCover = $property->images()->where('is_cover', true)->exists();

        foreach ((array) $request->file('images', []) as $file) {
            PropertyImage::create([
                'property_id' => $property->id,
                'path' => $file->store('properties', 'public'),
                'is_cover' => ! $hasCover,
                'sort' => ++$sort,
            ]);
            $hasCover = true;
        }
    }
}
