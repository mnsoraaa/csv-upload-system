<?php

namespace App\Actions;

use App\Models\File;
use App\Jobs\HandleExcelChunkJob;
use App\Jobs\HandleExcelCompletedJob;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Statement;

class ProcessCsvFileAction
{
    /**
     * Process CSV file in chunks for memory efficiency
     */
    public function execute(File $file, int $chunkSize = 1000): void
    {
        try {
            $filePath = Storage::path($file->file_path);

            // initialize csv reader
            $csv = Reader::createFromPath($filePath);
            $csv->setHeaderOffset(0);

            // validate csv headers
            $headers = $csv->getHeader();
            app(ValidateCsvAction::class)->validateHeaders($headers);

            $stmt = new Statement();
            $records = $stmt->process($csv);

            $chunk = [];
            $rowNumber = 0;
            $chunkNumber = 0;

            foreach ($records as $record) {

                try {

                    $validatedData = app(ValidateCsvAction::class)->cleanAndValidateRecord($record);

                    if ($validatedData) {

                        $chunk[] = [
                            'row_number' => ++$rowNumber,
                            'data' => $validatedData
                        ];

                    }

                } catch (\Exception $e) {

                    $rowNumber++;
                    continue;

                }

                if (count($chunk) >= $chunkSize) {

                    // dispatch the job to process the chunk (respective to $chunkSize)
                    HandleExcelChunkJob::dispatch($file->id, $chunk, ++$chunkNumber);

                    $chunk = [];

                }

            }

            // process remaining records in final chunk that is not reach $chunkSize
            if (! empty($chunk)) {

                HandleExcelChunkJob::dispatch($file->id, $chunk, ++$chunkNumber);

            }

            HandleExcelCompletedJob::dispatch($file->id, $chunkNumber);

        } catch (\Exception $e) {

            throw $e;

        }
    }
}
