<?php

namespace App\Services;

use App\Models\ExamSession;
use App\Models\ProctoringInfraction;
use Illuminate\Support\Facades\Log;

class ProctoringService
{
    public function __construct(
        protected DynamicMailService $mailService
    ) {}

    /**
     * Record an infraction for an exam session and enforce strikes.
     */
    public function recordInfraction(ExamSession $session, array $data): array
    {
        // If already completed or terminated, return immediately
        if ($session->isCompleted()) {
            return [
                'terminated' => $session->isTerminated(),
                'status' => $session->status,
                'strikes_count' => $session->strikes_count,
                'max_strikes' => $session->max_strikes,
                'reason' => $session->termination_reason,
            ];
        }

        $type = $data['infraction_type'] ?? 'unknown_infraction';
        $severity = $data['severity'] ?? 'medium';
        $details = $data['details'] ?? null;
        $clientTimestamp = $data['client_timestamp'] ?? now()->toIso8601String();
        $ip = $data['ip_address'] ?? request()->ip();

        // Increment strikes count
        $session->increment('strikes_count');
        $session->refresh();

        $currentStrike = $session->strikes_count;
        $maxStrikes = $session->max_strikes;

        // Create infraction log record
        $infraction = ProctoringInfraction::create([
            'exam_session_id' => $session->id,
            'infraction_type' => $type,
            'severity' => $severity,
            'strike_number' => $currentStrike,
            'details' => is_array($details) ? json_encode($details) : (string) $details,
            'client_timestamp' => $clientTimestamp,
            'server_timestamp' => now(),
            'ip_address' => $ip,
        ]);

        $terminated = false;
        $reason = null;

        // Check if strike threshold reached
        if ($currentStrike >= $maxStrikes) {
            $terminated = true;
            $reason = "Exam terminated automatically: Exceeded allowed anti-cheat infraction limit ({$currentStrike}/{$maxStrikes}). Violations: {$infraction->formatted_type}.";

            $session->update([
                'status' => 'terminated_cheat',
                'score' => 0.00,
                'percentage' => 0.00,
                'passed' => false,
                'submitted_at' => now(),
                'termination_reason' => $reason,
            ]);

            // Dispatch admin alert email
            $this->mailService->sendAdminStrikeAlert($session, $infraction);

            Log::warning("Exam session {$session->id} terminated due to cheating infraction", [
                'student' => $session->student_name,
                'id_code' => $session->student_id_code,
                'infraction' => $type,
                'strikes' => $currentStrike,
            ]);
        }

        return [
            'terminated' => $terminated,
            'status' => $session->status,
            'strikes_count' => $currentStrike,
            'max_strikes' => $maxStrikes,
            'reason' => $reason,
            'warning_message' => $terminated ? $reason : "Warning Strike {$currentStrike}/{$maxStrikes}: {$infraction->formatted_type}. Please return to fullscreen exam window.",
        ];
    }

    /**
     * Manually terminate an active session from the proctor ledger.
     */
    public function forceTerminate(ExamSession $session, string $manualReason = 'Manually terminated by proctor administrator'): ExamSession
    {
        $session->update([
            'status' => 'terminated_cheat',
            'score' => 0.00,
            'percentage' => 0.00,
            'passed' => false,
            'submitted_at' => now(),
            'termination_reason' => $manualReason,
        ]);

        $infraction = ProctoringInfraction::create([
            'exam_session_id' => $session->id,
            'infraction_type' => 'tamper_attempt',
            'severity' => 'critical',
            'strike_number' => $session->strikes_count + 1,
            'details' => $manualReason,
            'server_timestamp' => now(),
            'ip_address' => request()->ip(),
        ]);

        $this->mailService->sendAdminStrikeAlert($session, $infraction);

        return $session;
    }

    /**
     * Reset strikes for an active candidate.
     */
    public function resetStrikes(ExamSession $session): ExamSession
    {
        $session->update([
            'strikes_count' => 0,
            'status' => $session->status === 'terminated_cheat' ? 'in_progress' : $session->status,
            'termination_reason' => null,
        ]);

        return $session;
    }
}
