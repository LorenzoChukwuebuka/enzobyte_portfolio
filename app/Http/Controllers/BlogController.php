<?php

namespace App\Http\Controllers;

use App\Http\Resources\BlogPostResource;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of published blog posts
     */
    public function index(Request $request)
    {
        $posts = BlogPost::published()
            ->with('author')
            ->when($request->category, fn($q, $category) => $q->byCategory($category))
            ->when($request->tag, function($q, $tag) {
                $q->whereJsonContains('tags', $tag);
            })
            ->orderBy('published_at', 'desc')
            ->paginate(10);

        return BlogPostResource::collection($posts);
    }

    /**
     * Display a single blog post by slug
     */
    public function show(string $slug)
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->with('author')
            ->firstOrFail();

        // Increment view count
        $post->increment('views');

        return new BlogPostResource($post);
    }

    /**
     * Get recent posts
     */
    public function recent(int $limit = 5)
    {
        $posts = BlogPost::published()
            ->with('author')
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();

        return BlogPostResource::collection($posts);
    }
}
