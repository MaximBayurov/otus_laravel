<?php

namespace App\Mail;

use App\Services\ExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExportResultsMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(protected string $filePath, protected string $modelClass)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Export Results Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $model = ExportService::EXPORTABLE_MODELS[$this->modelClass];
        return new Content(
            view: 'mail.export-results',
            with: [
                'model' => $model,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->filePath),
        ];
    }
}
