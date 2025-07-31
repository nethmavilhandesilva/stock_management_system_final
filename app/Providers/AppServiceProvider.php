<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; 

use Illuminate\Support\Facades\View;
use App\Models\GrnEntry;
use App\Models\Item;
use App\Models\Customer;
use App\Models\Supplier;// ✅ ADD THIS LINE

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
        // Keep your existing composer for GRN entries if those modals still need it.
        View::composer([
            'layouts.partials.report-modal',
            'layouts.partials.weight-modal',
            'layouts.partials.salecode-modal',
            // If 'layouts.partials.item-wisemodal' is your old item report modal
            // and you're replacing it with 'layouts.partials.itemReportModal',
            // you might remove it from here if it no longer needs 'entries'.
            'layouts.partials.item-wisemodal', // Keep if this modal still exists and uses 'entries'
        ], function ($view) {
            $view->with('entries', GrnEntry::all());
        });


        // ✅ NEW: Share filter options specifically with itemReportModal.blade.php
        View::composer('layouts.partials.itemReportModal', function ($view) {
            $view->with('items', Item::all());
            $view->with('customers', Customer::all());
            $view->with('suppliers', Supplier::all());
        });
    }
}
