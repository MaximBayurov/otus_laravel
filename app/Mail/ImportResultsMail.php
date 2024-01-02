<?php

namespace App\Mail;

use App\Services\ImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ImportResultsMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected string $modelClass,
        protected array $stats,
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Import Results Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $model = ImportService::IMPORTABLE_MODELS[$this->modelClass];
        return new Content(
            view: 'mail.import-results',
            with: [
                'model' => $model,
                'stats' => $this->getStatsFormatted()
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
        return [];
    }

    private function getStatsFormatted(): array
    {
        $result = [];
        foreach ($this->stats as $key => $stat) {
            $result[] = [
                'title' => __("mail.import_".$key),
                'count' => $stat,
            ];
        }
        return $result;
    }
}
