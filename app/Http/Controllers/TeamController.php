<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeamMemberResource;
use App\Models\TeamMember;

class TeamController extends Controller
{
    /**
     * Display a listing of active team members
     */
    public function index()
    {
        $team = TeamMember::active()
            ->orderBy('order')
            ->get();

        return TeamMemberResource::collection($team);
    }

    /**
     * Display a single team member
     */
    public function show(int $id)
    {
        $member = TeamMember::active()
            ->findOrFail($id);

        return new TeamMemberResource($member);
    }
}
