<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('pagination.arrows');
        Paginator::defaultSimpleView('pagination.arrows');
        \Carbon\Carbon::setLocale('ar');
    }
}
