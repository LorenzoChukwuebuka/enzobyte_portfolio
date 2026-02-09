<?php

namespace App\Http\Controllers;

use App\Http\Resources\TestimonialResource;
use App\Models\Testimonial;

class TestimonialController extends Controller
{
    /**
     * Display published testimonials
     */
    public function index()
    {
        $testimonials = Testimonial::published()
            ->with('project')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return TestimonialResource::collection($testimonials);
    }

    /**
     * Get featured testimonials
     */
    public function featured()
    {
        $testimonials = Testimonial::published()
            ->featured()
            ->with('project')
            ->limit(6)
            ->get();

        return TestimonialResource::collection($testimonials);
    }
}
