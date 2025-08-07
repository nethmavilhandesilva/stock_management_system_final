<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DayStartReport extends Mailable
{
    use Queueable, SerializesModels;

    public $reportData;
    public $dayStartDate;

    /**
     * Create a new message instance.
     *
     * @param array $reportData The data for the sales report.
     * @param \Carbon\Carbon $dayStartDate The date the day was started.
     * @return void
     */
    public function __construct($reportData, $dayStartDate)
    {
        $this->reportData = $reportData;
        $this->dayStartDate = $dayStartDate;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('විකුණුම්/බර මත්තෙහි ඉතිරි වාර්තාව - ' . $this->dayStartDate->format('Y-m-d'))
                    ->to('nethmavilha@gmail.com')
                    ->view('emails.day_start_report');
    }
}