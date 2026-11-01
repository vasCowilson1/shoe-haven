<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        if (request()->header('x-forwarded-proto') === 'https') {
            URL::forceScheme('https');
        }

        View::composer(['partials.navbar', 'partials.footer', 'layouts.public'], function ($view) {
            $view->with('categories', Category::withCount('products')->get());
        });
    }
}
