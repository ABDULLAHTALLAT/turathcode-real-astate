<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('admin.settings', [
            'owner' => Setting::commission('owner'),
            'tenant' => Setting::commission('tenant'),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'commission_owner_percent' => ['required', 'numeric', 'min:0', 'max:50'],
            'commission_tenant_percent' => ['required', 'numeric', 'min:0', 'max:50'],
        ], [], [
            'commission_owner_percent' => 'نسبة المالك', 'commission_tenant_percent' => 'نسبة المستأجر',
        ]);

        foreach ($data as $k => $v) {
            Setting::put($k, $v);
        }

        return back()->with('success', 'تم حفظ النسب. هتتطبق على الاتفاقات الجديدة فقط (القديمة بتفضل بنسبتها وقت الاتفاق).');
    }
}
