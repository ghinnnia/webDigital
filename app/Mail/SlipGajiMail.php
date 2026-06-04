<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SlipGajiMail extends Mailable
{
    use Queueable, SerializesModels;

    public $detail;
    public $period;
    public $pdfContent;

    /**
     * Create a new message instance.
     */
    public function __construct($detail, $period, $pdfContent)
    {
        $this->detail = $detail;
        $this->period = $period;
        $this->pdfContent = $pdfContent;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Slip Gaji ' . $this->period->nama_periode)
                    ->view('emails.slip_gaji')
                    ->attachData($this->pdfContent, 'Slip_Gaji_' . ($this->detail->user->name ?? 'Karyawan') . '_' . $this->period->nama_periode . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}