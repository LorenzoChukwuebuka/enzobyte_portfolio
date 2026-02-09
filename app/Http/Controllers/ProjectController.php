<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of published projects
     */
    public function index(Request $request)
    {
        $projects = Project::published()
            ->orderBy('order')
            ->orderBy('completion_date', 'desc')
            ->when($request->category, fn($q, $category) => $q->byCategory($category))
            ->when($request->featured, fn($q) => $q->featured())
            ->paginate(12);

        return ProjectResource::collection($projects);
    }

    /**
     * Display a single project by slug
     */
    public function show(string $slug)
    {
        $project = Project::published()
            ->where('slug', $slug)
            ->with('testimonials')
            ->firstOrFail();

        return new ProjectResource($project);
    }

    /**
     * Get featured projects
     */
    public function featured()
    {
        $projects = Project::published()
            ->featured()
            ->orderBy('order')
            ->limit(6)
            ->get();

        return ProjectResource::collection($projects);
    }
}
