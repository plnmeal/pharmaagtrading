<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Removed: use App\Models\Setting; // Handled by NavigationComposer
use App\Models\Lead;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Display the contact us page.
     */
    public function index(): View
    {
        // Removed: $settings = Setting::firstOrCreate(...); // Handled by NavigationComposer
        return view('contact'); // 'settings' is now available from Composer
    }

    /**
     * Handle contact form submission.
     */
    public function submit(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput()
                             ->with('error', 'Please correct the form errors and try again.');
        }

        try {
            Lead::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'subject' => $request->subject,
                'message' => $request->message,
                'enquiry_type' => 'general',
                'is_read' => false,
            ]);

            return redirect()->back()->with('success', 'Your message has been sent successfully! We will get back to you soon.');

        } catch (\Exception $e) {
            \Log::error('Error submitting contact form: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'There was a problem sending your message. Please try again later.');
        }
    }
}