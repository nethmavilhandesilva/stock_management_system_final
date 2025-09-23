<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Setting;

class BillSummaryReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $salesByBill;
    public $grandTotal;
    public $settingDate;

    /**
     * Create a new message instance.
     *
     * @param  \Illuminate\Support\Collection  $salesByBill
     * @param  float  $grandTotal
     * @return void
     */
    public function __construct($salesByBill, $grandTotal)
    {
        $this->salesByBill = $salesByBill;
        $this->grandTotal = $grandTotal;
        $this->settingDate = Setting::value('value');
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sales Report - Bill Summary',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.bill_summary_report',
            with: [
                'salesByBill' => $this->salesByBill,
                'grandTotal' => $this->grandTotal,
                'settingDate' => $this->settingDate,
            ],
        );
    }
}
