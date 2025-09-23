<?php
// In app/Mail/GrnSalesOverviewMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GrnSalesOverviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reportData;

    public function __construct($reportData)
    {
        $this->reportData = $reportData;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'ඉතිරි වාර්තාව',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.grn-sales-overview',
            with: ['reportData' => $this->reportData],
        );
    }
}