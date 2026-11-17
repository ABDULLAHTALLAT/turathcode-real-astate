<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PropertyImage> */
class PropertyImageFactory extends Factory
{
    protected $model = PropertyImage::class;

    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'path' => 'images/covers/apartment.svg',
            'is_cover' => false,
            'sort' => 0,
        ];
    }

    public function cover(): static { return $this->state(['is_cover' => true]); }
}
