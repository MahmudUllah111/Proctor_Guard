<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::orderBy('name')->get();

        return view('admin.templates.index', compact('templates'));
    }

    public function edit(EmailTemplate $template)
    {
        return view('admin.templates.edit', compact('template'));
    }

    public function update(Request $request, EmailTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $template->update($validated);

        return redirect()->route('admin.templates.index')
            ->with('success', "Email template '{$template->name}' updated successfully.");
    }

    public function preview(EmailTemplate $template)
    {
        $sampleData = [
            'student_name' => 'Alexander Vance',
            'student_id' => 'STU-2026-9042',
            'student_email' => 'a.vance@student.university.edu',
            'exam_title' => 'CSC 455 Advanced Web Development Final Exam',
            'course_code' => 'CSC 455',
            'score' => '18.0',
            'max_score' => '20.0',
            'percentage' => '90.0',
            'pass_percentage' => '70.0',
            'status_text' => 'PASSED',
            'status_badge' => '<span style="display:inline-block;padding:6px 16px;background:#10b981;color:#ffffff;border-radius:9999px;font-weight:bold;font-size:14px;">PASSED</span>',
            'submission_date' => now()->toDayDateTimeString(),
            'ip_address' => '192.168.1.105',
            'strikes_count' => '0',
            'max_strikes' => '3',
            'infraction_type' => 'DevTools Detected',
            'severity' => 'CRITICAL',
            'strike_number' => '3',
            'status' => 'TERMINATED_CHEAT',
            'details' => 'Developer tools opened while viewing question #14.',
            'timestamp' => now()->toDayDateTimeString(),
        ];

        $rendered = $template->render($sampleData);

        return view('admin.templates.preview', compact('template', 'rendered'));
    }
}
