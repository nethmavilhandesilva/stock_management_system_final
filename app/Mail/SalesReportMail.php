<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SalesReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $salesByBill;
    public $grandTotal;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($salesByBill, $grandTotal)
    {
        $this->salesByBill = $salesByBill;
        $this->grandTotal = $grandTotal;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.sales_report')
                    ->subject('Daily Sales Report');
    }
}