<?php

namespace App\Services;

use App\Models\ExamSession;
use App\Models\Question;
use App\Models\QuestionOption;

class ExamGradingService
{
    public function __construct(
        protected DynamicMailService $mailService
    ) {}

    /**
     * Grade student exam submission and calculate final score.
     */
    public function gradeSession(ExamSession $session, array $submittedAnswers): ExamSession
    {
        // If already terminated for cheating, score remains 0
        if ($session->isTerminated()) {
            return $session;
        }

        $exam = $session->exam;
        $questions = $exam->questions()->with('options')->get();

        $totalEarnedPoints = 0.0;
        $maxPossiblePoints = 0.0;

        foreach ($questions as $question) {
            $questionPoints = (float) $question->points;
            $maxPossiblePoints += $questionPoints;

            $selectedOptionId = $submittedAnswers[$question->id] ?? null;

            if ($selectedOptionId) {
                // Check if selected option is marked as correct
                $correctOption = $question->options->firstWhere('is_correct', true);

                if ($correctOption && (int) $selectedOptionId === (int) $correctOption->id) {
                    $totalEarnedPoints += $questionPoints;
                }
            }
        }

        $percentage = $maxPossiblePoints > 0 ? ($totalEarnedPoints / $maxPossiblePoints) * 100 : 0.0;
        $passThreshold = (float) $exam->pass_percentage;
        $passed = $percentage >= $passThreshold;

        $session->update([
            'status' => 'completed',
            'score' => $totalEarnedPoints,
            'max_score' => $maxPossiblePoints,
            'percentage' => round($percentage, 2),
            'passed' => $passed,
            'answers' => $submittedAnswers,
            'submitted_at' => now(),
        ]);

        // Dispatch student score report email
        $this->mailService->sendStudentScoreReport($session);

        return $session;
    }
}
