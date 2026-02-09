<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'full_description' => $this->full_description,
            'icon' => $this->icon,
            'features' => $this->features,
            'pricing_model' => $this->pricing_model,
            'base_price' => $this->base_price,
            'is_active' => $this->is_active,
            'order' => $this->order,
            "icon_url" => $this->icon_url
        ];
    }
}