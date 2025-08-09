<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CombinedReportsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $dayStartReportData;
    public $grnReportData;
    public $dayStartDate;

    public function __construct($dayStartReportData, $grnReportData, $dayStartDate)
    {
        $this->dayStartReportData = $dayStartReportData;
        $this->grnReportData = $grnReportData;
        $this->dayStartDate = $dayStartDate;
    }

    public function build()
    {
        return $this->subject('ඒකාබද්ධ දින වාර්තාව - ' . $this->dayStartDate->format('Y-m-d'))
                    ->to('nethmavilha@gmail.com')
                    ->view('emails.day_start_report');
    }
}