<?php

namespace App\Services;

use App\Models\File;
use App\Jobs\HandleExcelProcessingJob;

class FileService
{
    /**
     * Handle file upload and dispatch background processing
     */
    public function handle($request)
    {
        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();

        $filePath = $file->store('files', 'local');

        $fileRecord = File::create([
            'filename' => pathinfo($originalFilename, PATHINFO_FILENAME) . '_' . time() . '.csv',
            'original_filename' => $originalFilename,
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'status' => 'pending'
        ]);

        HandleExcelProcessingJob::dispatch($fileRecord->id);

        return $fileRecord;
    }
}
