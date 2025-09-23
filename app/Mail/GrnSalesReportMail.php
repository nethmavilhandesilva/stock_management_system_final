<?php
// In app/Mail/GrnSalesReportMail.php
// In app/Mail/GrnSalesReportMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GrnSalesReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sales;
    public $total_packs;
    public $total_weight;
    public $total_amount;

    public function __construct($sales, $total_packs, $total_weight, $total_amount)
    {
        $this->sales = $sales;
        $this->total_packs = $total_packs;
        $this->total_weight = $total_weight;
        $this->total_amount = $total_amount;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'GRN Sales Report',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.grn-sales-report',
            with: [
                'sales' => $this->sales,
                'total_packs' => $this->total_packs,
                'total_weight' => $this->total_weight,
                'total_amount' => $this->total_amount,
            ],
        );
    }
}