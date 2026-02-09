<?php
namespace App\Http\Controllers;

use App\Models\ContactInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Store a new contact inquiry
     */
    public function store(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'name'           => 'required|string|max:255',
                'email'          => 'required|email|max:255',
                'phone'          => 'nullable|string|max:20',
                'company'        => 'nullable|string|max:255',
                'service_needed' => 'nullable|in:web_development,mobile_app,ui_ux_design,branding,consulting,maintenance,other',
                'message'        => 'required|string|min:10',
                'budget_range'   => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $inquiry = ContactInquiry::create([
                'name'           => $request->name,
                'email'          => $request->email,
                'phone'          => $request->phone,
                'company'        => $request->company,
                'service_needed' => $request->service_needed,
                'message'        => $request->message,
                'budget_range'   => $request->budget_range ?? 'not_specified',
                'ip_address'     => $request->ip(),
                'user_agent'     => $request->userAgent(),
            ]);

            // You can send notification email here if needed
            // Mail::to('hello@enzobyte.tech')->send(new ContactInquiryReceived($inquiry));

            return response()->json([
                'success' => true,
                'message' => 'Thank you for contacting us! We will get back to you soon.',
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }

    }
}