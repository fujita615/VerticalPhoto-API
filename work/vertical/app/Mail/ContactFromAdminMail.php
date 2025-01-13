<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;


class ContactFromAdminMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $from = new Address($this->from_data['email'], $this->from_data['name']);
        $subject = '【' . env('APP_NAME') . '】お問い合わせがありました';
        return new Envelope(
            from: $from,
            subject: $subject,
        );
    }


}
