<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;

class GrnReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sales;
    public $selectedGrnCode;
    public $selectedGrnEntry;
    public $startDate;
    public $endDate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sales, $selectedGrnCode, $selectedGrnEntry, $startDate, $endDate)
    {
        $this->sales = $sales;
        $this->selectedGrnCode = $selectedGrnCode;
        $this->selectedGrnEntry = $selectedGrnEntry;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address('no-reply@yourdomain.com', 'TGK Traders'),
            subject: 'GRN Sales Report for ' . $this->selectedGrnCode,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.grn_sale_code_report', // This is the blade file for the email
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}