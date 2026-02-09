<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestimonialResource;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestimonialController extends Controller
{
    /**
     * Display all testimonials
     */
    public function index()
    {
        $testimonials = Testimonial::with(['project', 'media'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return TestimonialResource::collection($testimonials);
    }

    /**
     * Store a new testimonial
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_name'       => 'required|string|max:255',
            'client_position'   => 'nullable|string|max:255',
            'client_company'    => 'nullable|string|max:255',
            'testimonial'       => 'required|string',
            'rating'            => 'integer|min:1|max:5',
            'client_photo'      => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'client_photo_path' => 'nullable|string', // If already uploaded
            'project_id'        => 'nullable|exists:projects,id',
            'is_featured'       => 'boolean',
            'is_published'      => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        unset($data['client_photo'], $data['client_photo_path']);

        $testimonial = Testimonial::create($data);

        // Handle client photo upload
        if ($request->hasFile('client_photo')) {
            $testimonial->addMedia($request->file('client_photo'), 'client_photo');
        } elseif ($request->has('client_photo_path')) {
            $testimonial->client_photo = $request->client_photo_path;
            $testimonial->save();
        }

        return new TestimonialResource($testimonial->load('media'));
    }

    /**
     * Display a single testimonial
     */
    public function show(int $id)
    {
        $testimonial = Testimonial::with(['project', 'media'])->findOrFail($id);
        return new TestimonialResource($testimonial);
    }

    /**
     * Update a testimonial
     */
    public function update(Request $request, int $id)
    {
        $testimonial = Testimonial::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'client_name'       => 'string|max:255',
            'client_position'   => 'nullable|string|max:255',
            'client_company'    => 'nullable|string|max:255',
            'testimonial'       => 'string',
            'rating'            => 'integer|min:1|max:5',
            'client_photo'      => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'client_photo_path' => 'nullable|string',
            'project_id'        => 'nullable|exists:projects,id',
            'is_featured'       => 'boolean',
            'is_published'      => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        unset($data['client_photo'], $data['client_photo_path']);

        $testimonial->update($data);

        // Handle client photo update
        if ($request->hasFile('client_photo')) {
            $testimonial->clearMediaCollection('client_photo');
            $testimonial->addMedia($request->file('client_photo'), 'client_photo');
        } elseif ($request->has('client_photo_path')) {
            $testimonial->client_photo = $request->client_photo_path;
            $testimonial->save();
        }

        return new TestimonialResource($testimonial->load('media'));
    }

    /**
     * Delete a testimonial
     */
    public function destroy(int $id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->delete(); // Media will be auto-deleted

        return response()->json(['message' => 'Testimonial deleted successfully']);
    }
}