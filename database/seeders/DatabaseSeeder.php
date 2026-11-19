<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AdminSeeder::class);

        // بيانات تجريبية للتطوير فقط (مش بتتشغل في production)
        if (! app()->environment('local', 'testing')) {
            return;
        }

        $this->call([
            DemoUsersSeeder::class,
            EgyptPropertiesSeeder::class,
            RentalRequestsSeeder::class,
            FavoritesSeeder::class,
            MessagesSeeder::class,
            DemoTimelineSeeder::class,
        ]);
    }
}
