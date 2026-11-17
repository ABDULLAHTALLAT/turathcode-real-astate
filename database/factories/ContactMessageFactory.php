<?php

namespace Database\Factories;

use App\Models\ContactMessage;
use App\Support\ArabicData;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ContactMessage> */
class ContactMessageFactory extends Factory
{
    protected $model = ContactMessage::class;

    public function definition(): array
    {
        return [
            'name' => ArabicData::name(),
            'phone' => '01'.fake()->randomElement([0, 1, 2, 5]).fake()->numerify('########'),
            'email' => fake()->boolean(70) ? fake()->unique()->safeEmail() : null,
            'message' => ArabicData::pick(ArabicData::MESSAGES),
            'is_read' => fake()->boolean(35),
        ];
    }
}
