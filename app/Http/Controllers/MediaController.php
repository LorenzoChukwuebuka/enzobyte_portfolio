<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
{
    /**
     * Upload a single file
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file'       => 'required|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:10240', // 10MB max
            'collection' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file       = $request->file('file');
        $collection = $request->input('collection', 'default');

        // Generate unique filename
        $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path     = "uploads/{$collection}/" . $fileName;

        // Store file
        Storage::disk('public')->put($path, file_get_contents($file));

        return response()->json([
            'success'   => true,
            'path'      => $path,
            'url'       => Storage::disk('public')->url($path),
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size'      => $file->getSize(),
        ]);
    }

    /**
     * Upload multiple files
     */
    public function uploadMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files'      => 'required|array',
            'files.*'    => 'file|mimes:jpg,jpeg,png,gif,webp,pdf|max:10240',
            'collection' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $collection    = $request->input('collection', 'default');
        $uploadedFiles = [];

        foreach ($request->file('files') as $file) {
            $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path     = "uploads/{$collection}/" . $fileName;

            Storage::disk('public')->put($path, file_get_contents($file));

            $uploadedFiles[] = [
                'path'      => $path,
                'url'       => Storage::disk('public')->url($path),
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size'      => $file->getSize(),
            ];
        }

        return response()->json([
            'success' => true,
            'files'   => $uploadedFiles,
        ]);
    }

    /**
     * Upload from base64
     */
    public function uploadBase64(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'      => 'required|string',
            'file_name'  => 'required|string',
            'collection' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $collection  = $request->input('collection', 'default');
        $base64Image = $request->input('image');
        $fileName    = $request->input('file_name');

        // Decode base64
        $fileData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));

        // Generate unique filename
        $extension  = pathinfo($fileName, PATHINFO_EXTENSION) ?: 'jpg';
        $uniqueName = uniqid() . '_' . time() . '.' . $extension;
        $path       = "uploads/{$collection}/" . $uniqueName;

        // Store file
        Storage::disk('public')->put($path, $fileData);

        return response()->json([
            'success'   => true,
            'path'      => $path,
            'url'       => Storage::disk('public')->url($path),
            'file_name' => $fileName,
            'size'      => strlen($fileData),
        ]);
    }

    /**
     * Delete a file
     */
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'path' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $path = $request->input('path');

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'File not found',
        ], 404);
    }
}