<?php

namespace Database\Factories;

use App\Models\User;
use App\Support\ArabicData;
use App\Support\EgyptData;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $loc = EgyptData::random();

        return [
            'name' => ArabicData::name(),
            'email' => fake()->unique()->userName().fake()->unique()->numerify('##').'@'.fake()->randomElement(['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com']),
            'phone' => '01'.fake()->randomElement([0, 1, 2, 5]).fake()->unique()->numerify('########'),
            'password' => 'password',
            'role' => 'tenant',
            'governorate' => $loc['governorate'],
            'city' => $loc['city'],
            'is_active' => true,
            'email_verified_at' => now(),
        ];
    }

    public function admin(): static { return $this->state(['role' => 'admin']); }
    public function owner(): static { return $this->state(['role' => 'owner', 'bio' => 'مالك عقارات معطلة وعايز أستفيد منها بإيجار طويل المدى.']); }
    public function tenant(): static { return $this->state(['role' => 'tenant']); }
    public function inactive(): static { return $this->state(['is_active' => false]); }
}
