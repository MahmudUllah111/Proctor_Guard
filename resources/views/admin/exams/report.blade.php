@extends('layouts.admin')

@section('title', 'Exam Report: ' . $exam->title)
@section('page_title', 'Performance & Proctor Audit Report')
@section('page_subtitle', $exam->title . ' (' . ($exam->course_code ?? 'EXAM') . ')')

@section('topbar_actions')
    <a href="{{ route('admin.exams.index') }}" class="btn-secondary"><x-icon name="arrow-left" class="w-3.5 h-3.5" />Exams</a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Overview Statistics Banner -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="card p-4">
            <span class="text-xs font-medium text-slate-500">Total Attempts</span>
            <p class="text-2xl font-semibold num text-slate-900 mt-1">{{ $totalAttempts }}</p>
        </div>
        <div class="card p-4">
            <span class="text-xs font-medium text-slate-500">Completed</span>
            <p class="text-2xl font-semibold num text-indigo-700 mt-1">{{ $completed }}</p>
        </div>
        <div class="card p-4">
            <span class="text-xs font-medium text-slate-500">Disqualified</span>
            <p class="text-2xl font-semibold num text-rose-600 mt-1">{{ $terminated }}</p>
        </div>
        <div class="card p-4">
            <span class="text-xs font-medium text-slate-500">Candidates Passed</span>
            <p class="text-2xl font-semibold num text-emerald-700 mt-1">{{ $passed }}</p>
        </div>
        <div class="card p-4">
            <span class="text-xs font-medium text-slate-500">Pass Rate</span>
            <p class="text-2xl font-semibold num text-slate-900 mt-1">{{ $passRate }}%</p>
        </div>
    </div>

    <!-- Candidate Attempts Table -->
    <div class="card overflow-hidden">
        <div class="card-header">
            <h3 class="card-title">Candidate sessions</h3>
            <span class="text-xs text-slate-500">Pass threshold <span class="num font-semibold text-slate-900">{{ $exam->pass_percentage }}%</span></span>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Candidate</th>
                        <th>Student ID</th>
                        <th>IP Address</th>
                        <th>Status</th>
                        <th class="text-right">Strikes</th>
                        <th class="text-right">Score</th>
                        <th class="text-right">Percent</th>
                        <th>Verdict</th>
                        <th class="text-right">Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $sess)
                        <tr>
                            <td class="font-medium text-slate-900">
                                <div>{{ $sess->student_name }}</div>
                                <div class="text-[11px] font-normal text-slate-500">{{ $sess->student_email }}</div>
                            </td>
                            <td class="font-mono text-slate-700">{{ $sess->student_id_code }}</td>
                            <td class="num text-slate-500">{{ $sess->ip_address ?? '—' }}</td>
                            <td>
                                <span class="{{ $sess->status_pill }}">{{ $sess->status_label }}</span>
                            </td>
                            <td class="num text-right">
                                <span class="{{ $sess->strikes_count > 0 ? 'text-rose-600 font-semibold' : 'text-slate-500' }}">
                                    {{ $sess->strikes_count }}/{{ $sess->max_strikes }}
                                </span>
                            </td>
                            <td class="num text-right text-slate-700">
                                {{ number_format($sess->score, 1) }} / {{ number_format($sess->max_score, 1) }}
                            </td>
                            <td class="num text-right font-semibold text-slate-900">
                                {{ number_format($sess->percentage, 1) }}%
                            </td>
                            <td>
                                @if($sess->passed)
                                    <span class="pill-emerald">Pass</span>
                                @elseif($sess->status === 'terminated_cheat')
                                    <span class="pill-rose">Disqualified</span>
                                @else
                                    <span class="pill-slate">Fail</span>
                                @endif
                            </td>
                            <td class="text-right num text-slate-500 whitespace-nowrap">
                                {{ $sess->submitted_at ? $sess->submitted_at->format('M d, H:i') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-10 text-center text-slate-400">No student attempts recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sessions->hasPages())<div class="px-5 py-3 border-t border-slate-100">{{ $sessions->links() }}</div>@endif
    </div>
</div>
@endsection
