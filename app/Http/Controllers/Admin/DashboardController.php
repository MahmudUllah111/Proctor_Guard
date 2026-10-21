<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\FormSubmission;
use App\Models\ProctoringInfraction;

class DashboardController extends Controller
{
    public function index()
    {
        $totalExams = Exam::count();
        $activeCandidates = ExamSession::whereIn('status', ['waiting_room', 'in_progress'])->count();
        $completedExams = ExamSession::where('status', 'completed')->count();
        $cheatTerminated = ExamSession::where('status', 'terminated_cheat')->count();
        $totalInfractions = ProctoringInfraction::count();
        $pendingLeads = FormSubmission::where('status', 'new')->count();

        // Calculate pass rate
        $totalGraded = ExamSession::whereIn('status', ['completed', 'terminated_cheat'])->count();
        $totalPassed = ExamSession::where('passed', true)->count();
        $passRate = $totalGraded > 0 ? round(($totalPassed / $totalGraded) * 100, 1) : 0;

        // Recent live sessions
        $recentSessions = ExamSession::with('exam')
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get();

        // Recent security infractions
        $recentInfractions = ProctoringInfraction::with('examSession.exam')
            ->orderBy('id', 'desc')
            ->limit(6)
            ->get();

        // Active exams
        $activeExams = Exam::withCount(['sessions', 'questions'])
            ->orderBy('is_released', 'desc')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalExams',
            'activeCandidates',
            'completedExams',
            'cheatTerminated',
            'totalInfractions',
            'pendingLeads',
            'passRate',
            'recentSessions',
            'recentInfractions',
            'activeExams'
        ));
    }
}
