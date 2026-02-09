<?php

namespace App\Http\Controllers;

use App\Http\Resources\ServiceResource;
use App\Models\Service;

class ServiceController extends Controller
{
    /**
     * Display a listing of active services
     */
    public function index()
    {
        $services = Service::active()
            ->orderBy('order')
            ->get();

        return ServiceResource::collection($services);
    }

    /**
     * Display a single service by slug
     */
    public function show(string $slug)
    {
        $service = Service::active()
            ->where('slug', $slug)
            ->firstOrFail();

        return new ServiceResource($service);
    }
}
