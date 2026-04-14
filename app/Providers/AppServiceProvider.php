<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use App\Observers\UserObserver;

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
        Paginator::useBootstrapFive();
        User::observe(UserObserver::class);

        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $categories = \App\Models\Category::where('user_id', auth()->id())
                    ->with(['color', 'icon'])
                    ->get();
                $view->with('sidebarCategories', $categories);
                $view->with('unreadNotificationsCount', auth()->user()->unreadNotifications->count());
            }
        });
    }
}
