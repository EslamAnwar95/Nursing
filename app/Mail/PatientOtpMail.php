<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PatientOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    /**
     * Create a new message instance.
     */
    public function __construct(int $otp)
    {
        $this->otp = $otp;

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'رمز التحقق من البريد الإلكتروني',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
  
        return new Content(

            view: 'emails.patients.otp', // ✅ استخدم اسم الـ Blade View الصحيح
            with: [
                'otp' => $this->otp // ✅ نمرر الـ OTP للعرض
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
