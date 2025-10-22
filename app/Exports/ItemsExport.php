<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ItemsExport implements FromView
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function view(): View
    {
        // Use a dedicated blade for export that contains the same columns
        return view('dashboard.reports.items_excelpdf', [
            'items' => $this->items
        ]);
    }
}
