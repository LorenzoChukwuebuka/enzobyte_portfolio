<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'featured_image' => $this->featured_image,
            'author' => new TeamMemberResource($this->whenLoaded('author')),
            'tags' => $this->tags,
            'category' => $this->category,
            'read_time_minutes' => $this->read_time_minutes,
            'views' => $this->views,
            'is_published' => $this->is_published,
            'published_at' => $this->published_at?->toDateTimeString(),
            // This automatically calls getFeaturedImageUrlAttribute()
            'featured_image_url' => $this->featured_image_url,
            
            // Author info
            'author' => [
                'id' => $this->author?->id,
                'name' => $this->author?->name,
                'position' => $this->author?->position,
                'photo_url' => $this->author?->photo_url,
            ],
        ];
    }
}