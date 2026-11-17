<?php

namespace App\Http\Controllers;

use App\Models\TenantProfile;
use App\Services\ImageProcessor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();

        return view('account.profile', ['user' => $user, 'profile' => $user->profile]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'regex:/^01[0125][0-9]{8}$/', Rule::unique('users', 'phone')->ignore($user->id)],
            'governorate' => ['nullable', 'string', 'max:60'],
            'city' => ['nullable', 'string', 'max:80'],
            'bio' => ['nullable', 'string', 'max:600'],
            'avatar' => ['nullable', 'image', 'max:3072'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ], ['phone.regex' => 'اكتب رقم موبايل مصري صحيح.'], [
            'name' => 'الاسم', 'email' => 'الإيميل', 'phone' => 'رقم الموبايل', 'avatar' => 'الصورة',
        ]);

        if ($request->hasFile('avatar')) {
            try {
                $data['avatar_media_id'] = app(ImageProcessor::class)->storeUpload($request->file('avatar'), $user, $user->name)->id;
            } catch (\InvalidArgumentException $e) {
                return back()->withErrors(['avatar' => $e->getMessage()])->withInput();
            }
            unset($data['avatar']);
        } else {
            unset($data['avatar']);
        }
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $data['bio'] = mask_contacts($data['bio'] ?? null);

        $user->update($data);

        if ($user->isTenant()) {
            $p = $request->validate([
                'marital_status' => ['nullable', 'in:single,married'],
                'family_members' => ['nullable', 'integer', 'min:1', 'max:30'],
                'purpose' => ['nullable', 'in:'.implode(',', array_keys(catalog('purposes')))],
                'job' => ['nullable', 'string', 'max:120'],
                'budget_min' => ['nullable', 'integer', 'min:0'],
                'budget_max' => ['nullable', 'integer', 'min:0'],
                'preferred_governorate' => ['nullable', 'string', 'max:60'],
                'preferred_lease_years' => ['nullable', 'integer', 'min:1', 'max:30'],
            ]);
            TenantProfile::updateOrCreate(['user_id' => $user->id], $p);
        }

        return back()->with('success', 'تم حفظ بياناتك.');
    }
}
