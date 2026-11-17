<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\RentalRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

/** طلبات إيجار بكل الحالات: انتظار، تفاوض، اتفاق، مكتمل، مرفوض */
class RentalRequestsSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = User::where('role', 'tenant')->where('is_active', true)->get();
        $props = Property::where('status', 'approved')->get();

        foreach ($props->random(min(70, $props->count())) as $i => $p) {
            $tenant = $tenants->random();
            $rent = (int) round($p->offered_rent * fake()->randomFloat(2, 0.8, 1.05) / 250) * 250;

            $rr = RentalRequest::create([
                'property_id' => $p->id, 'tenant_id' => $tenant->id, 'owner_id' => $p->owner_id,
                'purpose' => $p->allowed_purposes[0] ?? 'family',
                'marital_status' => fake()->randomElement(['single', 'married']),
                'occupants' => fake()->numberBetween(1, 6),
                'proposed_rent' => $rent,
                'lease_years' => fake()->numberBetween($p->min_lease_years, $p->max_lease_years),
                'start_date' => now()->addDays(fake()->numberBetween(7, 60)),
                'message' => 'مهتم بالعقار ومستعد أكمل التشطيب على حسابي مقابل مدة إيجار طويلة.',
                'last_offer_by' => 'tenant', 'status' => 'pending', 'tenant_terms_accepted_at' => now(),
            ]);

            match ($i % 6) {
                1 => $rr->update(['status' => 'negotiating', 'last_offer_by' => 'owner', 'proposed_rent' => $p->offered_rent]),
                2 => $rr->accept(),
                3 => tap($rr)->accept()->markPaid('owner'),
                4 => $rr->update(['status' => 'rejected']),
                5 => $rr->update(['status' => 'cancelled']),
                default => null,
            };
        }
    }
}
