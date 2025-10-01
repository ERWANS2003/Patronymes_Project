<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlertNotification extends Mailable
{
    use Queueable, SerializesModels;

    public string $title;
    public string $message;
    public array $context;
    public string $level;

    /**
     * Create a new message instance.
     */
    public function __construct(string $title, string $message, array $context = [], string $level = 'info')
    {
        $this->title = $title;
        $this->message = $message;
        $this->context = $context;
        $this->level = $level;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = "[{$this->level}] {$this->title} - " . config('app.name');

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.alert-notification',
            with: [
                'title' => $this->title,
                'message' => $this->message,
                'context' => $this->context,
                'level' => $this->level,
                'timestamp' => now()->format('d/m/Y H:i:s'),
                'app_name' => config('app.name'),
                'app_url' => config('app.url'),
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
