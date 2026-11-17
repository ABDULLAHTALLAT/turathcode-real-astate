<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\RentalRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<RentalRequest> */
class RentalRequestFactory extends Factory
{
    protected $model = RentalRequest::class;

    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'tenant_id' => User::factory()->tenant(),
            'owner_id' => User::factory()->owner(),
            'purpose' => fake()->randomElement(array_keys(catalog('purposes'))),
            'marital_status' => fake()->randomElement(['single', 'married']),
            'occupants' => fake()->numberBetween(1, 6),
            'proposed_rent' => fake()->numberBetween(3, 30) * 500,
            'lease_years' => fake()->numberBetween(1, 7),
            'start_date' => now()->addDays(fake()->numberBetween(7, 60)),
            'message' => 'مهتم بالعقار ومستعد أكمل التشطيب على حسابي مقابل مدة طويلة.',
            'last_offer_by' => 'tenant',
            'status' => 'pending',
            'tenant_terms_accepted_at' => now(),
        ];
    }

    public function negotiating(): static { return $this->state(['status' => 'negotiating', 'last_offer_by' => 'owner']); }
    public function rejected(): static { return $this->state(['status' => 'rejected']); }
}
