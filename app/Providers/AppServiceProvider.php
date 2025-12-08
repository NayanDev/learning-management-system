<?php

namespace App\Providers;

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
        // Override view easyadmin dengan PREPEND (prioritas tinggi)
        $this->app->afterResolving('view', function ($view) {
            $finder = $view->getFinder();
            $finder->prependNamespace('easyadmin', [
                resource_path('views')
            ]);
        });
    }
}
