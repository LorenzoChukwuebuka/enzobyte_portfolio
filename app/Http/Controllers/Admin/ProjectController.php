<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Display all projects (including unpublished)
     */
    public function index()
    {
        $projects = Project::with('media')
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // foreach ($projects as $project) {
        //                                              // Get thumbnail URL (via accessor)
        //     $thumbnailUrl = $project->thumbnail_url; // Returns URL or empty string
        //                                              // Get gallery URLs (via accessor)
        //     $galleryUrls = $project->gallery_urls;   // Returns array of URL                                                   // Or access media directly
        //                                              // $thumbnailMedia = $project->getFirstMedia('thumbnail'); // Returns Media object or null
        //                                              // $galleryMedia   = $project->getMedia('gallery');        // Returns collection of Media objects

        //     // Access all media
        //     // $allMedia = $project->media; // Returns all media for this project
        // }

        return ProjectResource::collection($projects);
    }

    /**
     * Store a new project
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|unique:projects,slug',
            'description'      => 'required|string',
            'full_description' => 'nullable|string',
            'client_name'      => 'nullable|string|max:255',
            'project_url'      => 'nullable|url',
            'thumbnail'        => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'gallery'          => 'nullable|array',
            'gallery.*'        => 'file|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'technologies'     => 'nullable|string',
            'category'         => 'required|in:web_development,mobile_app,ui_ux_design,branding,consulting,other',
            'completion_date'  => 'nullable|date',
            'duration_days'    => 'nullable|integer',
            'featured'         => 'nullable|string', // Changed from 'string' to 'nullable|string'
            'is_published'     => 'nullable|string', // Changed from 'string' to 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data         = $validator->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        if (! empty($data['technologies'])) {
            $decoded = json_decode($data['technologies'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'message' => 'Invalid technologies format',
                ], 422);
            }

            $data['technologies'] = $decoded;
        }

        foreach (['featured', 'is_published'] as $field) {
            if (array_key_exists($field, $data)) {
                if ($data[$field] === 'true') {
                    $data[$field] = true;
                } elseif ($data[$field] === 'false') {
                    $data[$field] = false;
                }
            }
        }

        // Remove file fields from data
        unset($data['thumbnail'], $data['gallery']);
        $project = Project::create($data);
        if ($request->hasFile('thumbnail')) {
            $media = $project->addMedia($request->file('thumbnail'), 'thumbnail');
            \Log::info('Thumbnail uploaded:', ['media_id' => $media->id, 'path' => $media->path]);

        } else {
            \Log::warning('No thumbnail file in request');
        }

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $index => $image) {
                $media = $project->addMedia($image, 'gallery');
                \Log::info("Gallery image {$index} uploaded:", ['media_id' => $media->id, 'path' => $media->path]);
            }

        } else {
            \Log::warning('No gallery files in request');
        }

        return new ProjectResource($project->load('media'));
    }
    /**
     * Display a single project
     */
    public function show(int $id)
    {
        $project = Project::with('media')->findOrFail($id);
        return new ProjectResource($project);
    }

    /**
     * Update a project
     */
    public function update(Request $request, int $id)
    {
        $project = Project::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title'            => 'nullable|string|max:255',
            'slug'             => 'nullable|string|unique:projects,slug,' . $id,
            'description'      => 'nullable|string',
            'full_description' => 'nullable|string',
            'client_name'      => 'nullable|string|max:255',
            'project_url'      => 'nullable|url',
            'thumbnail'        => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'gallery'          => 'nullable|array',
            'gallery.*'        => 'file|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'technologies'     => 'nullable|string', // Comes as JSON string from frontend
            'category'         => 'nullable|in:web_development,mobile_app,ui_ux_design,branding,consulting,other',
            'completion_date'  => 'nullable|date',
            'duration_days'    => 'nullable|integer',
            'featured'         => 'nullable|string', // Comes as 'true' or 'false' string
            'is_published'     => 'nullable|string', // Comes as 'true' or 'false' string
            'order'            => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            \Log::error('Update validation failed:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Handle technologies JSON
        if (! empty($data['technologies'])) {
            $decoded = json_decode($data['technologies'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'message' => 'Invalid technologies format',
                ], 422);
            }

            $data['technologies'] = $decoded;
        }

        // Convert 'true'/'false' strings to boolean
        foreach (['featured', 'is_published'] as $field) {
            if (array_key_exists($field, $data)) {
                if ($data[$field] === 'true') {
                    $data[$field] = true;
                } elseif ($data[$field] === 'false') {
                    $data[$field] = false;
                }
            }
        }

        // Remove file fields from data before updating
        unset($data['thumbnail'], $data['gallery']);

        // Update project with non-media fields
        $project->update($data);

        \Log::info('Project updated:', ['id' => $project->id]);

        // Handle thumbnail update
        if ($request->hasFile('thumbnail')) {
            \Log::info('Updating thumbnail...');
            try {
                // Clear old thumbnail
                $project->clearMediaCollection('thumbnail');
                // Add new thumbnail
                $media = $project->addMedia($request->file('thumbnail'), 'thumbnail');
                \Log::info('Thumbnail updated:', ['media_id' => $media->id, 'path' => $media->path]);
            } catch (\Exception $e) {
                \Log::error('Thumbnail update failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            }
        } else {
            \Log::info('No thumbnail file in update request');
        }

        // Handle gallery update
        if ($request->hasFile('gallery')) {
            \Log::info('Updating gallery images...');
            try {
                // Clear old gallery
                $project->clearMediaCollection('gallery');
                // Add new gallery images
                foreach ($request->file('gallery') as $index => $image) {
                    $media = $project->addMedia($image, 'gallery');
                    \Log::info("Gallery image {$index} updated:", ['media_id' => $media->id, 'path' => $media->path]);
                }
            } catch (\Exception $e) {
                \Log::error('Gallery update failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            }
        } else {
            \Log::info('No gallery files in update request');
        }

        return new ProjectResource($project->load('media'));
    }

    /**
     * Delete a project (soft delete)
     */
    public function destroy(int $id)
    {
        $project = Project::findOrFail($id);
        $project->delete(); // Media will be auto-deleted via trait

        return response()->json(['message' => 'Project deleted successfully']);
    }

    /**
     * Delete a specific media item from project
     */
    public function deleteMedia(int $projectId, int $mediaId)
    {
        $project = Project::findOrFail($projectId);

        if ($project->deleteMedia($mediaId)) {
            return response()->json(['message' => 'Media deleted successfully']);
        }

        return response()->json(['message' => 'Media not found'], 404);
    }
}