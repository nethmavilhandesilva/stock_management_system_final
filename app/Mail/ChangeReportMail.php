<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Setting;

class ChangeReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $entries;
    public $settingDate;

    /**
     * Create a new message instance.
     *
     * @param  mixed  $entries
     * @return void
     */
    public function __construct($entries)
    {
        $this->entries = $entries;
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
            subject: 'Daily Changes Report',
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
            view: 'emails.change_report',
            with: [
                'entries' => $this->entries,
                'settingDate' => $this->settingDate,
            ],
        );
    }
}