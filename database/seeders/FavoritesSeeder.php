<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoritesSeeder extends Seeder
{
    public function run(): void
    {
        $ids = Property::where('status', 'approved')->pluck('id');

        User::where('role', 'tenant')->get()->each(function (User $u) use ($ids) {
            $u->favorites()->syncWithoutDetaching($ids->random(min(fake()->numberBetween(2, 8), $ids->count()))->all());
        });
    }
}
