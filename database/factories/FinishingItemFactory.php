<?php

namespace Database\Factories;

use App\Models\FinishingItem;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<FinishingItem> */
class FinishingItemFactory extends Factory
{
    protected $model = FinishingItem::class;

    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'item_key' => fake()->randomElement(array_keys(catalog('finishing_items'))),
            'status' => fake()->randomElement(['done', 'partial', 'missing', 'na']),
            'note' => null,
        ];
    }

    public function missing(): static { return $this->state(['status' => 'missing']); }
    public function done(): static { return $this->state(['status' => 'done']); }
}
