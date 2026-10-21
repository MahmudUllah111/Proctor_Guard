<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\Exam;
use App\Models\FormSubmission;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display designated homepage.
     */
    public function home()
    {
        $page = CmsPage::where('is_home', true)->where('status', 'published')->first();

        if (!$page) {
            $page = CmsPage::where('slug', 'home')->where('status', 'published')->first();
        }

        $activeExams = Exam::where('is_published', true)->orderBy('is_released', 'desc')->get();

        return view('public.page', [
            'page' => $page,
            'activeExams' => $activeExams,
            'isHomePage' => true,
        ]);
    }

    /**
     * Display a published CMS page by slug.
     */
    public function show(string $slug)
    {
        $page = CmsPage::where('slug', $slug)->where('status', 'published')->firstOrFail();
        $activeExams = Exam::where('is_published', true)->get();

        return view('public.page', [
            'page' => $page,
            'activeExams' => $activeExams,
            'isHomePage' => false,
        ]);
    }

    /**
     * Handle lead/contact inquiry submissions from widget forms.
     */
    public function submitLead(Request $request)
    {
        $validated = $request->validate([
            'form_name' => 'nullable|string|max:100',
            'student_or_lead_name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|max:2000',
        ]);

        $submission = FormSubmission::create([
            'form_name' => $validated['form_name'] ?? 'Inquiry Form',
            'student_or_lead_name' => $validated['student_or_lead_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'] ?? 'Website Inquiry',
            'message' => $validated['message'],
            'payload' => $request->except(['_token', 'form_name', 'student_or_lead_name', 'email', 'phone', 'subject', 'message']),
            'ip_address' => $request->ip(),
            'status' => 'new',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your inquiry has been submitted successfully. Our proctoring team will review it shortly.',
            ]);
        }

        return back()->with('success', 'Your inquiry has been submitted successfully. Our proctoring team will review it shortly.');
    }
}
