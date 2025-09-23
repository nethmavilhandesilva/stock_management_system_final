<?php
// In app/Mail/SupplierSalesReportMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupplierSalesReportMail extends Mailable
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
            subject: 'විකුණුම්/බර මත්තෙහි ඉතිරි වාර්තාව',
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.supplier-sales-report',
            with: ['reportData' => $this->reportData],
        );
    }
}