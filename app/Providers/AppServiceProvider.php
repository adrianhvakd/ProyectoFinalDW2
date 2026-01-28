<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
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
        View::composer('layouts.private.sidebar', function ($view) {
            if (! Auth::check()) {
                return redirect()->route('login')->send();
            }

            $user = Auth::user();

            $view->with([
                'currentUser' => $user,
            ]);
        });
    }
}
