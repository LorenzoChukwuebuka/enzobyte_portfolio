<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'position'  => $this->position,
            'bio'       => $this->bio,
            'photo'     => $this->photo,
            'email'     => $this->email,
            'linkedin'  => $this->linkedin,
            'twitter'   => $this->twitter,
            'github'    => $this->github,
            'skills'    => $this->skills,
            'is_active' => $this->is_active,
            'order'     => $this->order,
            'photo_url' => $this->photo_url,
        ];
    }
}