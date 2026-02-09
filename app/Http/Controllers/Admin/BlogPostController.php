<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogPostResource;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    /**
     * Display all blog posts (including unpublished)
     */
    public function index()
    {
        $posts = BlogPost::with(['author', 'media'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

      

        return BlogPostResource::collection($posts);
    }

    /**
     * Store a new blog post
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'               => 'required|string|max:255',
            'slug'                => 'nullable|string|unique:blog_posts,slug',
            'excerpt'             => 'required|string',
            'content'             => 'required|string',
            'featured_image'      => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'featured_image_path' => 'nullable|string', // If already uploaded
            'author_id'           => 'nullable|exists:team_members,id',
            'tags'                => 'nullable|string',
            'category'            => 'required|in:technology,design,business,tutorials,case_studies,news',
            'read_time_minutes'   => 'nullable|integer',
            'is_published'        => 'string',
            'published_at'        => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data         = $validator->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        unset($data['featured_image'], $data['featured_image_path']);

        if ($data["is_published"] === "true") {
            $data["is_published"] = true;
        } elseif ($data["is_published"] === "false") {
            $data["is_published"] = false;
        }

        if (! empty($data['tags'])) {
            $decoded = json_decode($data['tags'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'message' => 'Invalid tags format',
                ], 422);
            }

            $data['tags'] = $decoded;
        }

        if (! isset($data['published_at']) && ($data['is_published'] ?? false)) {
            $data['published_at'] = now();
        }

        $post = BlogPost::create($data);

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $post->addMedia($request->file('featured_image'), 'featured_image');
        } elseif ($request->has('featured_image_path')) {
            $post->featured_image = $request->featured_image_path;
            $post->save();
        }

        return new BlogPostResource($post->load('media'));
    }

    /**
     * Display a single blog post
     */
    public function show(int $id)
    {
        $post = BlogPost::with(['author', 'media'])->findOrFail($id);
        return new BlogPostResource($post);
    }

    /**
     * Update a blog post
     */
    public function update(Request $request, int $id)
    {
        $post = BlogPost::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title'               => 'string|max:255',
            'slug'                => 'string|unique:blog_posts,slug,' . $id,
            'excerpt'             => 'string',
            'content'             => 'string',
            'featured_image'      => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'featured_image_path' => 'nullable|string',
            'author_id'           => 'nullable|exists:team_members,id',
            'tags'                => 'nullable|string',
            'category'            => 'in:technology,design,business,tutorials,case_studies,news',
            'read_time_minutes'   => 'nullable|integer',
            'is_published'        => 'string',
            'published_at'        => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        unset($data['featured_image'], $data['featured_image_path']);

        if ($data["is_published"] === "true") {
            $data["is_published"] = true;
        } elseif ($data["is_published"] === "false") {
            $data["is_published"] = false;
        }

        if (! empty($data['tags'])) {
            $decoded = json_decode($data['tags'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'message' => 'Invalid tags format',
                ], 422);
            }

            $data['tags'] = $decoded;
        }

        // Set published_at when publishing for the first time
        if (! $post->published_at && ($data['is_published'] ?? false)) {
            $data['published_at'] = now();
        }

        $post->update($data);

        // Handle featured image update
        if ($request->hasFile('featured_image')) {
            $post->clearMediaCollection('featured_image');
            $post->addMedia($request->file('featured_image'), 'featured_image');
        } elseif ($request->has('featured_image_path')) {
            $post->featured_image = $request->featured_image_path;
            $post->save();
        }

        return new BlogPostResource($post->load('media'));
    }

    /**
     * Delete a blog post (soft delete)
     */
    public function destroy(int $id)
    {
        $post = BlogPost::findOrFail($id);
        $post->delete(); // Media will be auto-deleted

        return response()->json(['message' => 'Blog post deleted successfully']);
    }
}