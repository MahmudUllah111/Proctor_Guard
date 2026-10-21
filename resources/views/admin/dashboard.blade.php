@extends('layouts.admin')

@section('title', 'Executive Dashboard')
@section('page_title', 'Integrity & Proctoring Overview')
@section('page_subtitle', 'Live system telemetry, security strikes, and examination statistics')

@section('topbar_actions')
    <a href="{{ route('admin.proctoring.index') }}" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs flex items-center gap-2 shadow-sm transition">
        <span class="relative flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
        </span>
        Live Proctoring Feed
    </a>
@endsection

@section('content')
<div class="space-y-8">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Active Candidates -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Active Test Takers</span>
                <span class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $activeCandidates }}</span>
                <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full flex items-center gap-1">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                    Live Now
                </span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">Candidates currently in waiting room or taking exam</p>
        </div>

        <!-- Cheat Infractions / Disqualified -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Security Terminations</span>
                <span class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-rose-600 tracking-tight">{{ $cheatTerminated }}</span>
                <span class="text-xs font-semibold text-slate-500">({{ $totalInfractions }} total strikes)</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">Exams automatically terminated and scored at 0%</p>
        </div>

        <!-- Pass Rate -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Average Pass Rate</span>
                <span class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $passRate }}%</span>
                <span class="text-xs font-semibold text-slate-500">({{ $completedExams }} submissions)</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">Verified candidate scoring accuracy</p>
        </div>

        <!-- Total Published Exams -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Exams</span>
                <span class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $totalExams }}</span>
                <span class="text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded-full">Active CMS</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">Zero hardcoding architecture</p>
        </div>
    </div>

    <!-- Active Exams & Live Waiting Room Controls -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Exams Status Table -->
        <div class="lg:col-span-2 bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Examination Release & Waiting Room Control
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Toggle release status to immediately release questions to candidates in waiting rooms</p>
                </div>
                <a href="{{ route('admin.exams.create') }}" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Exam
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-500 border-b border-slate-100">
                            <th class="pb-3 font-semibold">Course & Title</th>
                            <th class="pb-3 font-semibold">Duration</th>
                            <th class="pb-3 font-semibold">Questions</th>
                            <th class="pb-3 font-semibold">Waiting Room Release</th>
                            <th class="pb-3 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($activeExams as $exam)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 font-medium text-slate-900">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200 font-mono text-[10px]">{{ $exam->course_code ?? 'EXAM' }}</span>
                                        <a href="{{ route('admin.exams.edit', $exam) }}" class="hover:text-indigo-600 transition font-semibold">{{ $exam->title }}</a>
                                    </div>
                                </td>
                                <td class="py-3.5 text-slate-600">{{ $exam->duration_minutes }} mins</td>
                                <td class="py-3.5 text-slate-600">{{ $exam->questions_count }} questions</td>
                                <td class="py-3.5">
                                    <form action="{{ route('admin.exams.toggle-release', $exam) }}" method="POST" class="inline">
                                        @csrf
                                        @if($exam->is_released)
                                            <button type="submit" class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold text-[11px] hover:bg-emerald-100 transition flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                                Released (Active)
                                            </button>
                                        @else
                                            <button type="submit" class="px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 font-semibold text-[11px] hover:bg-amber-100 transition flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Waiting Room Locked
                                            </button>
                                        @endif
                                    </form>
                                </td>
                                <td class="py-3.5 text-right space-x-2 font-medium">
                                    <a href="{{ route('student.entry', $exam) }}" target="_blank" class="text-indigo-600 hover:text-indigo-700">Student Link</a>
                                    <span class="text-slate-300">•</span>
                                    <a href="{{ route('admin.exams.report', $exam) }}" class="text-slate-500 hover:text-slate-800">Audit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">No exams configured yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Security Alerts -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Recent Security Infractions
                    </h3>
                    <a href="{{ route('admin.proctoring.index') }}" class="text-[11px] text-rose-700 hover:text-rose-800 font-semibold">View All</a>
                </div>

                <div class="space-y-3">
                    @forelse($recentInfractions as $inf)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 flex items-start gap-3">
                            <span class="p-2 rounded-lg bg-rose-50 text-rose-600 border border-rose-200 flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </span>
                            <div class="flex-1 min-w-0 text-xs">
                                <div class="flex items-center justify-between">
                                    <p class="font-bold text-slate-900 truncate">{{ $inf->examSession->student_name ?? 'Candidate' }}</p>
                                    <span class="text-[10px] text-rose-700 font-mono font-semibold">Strike {{ $inf->strike_number }}</span>
                                </div>
                                <p class="text-slate-600 text-[11px] truncate">{{ $inf->formatted_type }}</p>
                                <p class="text-slate-400 text-[10px] mt-1">{{ $inf->created_at->diffForHumans() }} • IP: {{ $inf->ip_address }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-400">
                            No infractions detected. System integrity clean.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-between text-xs">
                    <span class="text-slate-500 font-medium">Proctoring Engine:</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 font-semibold font-mono text-[11px]">Strict (Canvas Raster)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Candidate Telemetry Table -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Live Candidate Telemetry
                </h3>
                <p class="text-xs text-slate-500">Real-time status of candidate sessions</p>
            </div>
            <a href="{{ route('admin.proctoring.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold">Open Live Ledger &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-500 border-b border-slate-100">
                        <th class="pb-3 font-semibold">Candidate</th>
                        <th class="pb-3 font-semibold">Student ID</th>
                        <th class="pb-3 font-semibold">Exam</th>
                        <th class="pb-3 font-semibold">Status</th>
                        <th class="pb-3 font-semibold">Strikes</th>
                        <th class="pb-3 font-semibold">Score</th>
                        <th class="pb-3 font-semibold text-right">Heartbeat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentSessions as $session)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3 font-semibold text-slate-900">{{ $session->student_name }}</td>
                            <td class="py-3 font-mono text-slate-700">{{ $session->student_id_code }}</td>
                            <td class="py-3 text-slate-600">{{ $session->exam->title ?? 'N/A' }}</td>
                            <td class="py-3">
                                @if($session->status === 'terminated_cheat')
                                    <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200 font-semibold text-[10px]">Terminated (Cheat)</span>
                                @elseif($session->status === 'completed')
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold text-[10px]">Submitted</span>
                                @elseif($session->status === 'in_progress')
                                    <span class="px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 font-semibold text-[10px]">In Exam</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 font-semibold text-[10px]">Waiting Room</span>
                                @endif
                            </td>
                            <td class="py-3 font-mono">
                                <span class="{{ $session->strikes_count > 0 ? 'text-rose-700 font-bold' : 'text-slate-500' }}">
                                    {{ $session->strikes_count }} / {{ $session->max_strikes }}
                                </span>
                            </td>
                            <td class="py-3 font-bold text-slate-900">
                                {{ $session->status === 'completed' ? number_format($session->percentage, 1) . '%' : '--' }}
                            </td>
                            <td class="py-3 text-right text-slate-500 text-[11px]">
                                {{ $session->last_heartbeat_at ? $session->last_heartbeat_at->diffForHumans() : 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400">No candidate sessions logged yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
