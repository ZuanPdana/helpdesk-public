<?php

namespace App\Providers;

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
        View::composer('layouts.partials.dashboard-shell', function ($view): void {
            $user = request()->user();

            if (! $user) {
                $view->with([
                    'shellNotifications' => collect(),
                    'shellUnreadNotificationCount' => 0,
                ]);

                return;
            }

            $view->with([
                'shellNotifications' => $user->notifications()
                    ->latest()
                    ->take(5)
                    ->get(),
                'shellUnreadNotificationCount' => $user->unreadNotifications()->count(),
            ]);
        });
    }
}
