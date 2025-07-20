<?php

namespace App\Jobs;

use App\Events\FileCompleted;
use App\Models\File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class HandleExcelCompletedJob implements ShouldQueue
{
    use Queueable;

    public int $fileId;
    public int $chunkNumber;

    /**
     * Create a new job instance.
     */
    public function __construct(int $fileId, int $chunkNumber)
    {
        $this->fileId = $fileId;
        $this->chunkNumber = $chunkNumber;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $file = File::findOrFail($this->fileId);

        $file->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        FileCompleted::dispatch($this->fileId, 'completed', "File processing completed successfully", $this->chunkNumber);
    }
}
