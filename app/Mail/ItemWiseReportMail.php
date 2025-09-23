<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ItemWiseReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sales;
    public $total_packs;
    public $total_weight;
    public $total_amount;
    public $settingDate;

    public function __construct(Collection $sales, $total_packs, $total_weight, $total_amount)
    {
        $this->sales = $sales;
        $this->total_packs = $total_packs;
        $this->total_weight = $total_weight;
        $this->total_amount = $total_amount;
        $this->settingDate = Setting::value('value') ?? now()->format('Y-m-d');

        Log::info('ItemWiseReportMail instance created.', [
            'sales_count' => $this->sales->count(),
            'total_packs' => $this->total_packs,
            'total_weight' => $this->total_weight,
            'total_amount' => $this->total_amount,
        ]);
    }

    public function envelope(): Envelope
    {
        Log::info('ItemWiseReportMail envelope prepared.', [
            'subject' => '📦 අයිතමය අනුව වාර්තාව (Item-Wise Report)',
        ]);

        return new Envelope(
            subject: '📦 අයිතමය අනුව වාර්තාව (Item-Wise Report)',
        );
    }

    public function content(): Content
    {
        Log::info('ItemWiseReportMail content prepared.', [
            'view' => 'emails.item_wise_report',
        ]);

        return new Content(
            view: 'emails.item_wise_report',
            with: [
                'sales' => $this->sales,
                'total_packs' => $this->total_packs,
                'total_weight' => $this->total_weight,
                'total_amount' => $this->total_amount,
                'settingDate' => $this->settingDate,
            ],
        );
    }
}
