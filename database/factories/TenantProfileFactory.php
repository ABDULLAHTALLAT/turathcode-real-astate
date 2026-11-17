<?php

namespace Database\Factories;

use App\Models\TenantProfile;
use App\Models\User;
use App\Support\ArabicData;
use App\Support\EgyptData;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TenantProfile> */
class TenantProfileFactory extends Factory
{
    protected $model = TenantProfile::class;

    public function definition(): array
    {
        $married = fake()->boolean(55);
        $min = fake()->randomElement([1500, 2500, 4000, 6000, 8000, 12000]);

        return [
            'user_id' => User::factory()->tenant(),
            'marital_status' => $married ? 'married' : 'single',
            'family_members' => $married ? fake()->numberBetween(2, 7) : 1,
            'purpose' => $married ? 'family' : fake()->randomElement(array_keys(catalog('purposes'))),
            'job' => ArabicData::pick(ArabicData::JOBS),
            'budget_min' => $min,
            'budget_max' => $min + fake()->randomElement([2000, 4000, 8000, 15000]),
            'preferred_governorate' => fake()->randomElement(EgyptData::governorates()),
            'preferred_lease_years' => fake()->numberBetween(1, 7),
        ];
    }
}
