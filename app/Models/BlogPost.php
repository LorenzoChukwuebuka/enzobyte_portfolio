<?php
namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    use HasFactory, SoftDeletes, HasMedia;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'author_id',
        'tags',
        'category',
        'read_time_minutes',
        'views',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'tags'         => 'array',
        'views'        => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $appends = ['featured_image_url'];

    // Relationships
    public function author()
    {
        return $this->belongsTo(TeamMember::class, 'author_id');
    }

    // Accessor for featured image URL
    public function getFeaturedImageUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('featured_image', $this->featured_image ?? '');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}