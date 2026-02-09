<?php

namespace App\Traits;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasMedia
{
    /**
     * Get all media for the model
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'model')->orderBy('order');
    }

    /**
     * Add media from uploaded file
     */
    public function addMedia(UploadedFile $file, string $collection = 'default', array $metadata = []): Media
    {
        $fileName = $this->generateFileName($file);
        $path = $this->getMediaPath($collection) . '/' . $fileName;

        // Store the file
        Storage::disk('public')->put($path, file_get_contents($file));

        // Create media record
        return $this->media()->create([
            'collection_name' => $collection,
            'file_name' => $file->getClientOriginalName(),
            'disk' => 'public',
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'metadata' => $metadata,
            'order' => $this->getNextMediaOrder($collection),
        ]);
    }

    /**
     * Add media from base64 string
     */
    public function addMediaFromBase64(string $base64, string $fileName, string $collection = 'default', array $metadata = []): Media
    {
        // Decode base64
        $fileData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
        
        // Generate file name
        $extension = pathinfo($fileName, PATHINFO_EXTENSION) ?: 'jpg';
        $generatedName = Str::uuid() . '.' . $extension;
        $path = $this->getMediaPath($collection) . '/' . $generatedName;

        // Store the file
        Storage::disk('public')->put($path, $fileData);

        // Get mime type
        $mimeType = Storage::disk('public')->mimeType($path);

        // Create media record
        return $this->media()->create([
            'collection_name' => $collection,
            'file_name' => $fileName,
            'disk' => 'public',
            'path' => $path,
            'mime_type' => $mimeType,
            'size' => strlen($fileData),
            'metadata' => $metadata,
            'order' => $this->getNextMediaOrder($collection),
        ]);
    }

    /**
     * Get first media from a collection
     */
    public function getFirstMedia(string $collection = 'default'): ?Media
    {
        return $this->media()
            ->where('collection_name', $collection)
            ->orderBy('order')
            ->first();
    }

    /**
     * Get first media URL from a collection
     */
    public function getFirstMediaUrl(string $collection = 'default', string $default = ''): string
    {
        $media = $this->getFirstMedia($collection);
        return $media ? $media->url : $default;
    }

    /**
     * Get all media from a collection
     */
    public function getMedia(string $collection = 'default')
    {
        return $this->media()
            ->where('collection_name', $collection)
            ->orderBy('order')
            ->get();
    }

    /**
     * Get all media URLs from a collection
     */
    public function getMediaUrls(string $collection = 'default'): array
    {
        return $this->getMedia($collection)->pluck('url')->toArray();
    }

    /**
     * Clear all media from a collection
     */
    public function clearMediaCollection(string $collection = 'default'): void
    {
        $mediaItems = $this->getMedia($collection);
        
        foreach ($mediaItems as $media) {
            $media->deleteFile();
            $media->delete();
        }
    }

    /**
     * Delete a specific media item
     */
    public function deleteMedia(int $mediaId): bool
    {
        $media = $this->media()->find($mediaId);
        
        if ($media) {
            $media->deleteFile();
            return $media->delete();
        }
        
        return false;
    }

    /**
     * Generate unique file name
     */
    protected function generateFileName(UploadedFile $file): string
    {
        return Str::uuid() . '.' . $file->getClientOriginalExtension();
    }

    /**
     * Get the storage path for media
     */
    protected function getMediaPath(string $collection): string
    {
        $modelName = Str::plural(Str::snake(class_basename($this)));
        return "{$modelName}/{$collection}";
    }

    /**
     * Get the next order number for a collection
     */
    protected function getNextMediaOrder(string $collection): int
    {
        $maxOrder = $this->media()
            ->where('collection_name', $collection)
            ->max('order');
        
        return ($maxOrder ?? -1) + 1;
    }

    /**
     * Boot the trait
     */
    protected static function bootHasMedia(): void
    {
        // Delete all media when model is deleted
        static::deleting(function ($model) {
            foreach ($model->media as $media) {
                $media->deleteFile();
                $media->delete();
            }
        });
    }
}