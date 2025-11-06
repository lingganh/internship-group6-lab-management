<?php

namespace App\Providers;

use App\View\Components\layouts\AdminLayout;
use App\View\Components\layouts\ClientLayout;
use App\View\Components\madals\QuickView;
use App\View\Components\tables\TableEmpty;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        Blade::component('admin-layout', AdminLayout::class);
        Blade::component('client-layout', ClientLayout::class);
        Blade::component('table-empty', TableEmpty::class);
        Blade::component('quick-view', QuickView::class);
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }
    }
}
