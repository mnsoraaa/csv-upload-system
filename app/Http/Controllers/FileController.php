<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileUploadRequest;
use App\Models\File;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FileController extends Controller
{
    /**
     * Display the file upload form
     */
    public function index(): View
    {
        $files = File::latest()->take(20)->get();
        return view('index', compact('files'));
    }

    /**
     * Handle file upload
     */
    public function store(FileUploadRequest $request, FileService $fileService): JsonResponse
    {
        try {
            $fileRecord = $fileService->handle($request);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully and processing will start shortly.',
                'file' => $fileRecord
            ]);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'Upload failed: ' . $e->getMessage()], 500);

        }
    }

    /**
     * Get file status
     */
    public function status(int $fileId): JsonResponse
    {
        try {
            $file = File::findOrFail($fileId);

            return response()->json([
                'success' => true,
                'file' => $file
            ]);

        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'File not found'], 404);

        }
    }

    /**
     * Get all files
     */
    public function list(): JsonResponse
    {
        try {
            $files = File::latest()->paginate(1);

            return response()->json(['success' => true, 'files' => $files]);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'Failed to retrieve files'], 500);

        }
    }

    /**
     * Delete file and associated data
     */
    public function destroy(int $fileId): JsonResponse
    {
        try {
            $file = File::findOrFail($fileId);

            Storage::delete($file->file_path);

            $file->delete();

            return response()->json(['success' => true, 'message' => 'File deleted successfully']);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'Failed to delete file'], 500);

        }
    }
}
