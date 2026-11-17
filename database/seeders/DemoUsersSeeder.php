<?php

namespace Database\Seeders;

use App\Models\TenantProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

/** حسابات ثابتة للدخول التجريبي + عدد كبير من المُلّاك والمستأجرين من كل محافظات مصر */
class DemoUsersSeeder extends Seeder
{
    public const OWNERS = 40;
    public const TENANTS = 60;

    public function run(): void
    {
        $owner = User::updateOrCreate(['email' => 'owner@example.com'], [
            'name' => 'محمد عبد الله', 'phone' => '01011111111', 'password' => 'password', 'role' => 'owner',
            'governorate' => 'القاهرة', 'city' => 'مدينة نصر',
        ]);
        $tenant = User::updateOrCreate(['email' => 'tenant@example.com'], [
            'name' => 'أحمد سمير', 'phone' => '01122222222', 'password' => 'password', 'role' => 'tenant',
            'governorate' => 'الجيزة', 'city' => '6 أكتوبر',
        ]);
        TenantProfile::updateOrCreate(['user_id' => $tenant->id], [
            'marital_status' => 'married', 'family_members' => 4, 'purpose' => 'family', 'job' => 'مهندس',
            'budget_min' => 5000, 'budget_max' => 12000, 'preferred_governorate' => 'القاهرة', 'preferred_lease_years' => 3,
        ]);

        User::factory()->owner()->count(self::OWNERS)->create();
        User::factory()->tenant()->count(self::TENANTS)->create()
            ->each(fn (User $u) => TenantProfile::factory()->create(['user_id' => $u->id]));
        User::factory()->inactive()->count(3)->tenant()->create();
    }
}
