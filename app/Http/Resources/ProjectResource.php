<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'slug'             => $this->slug,
            'description'      => $this->description,
            'full_description' => $this->full_description,
            'client_name'      => $this->client_name,
            'project_url'      => $this->project_url,
            'category'         => $this->category,
            'technologies'     => $this->technologies,
            'completion_date'  => $this->completion_date?->format('Y-m-d'),
            'duration_days'    => $this->duration_days,
            'featured'         => $this->featured,
            'is_published'     => $this->is_published,
            'order'            => $this->order,

            // Media URLs (using accessors)
            'thumbnail_url'    => $this->thumbnail_url,
            'gallery_urls'     => $this->gallery_urls,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];

    }
}