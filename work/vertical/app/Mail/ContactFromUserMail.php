<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFromUserMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $from = new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
        $subject = '【' . env('APP_NAME') . '】お問い合わせをお預かりしました';
        return new Envelope(
            from: $from,
            subject: $subject,
        );
    }

}
