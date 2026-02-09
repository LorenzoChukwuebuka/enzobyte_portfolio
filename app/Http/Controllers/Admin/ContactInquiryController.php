<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactInquiryResource;
use App\Models\ContactInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactInquiryController extends Controller
{
    /**
     * Display all contact inquiries
     */
    public function index(Request $request)
    {
        $inquiries = ContactInquiry::query()
            ->when($request->status, fn($q, $status) => $q->byStatus($status))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return ContactInquiryResource::collection($inquiries);
    }

    /**
     * Display a single inquiry
     */
    public function show(int $id)
    {
        $inquiry = ContactInquiry::findOrFail($id);
        return new ContactInquiryResource($inquiry);
    }

    /**
     * Update inquiry status and notes
     */
    public function update(Request $request, int $id)
    {
        $inquiry = ContactInquiry::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'in:new,in_progress,contacted,qualified,converted,closed',
            'internal_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Set contacted_at when status changes to contacted
        if (isset($data['status']) && $data['status'] === 'contacted' && !$inquiry->contacted_at) {
            $data['contacted_at'] = now();
        }

        $inquiry->update($data);

        return new ContactInquiryResource($inquiry);
    }

    /**
     * Delete an inquiry
     */
    public function destroy(int $id)
    {
        $inquiry = ContactInquiry::findOrFail($id);
        $inquiry->delete();

        return response()->json(['message' => 'Inquiry deleted successfully']);
    }

    /**
     * Get inquiry statistics
     */
    public function stats()
    {
        return response()->json([
            'total' => ContactInquiry::count(),
            'new' => ContactInquiry::where('status', 'new')->count(),
            'in_progress' => ContactInquiry::where('status', 'in_progress')->count(),
            'contacted' => ContactInquiry::where('status', 'contacted')->count(),
            'converted' => ContactInquiry::where('status', 'converted')->count(),
            'this_month' => ContactInquiry::whereMonth('created_at', now()->month)->count(),
        ]);
    }
}
