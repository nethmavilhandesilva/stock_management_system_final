<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customerName;
    public $receiptHtml;

    /**
     * Create a new message instance.
     */
    public function __construct($customerName, $receiptHtml)
    {
        $this->customerName = $customerName;
        $this->receiptHtml = $receiptHtml;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Receipt for ' . $this->customerName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            // Use the HTML passed from the frontend directly.
            html: $this->receiptHtml, 
        );
    }
}