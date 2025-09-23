<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CreditReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $loans;
    public $receivedTotal;
    public $paidTotal;
    public $netBalance;
    public $settingDate;

    /**
     * Create a new message instance.
     *
     * @param  \Illuminate\Support\Collection  $loans
     * @param  float  $receivedTotal
     * @param  float  $paidTotal
     * @param  float  $netBalance
     * @return void
     */
    public function __construct(Collection $loans, $receivedTotal, $paidTotal, $netBalance)
    {
        $this->loans = $loans;
        $this->receivedTotal = $receivedTotal;
        $this->paidTotal = $paidTotal;
        $this->netBalance = $netBalance;
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
            subject: 'ණය වාර්තාව (Credit Report) - TGK Traders',
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
            view: 'emails.credit_report',
            with: [
                'loans' => $this->loans,
                'receivedTotal' => $this->receivedTotal,
                'paidTotal' => $this->paidTotal,
                'netBalance' => $this->netBalance,
                'settingDate' => $this->settingDate,
            ],
        );
    }
}
