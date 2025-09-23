<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Setting;

class TotalSalesReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sales;
    public $grandTotal;
    public $settingDate;

    public function __construct($sales, $grandTotal)
    {
        $this->sales = $sales;
        $this->grandTotal = $grandTotal;
        $this->settingDate = Setting::value('value');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Total Sales Report',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.total_sales_report',
            with: [
                'sales' => $this->sales,
                'grandTotal' => $this->grandTotal,
                'settingDate' => $this->settingDate,
            ],
        );
    }
}