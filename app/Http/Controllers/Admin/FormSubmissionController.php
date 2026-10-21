<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FormSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');

        $submissions = FormSubmission::when($status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->when($search, function ($q, $search) {
                $q->where('student_or_lead_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'all' => FormSubmission::count(),
            'new' => FormSubmission::where('status', 'new')->count(),
            'reviewed' => FormSubmission::where('status', 'reviewed')->count(),
            'contacted' => FormSubmission::where('status', 'contacted')->count(),
            'closed' => FormSubmission::where('status', 'closed')->count(),
        ];

        return view('admin.forms.index', compact('submissions', 'counts', 'status', 'search'));
    }

    public function show(FormSubmission $submission)
    {
        return view('admin.forms.show', compact('submission'));
    }

    public function updateStatus(Request $request, FormSubmission $submission)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,reviewed,contacted,closed',
            'admin_notes' => 'nullable|string',
        ]);

        $submission->update($validated);

        return back()->with('success', 'Lead status updated.');
    }

    public function exportCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="leads_export_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Form Name', 'Candidate/Lead Name', 'Email', 'Phone', 'Subject', 'Message', 'Status', 'IP Address', 'Created At']);

            FormSubmission::orderBy('id', 'desc')->chunk(100, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->id,
                        $row->form_name,
                        $row->student_or_lead_name,
                        $row->email,
                        $row->phone,
                        $row->subject,
                        $row->message,
                        $row->status,
                        $row->ip_address,
                        $row->created_at->toDateTimeString(),
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy(FormSubmission $submission)
    {
        $submission->delete();

        return redirect()->route('admin.forms.index')
            ->with('success', 'Lead record deleted.');
    }
}
