<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\ProductLayoutService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('productLayout', ProductLayoutService::getConfig());

            // Share real active categories across all frontend & layout views
            try {
                $navbarCategories = Category::where('is_active', true)
                    ->withCount('products')
                    ->orderBy('display_order')
                    ->orderBy('name')
                    ->get();
                $view->with('navbarCategories', $navbarCategories);
            } catch (\Throwable $e) {
                $view->with('navbarCategories', collect());
            }
        });
    }
}
