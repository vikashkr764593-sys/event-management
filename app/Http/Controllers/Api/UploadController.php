<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadFileRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Exception;

class UploadController extends Controller
{
    /**
     * Handle file upload.
     *
     * @param UploadFileRequest $request
     * @return JsonResponse
     */
    public function upload(UploadFileRequest $request): JsonResponse
    {
        try {
            $path = $request->file('file')->store('uploads', 'public');
            return response()->json([
                'status' => true, 
                'url'    => url(Storage::url($path)), 
                'path'   => $path
            ], 201);
        } catch (Exception $e) { 
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500); 
        }
    }
    
    /**
     * Delete an uploaded file.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function destroy(\Illuminate\Http\Request $request): JsonResponse
    {
        try {
            $request->validate(['path' => 'required|string']);

            if (Storage::disk('public')->exists($request->path)) {
                Storage::disk('public')->delete($request->path);
                return response()->json([
                    'status'  => true, 
                    'message' => 'File deleted successfully.'
                ]);
            }

            return response()->json([
                'status'  => false, 
                'message' => 'File not found.'
            ], 404);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}