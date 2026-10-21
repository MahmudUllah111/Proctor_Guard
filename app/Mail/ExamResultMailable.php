<?php

namespace App\Mail;

use App\Models\ExamSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExamResultMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ExamSession $session,
        public array $renderedData
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->renderedData['subject'] ?? "Examination Result: {$this->session->exam->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: $this->renderedData['body_html'] ?? '',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
