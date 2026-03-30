<?php

namespace App\Providers;

use App\Models\MenuCategory;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    //
    }

    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $view->with('navMenuCategories', MenuCategory::all());
        });
    }
}
