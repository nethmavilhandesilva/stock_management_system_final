<?php

// In app/Mail/FinancialReportMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FinancialReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reportData;

    public function __construct($data)
    {
        $this->reportData = $data;
    }

    public function build()
    {
        return $this->subject('Daily Financial Report - ' . date('Y-m-d'))
            ->view('emails.financial_report')
            ->with($this->reportData);
    }
}