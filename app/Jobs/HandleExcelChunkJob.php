<?php

namespace App\Jobs;

use App\Models\File;
use App\Models\Product;
use App\Events\FileProgress;
use App\Events\FileCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable as BusQueueable;

class HandleExcelChunkJob implements ShouldQueue
{
    use BusQueueable, Queueable, InteractsWithQueue, SerializesModels;

    public int $fileId;
    public array $chunk;
    public int $chunkNumber;
    public int $timeout = 300; // 5 minutes
    public int $tries = 2;

    /**
     * Create a new job instance.
     */
    public function __construct(int $fileId, array $chunk, int $chunkNumber)
    {
        $this->fileId = $fileId;
        $this->chunk = $chunk;
        $this->chunkNumber = $chunkNumber;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {

            $file = File::findOrFail($this->fileId);

            FileProgress::dispatch($file->id, 'processing', "Processing chunk {$this->chunkNumber}");

            $processedRecords = array_column($this->chunk, 'data');

            if (! empty($processedRecords)) {

                $this->performBulkUpsert($processedRecords);

            }

        } catch (\Exception $e) {

            throw $e;

        }
    }

    /**
     * Fast bulk upsert using raw SQL
     */
    private function performBulkUpsert(array $records): void
    {
        try {

            DB::table('products')->upsert(
                $records,
                ['unique_key'],
                [
                    'product_title',
                    'product_description',
                    'style_number',
                    'sanmar_mainframe_color',
                    'size',
                    'color_name',
                    'piece_price',
                    'updated_at'
                ]
            );

        } catch (\Exception $e) {

            throw $e;

        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $file = File::find($this->fileId);

        if ($file) {

            $file->update(['status' => 'failed', 'completed_at' => now()]);

            FileCompleted::dispatch($file->id, 'failed', "Chunk {$this->chunkNumber} processing failed");

        }
    }
}
