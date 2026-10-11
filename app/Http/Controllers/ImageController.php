<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * Display an image from storage
     */
    public function show($path)
    {
        // Remove any URL encoding issues
        $path = urldecode($path);

        // Security: prevent directory traversal
        if (strpos($path, '..') !== false || strpos($path, '\\') !== false) {
            abort(403);
        }

        // Check if file exists in public disk
        if (Storage::disk('public')->exists($path)) {
            $file = Storage::disk('public')->get($path);
            $mimeType = Storage::disk('public')->mimeType($path);

            return response($file, 200)->header('Content-Type', $mimeType);
        }

        abort(404);
    }
}
