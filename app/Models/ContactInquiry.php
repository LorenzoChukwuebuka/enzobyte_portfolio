<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'service_needed',
        'message',
        'budget_range',
        'status',
        'internal_notes',
        'contacted_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'contacted_at' => 'datetime',
    ];

    // Scopes
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}