<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use App\Models\Question;
use App\Services\ExamGradingService;
use App\Services\ProctoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProctoringApiController extends Controller
{
    public function __construct(
        protected ProctoringService $proctoringService,
        protected ExamGradingService $gradingService
    ) {}

    /**
     * Waiting room polling status check.
     */
    public function getWaitingStatus(string $token): JsonResponse
    {
        $session = ExamSession::where('session_token', $token)->with('exam')->first();

        if (!$session) {
            return response()->json(['error' => 'Invalid session'], 404);
        }

        $session->update(['last_heartbeat_at' => now()]);

        $isReleased = (bool) $session->exam->is_released;

        if ($isReleased && $session->status === 'waiting_room') {
            $session->update([
                'status' => 'in_progress',
                'started_at' => $session->started_at ?? now(),
                'ends_at' => $session->ends_at ?? now()->addMinutes($session->exam->duration_minutes),
            ]);
        }

        return response()->json([
            'is_released' => $isReleased,
            'status' => $session->status,
            'exam_url' => route('student.exam', ['session' => $session->session_token]),
        ]);
    }

    /**
     * Provide sanitized question dataset for HTML5 Canvas Pixel Rendering.
     * Prevents browser AI scraping DOM text while keeping correct answer keys hidden.
     */
    public function getCanvasQuestionData(string $token): JsonResponse
    {
        $session = ExamSession::where('session_token', $token)->with('exam')->first();

        if (!$session) {
            return response()->json(['error' => 'Invalid session token'], 404);
        }

        if ($session->isCompleted()) {
            return response()->json(['error' => 'Session already finalized', 'status' => $session->status], 403);
        }

        $exam = $session->exam;
        $order = $session->question_order;

        $questionsQuery = $exam->questions()->with(['options' => function ($q) {
            $q->select('id', 'question_id', 'option_text', 'sort_order');
        }]);

        $questions = $questionsQuery->get();

        // Sort questions according to this student's randomized sequence
        if (!empty($order) && is_array($order)) {
            $orderMap = array_flip($order);
            $questions = $questions->sortBy(function ($q) use ($orderMap) {
                return $orderMap[$q->id] ?? 9999;
            })->values();
        }

        // Format clean payload for Canvas rendering
        $payload = $questions->map(function ($q, $index) use ($exam) {
            $options = $q->options;

            // Randomize options if exam setting enabled
            if ($exam->randomize_options) {
                $options = $options->shuffle();
            }

            return [
                'id' => $q->id,
                'index' => $index + 1,
                'prompt' => $q->prompt,
                'code_snippet' => $q->code_snippet,
                'language' => $q->language ?? 'code',
                'question_type' => $q->question_type,
                'points' => (int) $q->points,
                'options' => $options->map(function ($opt) {
                    return [
                        'id' => $opt->id,
                        'text' => $opt->option_text,
                    ];
                })->values(),
            ];
        });

        return response()->json([
            'success' => true,
            'exam_title' => $exam->title,
            'course_code' => $exam->course_code,
            'total_questions' => $payload->count(),
            'duration_minutes' => $exam->duration_minutes,
            'remaining_seconds' => $session->remainingSeconds(),
            'strikes_count' => $session->strikes_count,
            'max_strikes' => $session->max_strikes,
            'student_name' => $session->student_name,
            'student_id' => $session->student_id_code,
            'ip_address' => request()->ip(),
            'watermark_text' => "{$session->student_name} | {$session->student_id_code} | " . request()->ip(),
            'saved_answers' => $session->answers ?? new \stdClass(),
            'questions' => $payload,
        ]);
    }

    /**
     * Heartbeat endpoint: validates time remaining and auto-saves answers.
     */
    public function heartbeat(Request $request, string $token): JsonResponse
    {
        $session = ExamSession::where('session_token', $token)->with('exam')->first();

        if (!$session) {
            return response()->json(['error' => 'Invalid session'], 404);
        }

        // Optionally update saved draft answers
        if ($request->has('answers')) {
            $session->answers = $request->input('answers');
        }

        $session->last_heartbeat_at = now();
        $session->save();

        // Check if terminated
        if ($session->isTerminated()) {
            return response()->json([
                'active' => false,
                'terminated' => true,
                'status' => 'terminated_cheat',
                'reason' => $session->termination_reason,
                'redirect_url' => route('student.result', ['session' => $session->session_token]),
            ]);
        }

        // Check authoritative server expiration
        if ($session->isExpired()) {
            $this->gradingService->gradeSession($session, $session->answers ?? []);

            return response()->json([
                'active' => false,
                'terminated' => false,
                'status' => 'completed',
                'message' => 'Time expired. Exam submitted.',
                'redirect_url' => route('student.result', ['session' => $session->session_token]),
            ]);
        }

        return response()->json([
            'active' => true,
            'terminated' => false,
            'status' => $session->status,
            'remaining_seconds' => $session->remainingSeconds(),
            'strikes_count' => $session->strikes_count,
            'max_strikes' => $session->max_strikes,
        ]);
    }

    /**
     * Report an anti-cheat infraction (visibility loss, devtools, shortcuts, blur).
     */
    public function recordInfraction(Request $request, string $token): JsonResponse
    {
        $session = ExamSession::where('session_token', $token)->first();

        if (!$session) {
            return response()->json(['error' => 'Invalid session'], 404);
        }

        $result = $this->proctoringService->recordInfraction($session, [
            'infraction_type' => $request->input('infraction_type', 'unknown'),
            'severity' => $request->input('severity', 'medium'),
            'details' => $request->input('details'),
            'client_timestamp' => $request->input('client_timestamp'),
            'ip_address' => $request->ip(),
        ]);

        if ($result['terminated']) {
            $result['redirect_url'] = route('student.result', ['session' => $session->session_token]);
        }

        return response()->json($result);
    }
}
