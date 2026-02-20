<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    /**
     * Download file by filename parameter (URL path)
     */
    public function downloadFile($filename)
    {
        if (!$filename) {
            abort(400, 'Filename is required');
        }

        try {
            // Decode filename if URL-encoded
            $path = urldecode($filename);

            // Remove any path traversal attempts
            $path = str_replace('..', '', $path);
            $path = str_replace('\\', '/', $path);

            // Check if file exists in public disk
            if (!Storage::disk('public')->exists($path)) {
                abort(404, 'File not found: ' . $path);
            }

            // Get file properties
            $fullPath = Storage::disk('public')->path($path);
            $filename = basename($path);

            // Log for debugging
            \Log::info('Download file by path - Path: ' . $path . ', Full: ' . $fullPath . ', Readable: ' . (is_readable($fullPath) ? 'YES' : 'NO'));

            // Check if file is readable
            if (!is_readable($fullPath)) {
                abort(403, 'File is not readable. Check permissions: ' . $fullPath);
            }

            // Get file content and return as response
            $fileContent = file_get_contents($fullPath);
            $mimeType = mime_content_type($fullPath);

            return response($fileContent, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Length', strlen($fileContent));
        } catch (\Exception $e) {
            \Log::error('Download file error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
            abort(500, 'Error downloading file: ' . $e->getMessage());
        }
    }

    /**
     * Download file by query parameter (fallback)
     */
    public function downloadFileQuery(Request $request)
    {
        $path = $request->query('path');

        if (!$path) {
            abort(400, 'Path is required');
        }

        try {
            // Remove any path traversal attempts
            $path = str_replace('..', '', $path);
            $path = str_replace('\\', '/', $path);

            // Check if file exists in public disk
            if (!Storage::disk('public')->exists($path)) {
                abort(404, 'File not found: ' . $path);
            }

            // Get file properties
            $fullPath = Storage::disk('public')->path($path);
            $filename = basename($path);

            // Log for debugging
            \Log::info('Download file by query - Path: ' . $path . ', Full: ' . $fullPath . ', Readable: ' . (is_readable($fullPath) ? 'YES' : 'NO'));

            // Check if file is readable
            if (!is_readable($fullPath)) {
                abort(403, 'File is not readable. Check permissions: ' . $fullPath);
            }

            // Get file content and return as response
            $fileContent = file_get_contents($fullPath);
            $mimeType = mime_content_type($fullPath);

            return response($fileContent, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Length', strlen($fileContent));
        } catch (\Exception $e) {
            \Log::error('Download file query error: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
            abort(500, 'Error downloading file: ' . $e->getMessage());
        }
    }
}
