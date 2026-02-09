<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Display all services
     */
    public function index()
    {
        $services = Service::with("media")->orderBy('order')->get();
        return ServiceResource::collection($services);
    }

    /**
     * Store a new service
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'             => 'required|string|max:255',
            'slug'              => 'nullable|string|unique:services,slug',
            'short_description' => 'required|string',
            'full_description'  => 'nullable|string',
            'icon'              => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg|max:5120',
            'features'          => 'nullable|array',
            'pricing_model'     => 'nullable|in:fixed,hourly,project_based',
            'base_price'        => 'nullable|numeric|min:0',
            'is_active'         => 'string',
            
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data         = $validator->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        $data = $validator->validated();

        switch ($data["is_active"]) {
            case 'false':
                $data["is_active"] = false;
                break;
            case 'true':
                $data["is_active"] = true;
                break;
            default:
                $data["is_active"] = true;
                break;
        }
        // Remove icon from data before creating the service
        unset($data['icon']);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $service = Service::create($data);

        if ($request->hasFile('icon')) {
            $service->addMedia($request->file('icon'), 'icon');
        } elseif ($request->has('icon_path')) {
            $service->icon = $request->icon_path;
            $service->save();
        }

        return new ServiceResource($service);
    }

    /**
     * Display a single service
     */
    public function show(int $id)
    {
        $service = Service::findOrFail($id);
        return new ServiceResource($service);
    }

    /**
     * Update a service
     */
    public function update(Request $request, int $id)
    {
        $service = Service::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title'             => 'string|max:255',
            'slug'              => 'string|unique:services,slug,' . $id,
            'short_description' => 'string',
            'full_description'  => 'nullable|string',
            'icon'              => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg|max:5120',
            'features'          => 'nullable|array',
            'pricing_model'     => 'nullable|in:fixed,hourly,project_based',
            'base_price'        => 'nullable|numeric|min:0',
            'is_active'         => 'string',
            'order'             => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        switch ($data["is_active"]) {
            case 'false':
                false;
                break;
            case 'true':
                true;
                break;

            default:
                # code...
                break;
        }

        return $data["is_active"];

        $service->update($validator->validated());
        if ($request->hasFile('icon')) {
            $service->addMedia($request->file('icon'), 'icon');
        } elseif ($request->has('icon_path')) {
            $service->icon = $request->icon_path;
            $service->save();
        }

        return new ServiceResource($service);
    }

    /**
     * Delete a service
     */
    public function destroy(int $id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return response()->json(['message' => 'Service deleted successfully']);
    }
}