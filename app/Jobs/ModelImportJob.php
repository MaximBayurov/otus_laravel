<?php

namespace App\Jobs;

use App\Mail\ImportResultsMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ModelImportJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const DISK_NAME = "import";

    protected array $stats = [
        'created' => 0,
        'failed' => 0,
    ];

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $model,
        protected string $filePath,
        protected array $fields,
        protected ?string $email = null,
        protected bool $withHeaders = false,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $storage = Storage::disk(self::DISK_NAME);
        if ($storage->missing($this->filePath)) {
            return;
        }

        $this->import($storage);

        $this->sendEmail();
        \Log::info('Результаты импорта', [
            'failed' => $this->stats['failed'],
            'created' => $this->stats['created'],
        ]);
        Cache::tags([$this->model])->flush();
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return $this->model;
    }

    private function import(Filesystem|FilesystemAdapter $storage)
    {
        if (!is_a($this->model, Model::class, true)) {
            return;
        }

        $handle = fopen($storage->path($this->filePath), 'r');

        if ($this->withHeaders) {
            fgetcsv($handle);
        }

        while ($data = fgetcsv($handle)) {
            if (empty(array_filter($data))) {
                continue;
            }
            $attributes = [];
            foreach ($this->fields as $index => $attribute) {
                $attributes[$attribute] = $data[$index];
            }

            try {
                $this->model::create($attributes);
                $this->stats['created'] += 1;
            } catch (\Throwable $e) {
                $this->stats['failed'] += 1;
            }
        }

        fclose($handle);
    }

    private function sendEmail()
    {
        if (empty($this->email)) {
            return;
        }

        Mail::to($this->email)->send(
            new ImportResultsMail($this->model, $this->stats)
        );
    }
}
