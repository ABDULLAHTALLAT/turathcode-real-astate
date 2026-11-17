<?php

namespace App\Http\Controllers;

use App\Models\TenantProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [], ['login' => 'الإيميل أو رقم الموبايل', 'password' => 'كلمة المرور']);

        $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (! Auth::attempt([$field => $data['login'], 'password' => $data['password']], $request->boolean('remember'))) {
            return back()->withErrors(['login' => 'بيانات الدخول غير صحيحة.'])->onlyInput('login');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister(Request $request)
    {
        return view('auth.register', ['role' => in_array($request->query('role'), ['owner', 'tenant']) ? $request->query('role') : 'tenant']);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'role' => ['required', 'in:owner,tenant'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['required', 'regex:/^01[0125][0-9]{8}$/', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'governorate' => ['nullable', 'string', 'max:60'],
            'terms' => ['accepted'],
        ], [
            'phone.regex' => 'اكتب رقم موبايل مصري صحيح (مثال: 01012345678).',
            'terms.accepted' => 'لازم توافق على الشروط وسياسة نسبة المنصة.',
        ], [
            'name' => 'الاسم', 'email' => 'الإيميل', 'phone' => 'رقم الموبايل', 'password' => 'كلمة المرور',
        ]);

        $user = User::create([
            'name' => $data['name'], 'email' => $data['email'], 'phone' => $data['phone'],
            'password' => $data['password'], 'role' => $data['role'], 'governorate' => $data['governorate'] ?? null,
        ]);

        if ($user->isTenant()) {
            TenantProfile::create(['user_id' => $user->id]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'أهلاً بيك في بيتك في ايدك! كمّل بيانات ملفك الشخصي.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
