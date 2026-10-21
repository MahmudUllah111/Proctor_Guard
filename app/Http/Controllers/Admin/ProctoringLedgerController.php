<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\ProctoringInfraction;
use App\Services\ProctoringService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProctoringLedgerController extends Controller
{
    public function __construct(
        protected ProctoringService $proctoringService
    ) {}

    /**
     * Proctoring Ledger view.
     */
    public function index(Request $request)
    {
        $exams = Exam::orderBy('id', 'desc')->get();
        $selectedExamId = $request->input('exam_id');
        $selectedStatus = $request->input('status');

        $query = ExamSession::with(['exam', 'infractions'])
            ->when($selectedExamId, function ($q, $id) {
                $q->where('exam_id', $id);
            })
            ->when($selectedStatus, function ($q, $status) {
                $q->where('status', $status);
            })
            ->orderBy('id', 'desc');

        $sessions = $query->paginate(20)->withQueryString();

        return view('admin.proctoring.index', compact('sessions', 'exams', 'selectedExamId', 'selectedStatus'));
    }

    /**
     * Real-time JSON feed for live auto-updating proctoring telemetry.
     */
    public function feed(Request $request): JsonResponse
    {
        $selectedExamId = $request->input('exam_id');

        $sessions = ExamSession::with(['exam', 'infractions' => function ($q) {
                $q->latest()->limit(5);
            }])
            ->when($selectedExamId, function ($q, $id) {
                $q->where('exam_id', $id);
            })
            ->where(function ($q) {
                // Return active or recently updated sessions
                $q->whereIn('status', ['waiting_room', 'in_progress'])
                  ->orWhere('updated_at', '>=', Carbon::now()->subMinutes(10));
            })
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        $data = $sessions->map(function ($s) {
            $isHeartbeatRecent = $s->last_heartbeat_at && Carbon::parse($s->last_heartbeat_at)->diffInSeconds(now()) < 30;

            return [
                'id' => $s->id,
                'token' => $s->session_token,
                'student_name' => $s->student_name,
                'student_id' => $s->student_id_code,
                'student_email' => $s->student_email,
                'exam_title' => $s->exam->title ?? 'N/A',
                'course_code' => $s->exam->course_code ?? 'N/A',
                'status' => $s->status,
                'status_label' => match($s->status) {
                    'waiting_room' => 'Waiting Room',
                    'in_progress' => 'Active Test',
                    'completed' => 'Submitted',
                    'terminated_cheat' => 'Terminated (Cheat)',
                    default => ucfirst($s->status),
                },
                'strikes_count' => $s->strikes_count,
                'max_strikes' => $s->max_strikes,
                'remaining_seconds' => $s->remainingSeconds(),
                'ip_address' => $s->ip_address ?? '127.0.0.1',
                'last_heartbeat' => $s->last_heartbeat_at ? $s->last_heartbeat_at->diffForHumans() : 'Never',
                'is_online' => $isHeartbeatRecent,
                'infractions_count' => $s->infractions->count(),
                'termination_reason' => $s->termination_reason,
            ];
        });

        return response()->json([
            'timestamp' => now()->toIso8601String(),
            'active_count' => ExamSession::whereIn('status', ['waiting_room', 'in_progress'])->count(),
            'terminated_count' => ExamSession::where('status', 'terminated_cheat')->count(),
            'sessions' => $data,
        ]);
    }

    /**
     * Get detailed infraction audit logs for an examination session.
     */
    public function getInfractions(ExamSession $session): JsonResponse
    {
        $infractions = $session->infractions()->orderBy('id', 'desc')->get();

        return response()->json([
            'session' => [
                'id' => $session->id,
                'student_name' => $session->student_name,
                'student_id' => $session->student_id_code,
                'exam_title' => $session->exam->title,
                'strikes_count' => $session->strikes_count,
                'max_strikes' => $session->max_strikes,
                'status' => $session->status,
                'termination_reason' => $session->termination_reason,
            ],
            'infractions' => $infractions->map(function ($inf) {
                return [
                    'id' => $inf->id,
                    'type' => $inf->formatted_type,
                    'raw_type' => $inf->infraction_type,
                    'severity' => $inf->severity,
                    'strike_number' => $inf->strike_number,
                    'details' => $inf->details,
                    'timestamp' => $inf->created_at->format('M d, Y H:i:s'),
                    'time_ago' => $inf->created_at->diffForHumans(),
                    'ip_address' => $inf->ip_address,
                ];
            }),
        ]);
    }

    /**
     * Proctor manual force-termination for cheating.
     */
    public function terminate(Request $request, ExamSession $session)
    {
        $reason = $request->input('reason', 'Manually disqualified by Exam Proctor.');

        $this->proctoringService->forceTerminate($session, $reason);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Candidate session terminated.']);
        }

        return back()->with('warning', "Candidate {$session->student_name} has been disqualified and scored at zero.");
    }

    /**
     * Proctor reset strikes for a candidate.
     */
    public function resetStrikes(Request $request, ExamSession $session)
    {
        $this->proctoringService->resetStrikes($session);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Strikes reset successfully.']);
        }

        return back()->with('success', "Strikes reset for candidate {$session->student_name}.");
    }
}
