<?php
namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes, HasMedia;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'full_description',
        'client_name',
        'project_url',
        // 'thumbnail',
        // 'gallery',
        'technologies',
        'category',
        'completion_date',
        'duration_days',
        'featured',
        'is_published',
        'order',
    ];

    protected $casts = [
        // 'gallery'         => 'array',
        'technologies'    => 'array',
        'completion_date' => 'date',
        'featured'        => 'boolean',
        'is_published'    => 'boolean',
    ];

    protected $appends = ['thumbnail_url', 'gallery_urls'];

    // Relationships
    public function testimonials()
    {
        return $this->hasMany(Testimonial::class);
    }

    // Accessors for media URLs
    public function getThumbnailUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('thumbnail', $this->thumbnail ?? '');
    }

    public function getGalleryUrlsAttribute(): array
    {
        return $this->getMediaUrls('gallery');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->with('media')->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->with('media')->where('featured', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}