<?php

namespace Database\Seeders;

use App\Models\FinishingItem;
use App\Models\Property;
use App\Models\Setting;
use App\Models\TenantProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- الحسابات الأساسية (غيّر كلمات المرور قبل النشر!) ----
        $admin = User::updateOrCreate(['email' => 'admin@turathcode.com'], [
            'name' => 'إدارة بيتك في ايدك', 'phone' => '01000000000', 'password' => 'Admin@12345', 'role' => 'admin',
        ]);

        Setting::put('commission_owner_percent', config('site.commission_owner_percent'));
        Setting::put('commission_tenant_percent', config('site.commission_tenant_percent'));

        if (! app()->environment('local', 'testing')) {
            return;
        }

        // ---- بيانات تجريبية للتطوير فقط ----
        $owner = User::updateOrCreate(['email' => 'owner@example.com'], [
            'name' => 'محمد عبد الله', 'phone' => '01011111111', 'password' => 'password', 'role' => 'owner',
            'governorate' => 'القاهرة', 'city' => 'مدينة نصر',
        ]);
        $tenant = User::updateOrCreate(['email' => 'tenant@example.com'], [
            'name' => 'أحمد سمير', 'phone' => '01122222222', 'password' => 'password', 'role' => 'tenant',
            'governorate' => 'الجيزة', 'city' => '6 أكتوبر',
        ]);
        TenantProfile::updateOrCreate(['user_id' => $tenant->id], [
            'marital_status' => 'married', 'family_members' => 4, 'purpose' => 'family', 'job' => 'مهندس',
            'budget_min' => 5000, 'budget_max' => 12000, 'preferred_governorate' => 'القاهرة', 'preferred_lease_years' => 3,
        ]);

        $samples = [
            ['شقة 150م على المحارة بمدينة نصر', 'apartment', 'القاهرة', 'مدينة نصر', 'الحي السابع', 'plaster', 150, 3, 2, 20000, 15000, ['family', 'office'], 3],
            ['فيلا مفتوحة على الطوب في الشيخ زايد', 'villa', 'الجيزة', 'الشيخ زايد', 'الحي الثالث', 'red_brick', 420, 6, 4, 45000, 32000, ['family', 'clinic'], 5],
            ['مبنى 5 أدوار هيكل خرساني بالمنصورة', 'building', 'الدقهلية', 'المنصورة', 'حي الجامعة', 'concrete', 900, null, null, 60000, 38000, ['school', 'office'], 7],
            ['محل 60م نصف تشطيب بمصر الجديدة', 'shop', 'القاهرة', 'مصر الجديدة', 'شارع الميرغني', 'half_finished', 60, null, 1, 18000, 14000, ['shop'], 2],
            ['شقة 110م تشطيب كامل بالإسكندرية', 'apartment', 'الإسكندرية', 'سموحة', 'شارع فوزي معاذ', 'finished', 110, 2, 1, 9000, 7500, ['family', 'singles'], 1],
        ];

        foreach ($samples as $i => [$title, $type, $gov, $city, $district, $stage, $area, $rooms, $baths, $market, $offered, $purposes, $vacant]) {
            $p = Property::create([
                'owner_id' => $owner->id, 'title' => $title, 'property_type' => $type, 'governorate' => $gov, 'city' => $city,
                'district' => $district, 'street' => 'شارع رئيسي', 'building_number' => (string) (10 + $i), 'floor_number' => 'الثالث',
                'unit_number' => '5', 'landmark' => 'بجوار مسجد', 'area_m2' => $area, 'rooms' => $rooms, 'bathrooms' => $baths,
                'floors_count' => $type === 'building' ? 5 : null, 'finishing_stage' => $stage,
                'finishing_notes' => 'العقار فاضي من سنين ومحتاج استكمال التشطيبات حسب الجدول المرفق.',
                'description' => 'عقار موجود ومعطل من فترة، والمالك مستعد لتأجيره بسعر مخفض مقابل استكمال المستأجر للتشطيب.',
                'utilities' => ['electricity', 'water', 'sewage'], 'allowed_purposes' => $purposes,
                'vacant_since_years' => $vacant, 'market_rent' => $market, 'offered_rent' => $offered,
                'min_lease_years' => 2, 'max_lease_years' => 7, 'rent_free_months' => 3, 'annual_increase_percent' => 5,
                'finishing_by' => 'tenant', 'status' => 'approved', 'is_featured' => $i < 3,
                'terms_accepted_at' => now(), 'approved_at' => now(),
            ]);

            $level = catalog('finishing_stages')[$stage]['level'];
            foreach (array_keys(catalog('finishing_items')) as $n => $key) {
                $status = match (true) {
                    $level >= 5 => 'done',
                    in_array($key, ['structure']) => 'done',
                    in_array($key, ['bricks']) && $level >= 2 => 'done',
                    in_array($key, ['plaster']) && $level >= 3 => 'done',
                    $level >= 4 && $n % 3 === 0 => 'partial',
                    default => 'missing',
                };
                FinishingItem::create(['property_id' => $p->id, 'item_key' => $key, 'status' => $status]);
            }
        }
    }
}
