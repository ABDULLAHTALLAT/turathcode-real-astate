<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\Owner;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RentalRequestController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

// ---------------- الموقع العام ----------------
Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/properties', [SiteController::class, 'index'])->name('properties.index');
Route::get('/properties/count', [SiteController::class, 'count'])->name('properties.count');
Route::get('/properties/{property:slug}', [SiteController::class, 'show'])->name('properties.show');
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, \App\Http\Middleware\SetLocale::LOCALES, true)) {
        session(['locale' => $locale]);
    }

    return redirect()->back(fallback: route('home'));
})->whereIn('locale', \App\Http\Middleware\SetLocale::LOCALES)->name('lang');

Route::get('/how-it-works', [SiteController::class, 'how'])->name('how');
Route::get('/about', [SiteController::class, 'about'])->name('about');
Route::get('/terms', [SiteController::class, 'terms'])->name('terms');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
Route::post('/contact', [SiteController::class, 'contactSend'])->middleware('throttle:6,1')->name('contact.send');

// ---------------- الدخول والتسجيل ----------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ---------------- بعد تسجيل الدخول ----------------
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // طلبات الإيجار والتفاوض (المالك والمستأجر)
    Route::get('/requests', [RentalRequestController::class, 'index'])->middleware('role:owner,tenant')->name('requests.index');
    Route::get('/requests/{rentalRequest}', [RentalRequestController::class, 'show'])->name('requests.show');
    Route::post('/requests/{rentalRequest}/accept', [RentalRequestController::class, 'accept'])->middleware('role:owner,tenant')->name('requests.accept');
    Route::post('/requests/{rentalRequest}/counter', [RentalRequestController::class, 'counter'])->middleware('role:owner,tenant')->name('requests.counter');
    Route::post('/requests/{rentalRequest}/reject', [RentalRequestController::class, 'reject'])->middleware('role:owner,tenant')->name('requests.reject');
    Route::post('/requests/{rentalRequest}/cancel', [RentalRequestController::class, 'cancel'])->middleware('role:tenant')->name('requests.cancel');

    // المستأجر: مفضلة + مقارنة + تقديم طلب
    Route::middleware('role:tenant')->group(function () {
        Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites');
        Route::post('/favorites/{property}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
        Route::post('/properties/{property}/request', [RentalRequestController::class, 'store'])->middleware('throttle:10,1')->name('requests.store');
    });

    // المالك: إدارة عقاراته
    Route::middleware('role:owner')->prefix('owner')->name('owner.')->group(function () {
        Route::resource('properties', Owner\PropertyController::class)->except('show');
        Route::post('properties/{property}/toggle', [Owner\PropertyController::class, 'toggleHidden'])->name('properties.toggle');
    });

    // الأدمن (صاحب المنصة)
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('properties', [Admin\PropertyController::class, 'index'])->name('properties');
        Route::post('properties/{property}/approve', [Admin\PropertyController::class, 'approve'])->name('properties.approve');
        Route::post('properties/{property}/reject', [Admin\PropertyController::class, 'reject'])->name('properties.reject');
        Route::post('properties/{property}/feature', [Admin\PropertyController::class, 'feature'])->name('properties.feature');
        Route::delete('properties/{property}', [Admin\PropertyController::class, 'destroy'])->name('properties.destroy');

        Route::get('users', [Admin\UserController::class, 'index'])->name('users');
        Route::post('users/{user}/toggle', [Admin\UserController::class, 'toggle'])->name('users.toggle');

        Route::get('requests', [Admin\RequestController::class, 'index'])->name('requests');
        Route::get('requests/export', [Admin\RequestController::class, 'export'])->name('requests.export');
        Route::post('requests/{rentalRequest}/paid', [Admin\RequestController::class, 'markPaid'])->name('requests.paid');
        Route::post('requests/{rentalRequest}/note', [Admin\RequestController::class, 'note'])->name('requests.note');

        Route::get('settings', [Admin\SettingsController::class, 'edit'])->name('settings');
        Route::put('settings', [Admin\SettingsController::class, 'update'])->name('settings.update');

        Route::get('messages', [Admin\MessageController::class, 'index'])->name('messages');
        Route::post('messages/{message}/read', [Admin\MessageController::class, 'read'])->name('messages.read');
    });
});

// المقارنة (مفتوحة للزوار بالجلسة)
Route::get('/compare', [CompareController::class, 'index'])->name('compare');
Route::post('/compare/{property}', [CompareController::class, 'toggle'])->name('compare.toggle');
Route::post('/compare', [CompareController::class, 'clear'])->name('compare.clear');
