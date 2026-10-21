@extends('layouts.student')

@section('title', 'Examination Result: ' . $session->exam->title)

@section('content')
<div class="min-h-full flex items-center justify-center p-6 relative overflow-y-auto">

    <!-- Ambient glow -->
    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] {{ $session->isTerminated() ? 'bg-rose-600/10' : ($session->passed ? 'bg-emerald-600/10' : 'bg-indigo-600/10') }} rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-2xl w-full relative z-10 py-10 space-y-6">

        @if($session->isTerminated())
            <!-- CHEAT DISQUALIFICATION CARD -->
            <div class="bg-slate-900 border-2 border-rose-600/80 rounded-3xl p-8 shadow-2xl space-y-6">
                <div class="text-center">
                    <div class="w-16 h-16 rounded-3xl bg-rose-600/20 text-rose-500 border border-rose-500/40 flex items-center justify-center mx-auto mb-4 animate-pulse">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-rose-600 text-white font-mono text-xs font-bold uppercase tracking-wider">
                        DISQUALIFIED • TERMINATED (CHEAT)
                    </span>
                    <h1 class="text-2xl font-black text-white mt-3">{{ $session->exam->title }}</h1>
                    <p class="text-xs text-rose-300 mt-1">Integrity Violation Threshold Exceeded</p>
                </div>

                <!-- Zero Score Banner -->
                <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-800 text-center">
                    <span class="text-xs font-bold text-rose-300 uppercase tracking-wider block">Official Final Score</span>
                    <span class="text-4xl font-black text-rose-500 font-mono mt-1 block">0.00%</span>
                    <p class="text-[11px] text-rose-400 mt-1">Points Earned: 0.00 / {{ number_format($session->max_score, 1) }}</p>
                </div>

                <!-- Reason Details -->
                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-2 text-xs">
                    <strong class="text-white block font-semibold">Disqualification Reason:</strong>
                    <p class="text-rose-300 font-mono text-[11px] leading-relaxed">{{ $session->termination_reason ?? 'Accumulated infraction strikes beyond policy limit.' }}</p>
                </div>

                <!-- Infractions Audit Ledger -->
                <div class="space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Forensic Incident Evidence Log</h3>
                    <div class="space-y-2 max-h-60 overflow-y-auto">
                        @foreach($session->infractions as $inf)
                            <div class="p-3 rounded-xl bg-slate-950 border border-slate-800/80 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-3">
                                    <span class="w-6 h-6 rounded-md bg-rose-500/20 text-rose-400 font-bold text-[10px] flex items-center justify-center font-mono">
                                        #{{ $inf->strike_number }}
                                    </span>
                                    <div>
                                        <p class="font-bold text-white">{{ $inf->formatted_type }}</p>
                                        <p class="text-[10px] text-slate-500 font-mono">{{ $inf->created_at->format('H:i:s') }} • IP: {{ $inf->ip_address }}</p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-rose-400 uppercase font-mono">{{ $inf->severity }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-800 text-center text-xs text-slate-500">
                    A formal incident report and forensic watermark capture have been submitted to the university proctoring committee.
                </div>
            </div>

        @else
            <!-- NORMAL EXAMINATION RESULT CARD -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl space-y-6">
                <div class="text-center">
                    <div class="w-16 h-16 rounded-3xl {{ $session->passed ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-300' }} flex items-center justify-center mx-auto mb-4">
                        @if($session->passed)
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        @endif
                    </div>

                    <span class="px-3.5 py-1 rounded-full {{ $session->passed ? 'bg-emerald-500 text-white' : 'bg-rose-600 text-white' }} font-bold text-xs uppercase tracking-wider shadow">
                        {{ $session->passed ? 'EXAMINATION PASSED' : 'NOT PASSED' }}
                    </span>

                    <h1 class="text-2xl font-black text-white mt-3">{{ $session->exam->title }}</h1>
                    <p class="text-xs text-slate-400 mt-1">Official Candidate Score Report</p>
                </div>

                <!-- Score KPI Cards -->
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800">
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block">Final Percentage</span>
                        <span class="text-2xl font-black font-mono {{ $session->passed ? 'text-emerald-400' : 'text-slate-300' }} mt-1 block">
                            {{ number_format($session->percentage, 1) }}%
                        </span>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800">
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block">Points Scored</span>
                        <span class="text-2xl font-black font-mono text-white mt-1 block">
                            {{ number_format($session->score, 1) }} / {{ number_format($session->max_score, 1) }}
                        </span>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800">
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block">Passing Threshold</span>
                        <span class="text-2xl font-black font-mono text-indigo-400 mt-1 block">
                            {{ number_format($session->exam->pass_percentage, 1) }}%
                        </span>
                    </div>
                </div>

                <!-- Candidate Details Summary -->
                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Candidate:</span>
                        <span class="font-bold text-white">{{ $session->student_name }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Student ID:</span>
                        <span class="font-mono text-indigo-300 font-bold">{{ $session->student_id_code }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Submission Timestamp:</span>
                        <span class="font-mono text-slate-300">{{ $session->submitted_at ? $session->submitted_at->toDayDateTimeString() : now()->toDayDateTimeString() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Security Infractions:</span>
                        <span class="font-mono {{ $session->strikes_count > 0 ? 'text-amber-400 font-bold' : 'text-emerald-400' }}">
                            {{ $session->strikes_count }} strikes recorded
                        </span>
                    </div>
                </div>

                <!-- SMTP Notification confirmation badge -->
                <div class="p-3.5 rounded-xl bg-indigo-950/40 border border-indigo-700/50 flex items-center justify-between text-xs text-indigo-200">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Score report dispatched to: <strong>{{ $session->student_email }}</strong>
                    </span>
                    <span class="text-indigo-400 font-bold text-[10px]">VERIFIED</span>
                </div>

                <div class="pt-4 border-t border-slate-800 flex justify-center">
                    <a href="{{ route('home') }}" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-xl border border-slate-700 transition">
                        Return to Homepage
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
