<?php

namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'name',
        'position',
        'bio',
        'photo',
        'email',
        'linkedin',
        'twitter',
        'github',
        'skills',
        'is_active',
        'order',
    ];

    protected $casts = [
        'skills' => 'array',
        'is_active' => 'boolean',
    ];

    protected $appends = ['photo_url'];

    // Relationships
    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class, 'author_id');
    }

    // Accessor for photo URL
    public function getPhotoUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('photo', $this->photo ?? '');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}