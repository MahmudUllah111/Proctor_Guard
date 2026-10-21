<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $exams = Exam::withCount(['questions', 'sessions'])
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('course_code', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('admin.exams.index', compact('exams', 'search'));
    }

    public function create()
    {
        return view('admin.exams.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1|max:600',
            'pass_percentage' => 'required|numeric|min:0|max:100',
            'max_strikes' => 'required|integer|min:1|max:20',
            'access_passcode' => 'nullable|string|max:50',
            'anti_cheat_strictness' => 'required|in:strict,medium,lenient',
            'randomize_questions' => 'boolean',
            'randomize_options' => 'boolean',
            'enforce_fullscreen' => 'boolean',
            'block_clipboard' => 'boolean',
            'block_devtools' => 'boolean',
            'watermark_enabled' => 'boolean',
            'watermark_custom_text' => 'nullable|string|max:255',
            'is_published' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['randomize_questions'] = $request->boolean('randomize_questions');
        $validated['randomize_options'] = $request->boolean('randomize_options');
        $validated['enforce_fullscreen'] = $request->boolean('enforce_fullscreen');
        $validated['block_clipboard'] = $request->boolean('block_clipboard');
        $validated['block_devtools'] = $request->boolean('block_devtools');
        $validated['watermark_enabled'] = $request->boolean('watermark_enabled');
        $validated['is_published'] = $request->boolean('is_published');
        $validated['is_released'] = false;

        $exam = Exam::create($validated);

        return redirect()->route('admin.exams.questions.index', $exam)
            ->with('success', 'Exam created successfully! Now add questions to the exam.');
    }

    public function edit(Exam $exam)
    {
        return view('admin.exams.edit', compact('exam'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1|max:600',
            'pass_percentage' => 'required|numeric|min:0|max:100',
            'max_strikes' => 'required|integer|min:1|max:20',
            'access_passcode' => 'nullable|string|max:50',
            'anti_cheat_strictness' => 'required|in:strict,medium,lenient',
            'randomize_questions' => 'boolean',
            'randomize_options' => 'boolean',
            'enforce_fullscreen' => 'boolean',
            'block_clipboard' => 'boolean',
            'block_devtools' => 'boolean',
            'watermark_enabled' => 'boolean',
            'watermark_custom_text' => 'nullable|string|max:255',
            'is_published' => 'boolean',
        ]);

        $validated['randomize_questions'] = $request->boolean('randomize_questions');
        $validated['randomize_options'] = $request->boolean('randomize_options');
        $validated['enforce_fullscreen'] = $request->boolean('enforce_fullscreen');
        $validated['block_clipboard'] = $request->boolean('block_clipboard');
        $validated['block_devtools'] = $request->boolean('block_devtools');
        $validated['watermark_enabled'] = $request->boolean('watermark_enabled');
        $validated['is_published'] = $request->boolean('is_published');

        $exam->update($validated);

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam updated successfully.');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();

        return redirect()->route('admin.exams.index')
            ->with('success', 'Exam deleted successfully.');
    }

    /**
     * One-click toggle release status for synchronized waiting rooms.
     */
    public function toggleRelease(Exam $exam)
    {
        $newState = !$exam->is_released;

        $exam->update([
            'is_released' => $newState,
            'released_at' => $newState ? now() : null,
        ]);

        $msg = $newState
            ? "Exam questions released! Students in waiting room will begin immediately."
            : "Exam paused/frozen. New submissions paused.";

        return back()->with('success', $msg);
    }

    /**
     * Exam candidate performance report.
     */
    public function report(Exam $exam)
    {
        $sessions = $exam->sessions()
            ->with('infractions')
            ->orderBy('id', 'desc')
            ->paginate(25);

        $totalAttempts = $exam->sessions()->count();
        $completed = $exam->sessions()->where('status', 'completed')->count();
        $terminated = $exam->sessions()->where('status', 'terminated_cheat')->count();
        $passed = $exam->sessions()->where('passed', true)->count();
        $passRate = ($completed + $terminated) > 0 ? round(($passed / ($completed + $terminated)) * 100, 1) : 0;
        $avgScore = $exam->sessions()->where('status', 'completed')->avg('score') ?? 0;

        return view('admin.exams.report', compact('exam', 'sessions', 'totalAttempts', 'completed', 'terminated', 'passed', 'passRate', 'avgScore'));
    }
}
