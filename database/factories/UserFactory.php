<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '01'.fake()->randomElement([0, 1, 2, 5]).fake()->unique()->numerify('########'),
            'password' => 'password',
            'role' => 'tenant',
            'is_active' => true,
        ];
    }

    public function admin(): static { return $this->state(['role' => 'admin']); }
    public function owner(): static { return $this->state(['role' => 'owner']); }
    public function tenant(): static { return $this->state(['role' => 'tenant']); }
}
