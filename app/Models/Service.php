<?php
namespace App\Models;

use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'full_description',
        // 'icon',
        'features',
        'pricing_model',
        'base_price',
        'is_active',
        'order',
    ];

    protected $casts = [
        'features'   => 'array',
        'base_price' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getIconUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('icon', $this->icon ?? '');
    }

}