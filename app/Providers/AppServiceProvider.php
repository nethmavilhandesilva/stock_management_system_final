<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; 

use Illuminate\Support\Facades\View;
use App\Models\GrnEntry;// ✅ ADD THIS LINE

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
    // ✅ Set default string length for older MySQL versions
    Schema::defaultStringLength(191);

    // ✅ Automatically share 'entries' with specific modal views
    View::composer([
        'layouts.partials.report-modal',
        'layouts.partials.item-wisemodal',
        'layouts.partials.weight-modal.blade',
        'layouts.partials.salecode-modal'
      
    ], function ($view) {
        $view->with('entries', GrnEntry::all());
    });
}
}
