@extends('layouts.admin')

@section('title', 'Exam Report: ' . $exam->title)
@section('page_title', 'Performance & Proctor Audit Report')
@section('page_subtitle', $exam->title . ' (' . ($exam->course_code ?? 'EXAM') . ')')

@section('topbar_actions')
    <a href="{{ route('admin.exams.index') }}" class="px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-200 shadow-sm transition">
        &larr; Back to Exams
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Overview Statistics Banner -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="p-4 rounded-xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Attempts</span>
            <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalAttempts }}</p>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Completed</span>
            <p class="text-2xl font-extrabold text-indigo-700 mt-1">{{ $completed }}</p>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Disqualified</span>
            <p class="text-2xl font-extrabold text-rose-600 mt-1">{{ $terminated }}</p>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Candidates Passed</span>
            <p class="text-2xl font-extrabold text-emerald-700 mt-1">{{ $passed }}</p>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Pass Rate</span>
            <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ $passRate }}%</p>
        </div>
    </div>

    <!-- Candidate Attempts Table -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-sm text-slate-900">Candidate Examination Sessions</h3>
            <span class="text-xs text-slate-500 font-medium">Passing Threshold: {{ $exam->pass_percentage }}%</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-500 border-b border-slate-100">
                        <th class="pb-3 font-semibold">Candidate</th>
                        <th class="pb-3 font-semibold">Student ID</th>
                        <th class="pb-3 font-semibold">IP Address</th>
                        <th class="pb-3 font-semibold">Status</th>
                        <th class="pb-3 font-semibold">Strikes</th>
                        <th class="pb-3 font-semibold">Score / Max</th>
                        <th class="pb-3 font-semibold">Percentage</th>
                        <th class="pb-3 font-semibold">Verdict</th>
                        <th class="pb-3 font-semibold text-right">Submitted At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sessions as $sess)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3 font-semibold text-slate-900">
                                <div>{{ $sess->student_name }}</div>
                                <div class="text-[10px] text-slate-500">{{ $sess->student_email }}</div>
                            </td>
                            <td class="py-3 font-mono text-slate-700">{{ $sess->student_id_code }}</td>
                            <td class="py-3 font-mono text-slate-500 text-[11px]">{{ $sess->ip_address ?? 'N/A' }}</td>
                            <td class="py-3">
                                @if($sess->status === 'terminated_cheat')
                                    <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200 font-semibold text-[10px]">Terminated (Cheat)</span>
                                @elseif($sess->status === 'completed')
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold text-[10px]">Completed</span>
                                @elseif($sess->status === 'in_progress')
                                    <span class="px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 font-semibold text-[10px]">Taking Exam</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 font-semibold text-[10px]">Waiting</span>
                                @endif
                            </td>
                            <td class="py-3 font-mono">
                                <span class="{{ $sess->strikes_count > 0 ? 'text-rose-700 font-bold' : 'text-slate-500' }}">
                                    {{ $sess->strikes_count }} / {{ $sess->max_strikes }}
                                </span>
                            </td>
                            <td class="py-3 text-slate-700 font-mono">
                                {{ number_format($sess->score, 1) }} / {{ number_format($sess->max_score, 1) }}
                            </td>
                            <td class="py-3 font-bold text-slate-900 font-mono">
                                {{ number_format($sess->percentage, 1) }}%
                            </td>
                            <td class="py-3">
                                @if($sess->passed)
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[10px]">PASS</span>
                                @elseif($sess->status === 'terminated_cheat')
                                    <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200 font-bold text-[10px]">DISQUALIFIED</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 font-bold text-[10px]">FAIL</span>
                                @endif
                            </td>
                            <td class="py-3 text-right text-slate-500 text-[11px]">
                                {{ $sess->submitted_at ? $sess->submitted_at->format('M d, H:i') : '--' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">No student attempts recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $sessions->links() }}</div>
    </div>
</div>
@endsection
