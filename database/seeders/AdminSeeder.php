<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // غيّر كلمة المرور قبل النشر!
        User::updateOrCreate(['email' => 'admin@turathcode.com'], [
            'name' => 'إدارة بيتك في ايدك', 'phone' => '01000000000', 'password' => 'Admin@12345', 'role' => 'admin',
        ]);

        Setting::put('commission_owner_percent', config('site.commission_owner_percent'));
        Setting::put('commission_tenant_percent', config('site.commission_tenant_percent'));
    }
}
