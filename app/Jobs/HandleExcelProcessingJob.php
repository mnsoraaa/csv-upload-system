<?php

namespace App\Jobs;

use App\Models\File;
use App\Actions\ProcessCsvFileAction;
use App\Events\FileProgress;
use App\Events\FileCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable as BusQueueable;

class HandleExcelProcessingJob implements ShouldQueue
{
    use BusQueueable, Queueable, InteractsWithQueue, SerializesModels;

    public int $fileId;
    public int $timeout = 900; // 15 minutes
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(int $fileId)
    {
        $this->fileId = $fileId;
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        $file = File::findOrFail($this->fileId);

        FileProgress::dispatch($file->id, 'processing', 'File processing started');

        $file->update(['status' => 'processing', 'started_at' => now()]);

        app(ProcessCsvFileAction::class)->execute($file);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $file = File::find($this->fileId);

        if ($file) {

            $file->update(['status' => 'failed', 'completed_at' => now()]);

            FileCompleted::dispatch($file->id, 'failed', 'File failed to process');

        }
    }
}
