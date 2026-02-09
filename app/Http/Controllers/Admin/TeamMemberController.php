<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamMemberResource;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeamMemberController extends Controller
{
    /**
     * Display all team members
     */
    public function index()
    {
        $members = TeamMember::with('media')
            ->orderBy('order')
            ->get();

        // foreach ($members as $member) {
        //                                     // Get photo URL (via accessor) - EASIEST WAY
        //     $photoUrl = $member->photo_url; // Returns URL or empty string

        //                                                    // Or access media directly
        //     $photoMedia = $member->getFirstMedia('photo'); // Returns Media object or null

        //                                 // Access all media
        //     $allMedia = $member->media; // Returns all media for this member
        // }

        return TeamMemberResource::collection($members);
    }

    /**
     * Store a new team member
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'position'   => 'required|string|max:255',
            'bio'        => 'nullable|string',
            'photo'      => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'photo_path' => 'nullable|string', // If already uploaded
            'email'      => 'nullable|email',
            'linkedin'   => 'nullable|url',
            'twitter'    => 'nullable|url',
            'github'     => 'nullable|url',
            'skills'     => 'nullable|array',
            'is_active'  => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        unset($data['photo'], $data['photo_path']);

        $member = TeamMember::create($data);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $member->addMedia($request->file('photo'), 'photo');
        } elseif ($request->has('photo_path')) {
            $member->photo = $request->photo_path;
            $member->save();
        }

        return new TeamMemberResource($member->load('media'));
    }

    /**
     * Display a single team member
     */
    public function show(int $id)
    {
        $member = TeamMember::with('media')->findOrFail($id);
        return new TeamMemberResource($member);
    }

    /**
     * Update a team member
     */
    public function update(Request $request, int $id)
    {
        $member = TeamMember::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'       => 'string|max:255',
            'position'   => 'string|max:255',
            'bio'        => 'nullable|string',
            'photo'      => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'photo_path' => 'nullable|string',
            'email'      => 'nullable|email',
            'linkedin'   => 'nullable|url',
            'twitter'    => 'nullable|url',
            'github'     => 'nullable|url',
            'skills'     => 'nullable|array',
            'is_active'  => 'boolean',
            'order'      => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        unset($data['photo'], $data['photo_path']);

        $member->update($data);

        // Handle photo update
        if ($request->hasFile('photo')) {
            $member->clearMediaCollection('photo');
            $member->addMedia($request->file('photo'), 'photo');
        } elseif ($request->has('photo_path')) {
            $member->photo = $request->photo_path;
            $member->save();
        }

        return new TeamMemberResource($member->load('media'));
    }

    /**
     * Delete a team member
     */
    public function destroy(int $id)
    {
        $member = TeamMember::findOrFail($id);
        $member->delete(); // Media will be auto-deleted

        return response()->json(['message' => 'Team member deleted successfully']);
    }
}