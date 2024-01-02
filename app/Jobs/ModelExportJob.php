<?php

namespace App\Jobs;

use App\Mail\ExportResultsMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Storage;

class ModelExportJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const DISK_NAME = "export";

    protected string $filePath;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $model,
        protected ?string $email = null,
        protected bool $redo = false,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $storage = Storage::disk(self::DISK_NAME);
        if ($storage->missing($this->getFilePath()) || $this->redo) {
            $this->makeCsvFile($storage);
        }

        if ($storage->exists($this->getFilePath())) {
            $this->sendEmail($storage->path($this->getFilePath()));
        }
    }

    private function getFilePath(): string
    {
        if (empty($this->filePath)) {
            $modelPieces = explode('\\', $this->model);
            $modelName = end($modelPieces);

            $storage = Storage::disk(self::DISK_NAME);
            if ($storage->missing($modelName)) {
                $storage->makeDirectory($modelName);
            }
            $this->filePath = sprintf(
                "%s/%s.csv",
                $modelName,
                now()->format('Y-m-d')
            );
        }

        return $this->filePath;
    }

    private function makeCsvFile($storage): void
    {
        if (!is_a($this->model, Model::class, true)) {
            return;
        }

        $models = $this->model::all();
        if ($models->count() === 0) {
            return;
        }

        $handle = fopen($storage->path($this->getFilePath()), 'w');
        fputcsv($handle, array_keys($models->get(0)->getAttributes()));

        foreach ($models as $model) {
            fputcsv($handle, array_values($model->toArray()));
        }
        fclose($handle);
    }

    private function sendEmail(string $fullPath): void
    {
        if (empty($this->email)) {
            return;
        }

        Mail::to($this->email)->send(
            new ExportResultsMail($fullPath, $this->model)
        );
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return md5(serialize([
            $this->email,
            $this->model
        ]));
    }
}
