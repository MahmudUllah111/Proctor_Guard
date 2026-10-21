<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Services\ExamGradingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExamPortalController extends Controller
{
    public function __construct(
        protected ExamGradingService $gradingService
    ) {}

    /**
     * Show student entry / login form for a specific exam.
     */
    public function showEntry(Exam $exam)
    {
        if (!$exam->is_published) {
            abort(404, 'This examination is currently not available.');
        }

        return view('student.entry', compact('exam'));
    }

    /**
     * Authenticate student ID and create/resume exam session.
     */
    public function enter(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'student_id_code' => 'required|string|max:50',
            'student_name' => 'required|string|max:100',
            'student_email' => 'required|email|max:100',
            'passcode' => 'nullable|string',
        ]);

        // Validate passcode if exam is protected
        if (!empty($exam->access_passcode) && $request->input('passcode') !== $exam->access_passcode) {
            return back()->withInput()->withErrors(['passcode' => 'Invalid exam access passcode provided.']);
        }

        // Look for existing session or create fresh attempt
        $session = ExamSession::where('exam_id', $exam->id)
            ->where('student_id_code', $validated['student_id_code'])
            ->first();

        if (!$session) {
            // Generate randomized question order if enabled
            $questionIds = $exam->questions()->pluck('id')->toArray();
            if ($exam->randomize_questions) {
                shuffle($questionIds);
            }

            $session = ExamSession::create([
                'exam_id' => $exam->id,
                'student_id_code' => strtoupper(trim($validated['student_id_code'])),
                'student_name' => trim($validated['student_name']),
                'student_email' => strtolower(trim($validated['student_email'])),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => $exam->is_released ? 'in_progress' : 'waiting_room',
                'max_strikes' => $exam->max_strikes,
                'question_order' => $questionIds,
                'started_at' => $exam->is_released ? now() : null,
                'ends_at' => $exam->is_released ? now()->addMinutes($exam->duration_minutes) : null,
                'last_heartbeat_at' => now(),
            ]);
        } else {
            // Update IP and user agent
            $session->update([
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'last_heartbeat_at' => now(),
            ]);
        }

        // If completed or terminated, go directly to result screen
        if ($session->isCompleted()) {
            return redirect()->route('student.result', ['session' => $session->session_token]);
        }

        // If exam is not released yet or session in waiting room
        if (!$exam->is_released || $session->status === 'waiting_room') {
            return redirect()->route('student.waiting-room', ['session' => $session->session_token]);
        }

        // Exam is released and session active
        return redirect()->route('student.exam', ['session' => $session->session_token]);
    }

    /**
     * Synchronized waiting room view awaiting proctor question release.
     */
    public function waitingRoom(string $token)
    {
        $session = ExamSession::where('session_token', $token)->with('exam')->firstOrFail();

        // If already completed or terminated, jump to results
        if ($session->isCompleted()) {
            return redirect()->route('student.result', ['session' => $session->session_token]);
        }

        // If exam has been released, advance session and jump to exam
        if ($session->exam->is_released) {
            if ($session->status === 'waiting_room') {
                $session->update([
                    'status' => 'in_progress',
                    'started_at' => $session->started_at ?? now(),
                    'ends_at' => $session->ends_at ?? now()->addMinutes($session->exam->duration_minutes),
                ]);
            }
            return redirect()->route('student.exam', ['session' => $session->session_token]);
        }

        return view('student.waiting_room', compact('session'));
    }

    /**
     * Anti-cheat proctored examination canvas view.
     */
    public function exam(string $token)
    {
        $session = ExamSession::where('session_token', $token)->with('exam')->firstOrFail();

        // If completed or terminated, redirect
        if ($session->isCompleted()) {
            return redirect()->route('student.result', ['session' => $session->session_token]);
        }

        // If exam got revoked/frozen back into waiting room
        if (!$session->exam->is_released) {
            return redirect()->route('student.waiting-room', ['session' => $session->session_token]);
        }

        // Ensure session has started_at and ends_at
        if (!$session->started_at || !$session->ends_at) {
            $session->update([
                'status' => 'in_progress',
                'started_at' => now(),
                'ends_at' => now()->addMinutes($session->exam->duration_minutes),
            ]);
        }

        // Check if server-side countdown has expired
        if ($session->isExpired()) {
            // Auto-grade session with whatever answers were saved
            $this->gradingService->gradeSession($session, $session->answers ?? []);
            return redirect()->route('student.result', ['session' => $session->session_token])
                ->with('info', 'Time limit expired. Your exam was automatically submitted.');
        }

        return view('student.exam', compact('session'));
    }

    /**
     * Submit completed examination.
     */
    public function submit(Request $request, string $token)
    {
        $session = ExamSession::where('session_token', $token)->with('exam')->firstOrFail();

        if ($session->isCompleted()) {
            return redirect()->route('student.result', ['session' => $session->session_token]);
        }

        $answers = $request->input('answers', []);
        if (is_string($answers)) {
            $answers = json_decode($answers, true) ?? [];
        }

        $this->gradingService->gradeSession($session, $answers);

        return redirect()->route('student.result', ['session' => $session->session_token])
            ->with('success', 'Your examination has been submitted successfully.');
    }

    /**
     * Final examination result and score report.
     */
    public function result(string $token)
    {
        $session = ExamSession::where('session_token', $token)->with(['exam', 'infractions'])->firstOrFail();

        return view('student.result', compact('session'));
    }
}
