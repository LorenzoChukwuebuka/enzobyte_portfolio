<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'client_name',
        'client_position',
        'client_company',
        'testimonial',
        'rating',
        'client_photo',
        'project_id',
        'is_featured',
        'is_published',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    protected $appends = ['client_photo_url'];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Accessor for client photo URL
    public function getClientPhotoUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('client_photo', $this->client_photo ?? '');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}