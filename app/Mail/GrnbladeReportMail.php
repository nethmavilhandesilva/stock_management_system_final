<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GrnbladeReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $groupedData;

    /**
     * Create a new message instance.
     */
    public function __construct($groupedData)
    {
        $this->groupedData = $groupedData;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('GRN Report')
                    ->view('emails.grn_report'); // <-- create this view
    }
}
