@extends('layouts.student')

@section('title', 'Examination Result: ' . $session->exam->title)

@section('content')
<div class="min-h-full flex items-center justify-center p-6 relative overflow-y-auto bg-slate-50">

    <div class="max-w-2xl w-full relative z-10 py-10 space-y-6">

        @if($session->isTerminated())
            <!-- CHEAT DISQUALIFICATION AUDIT CARD -->
            <div class="bg-white border border-rose-200 rounded-3xl p-8 md:p-10 shadow-sm space-y-6">
                <div class="text-center">
                    <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <span class="px-3.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 font-mono text-xs font-bold uppercase tracking-wider">
                        DISQUALIFIED • TERMINATED (CHEAT)
                    </span>
                    <h1 class="text-2xl font-bold text-slate-900 mt-3">{{ $session->exam->title }}</h1>
                    <p class="text-xs text-rose-700 mt-1 font-medium">Integrity Violation Threshold Exceeded</p>
                </div>

                <!-- Zero Score Banner -->
                <div class="p-5 rounded-2xl bg-rose-50/50 border border-rose-200/80 text-center">
                    <span class="text-xs font-bold text-rose-700 uppercase tracking-wider block">Official Final Score</span>
                    <span class="text-4xl font-extrabold text-rose-700 font-mono mt-1 block">0.00%</span>
                    <p class="text-[11px] text-slate-500 mt-1">Points Earned: 0.00 / {{ number_format($session->max_score, 1) }}</p>
                </div>

                <!-- Reason Details -->
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1.5 text-xs">
                    <strong class="text-slate-900 block font-semibold">Disqualification Reason:</strong>
                    <p class="text-rose-700 font-mono text-[11px] leading-relaxed">{{ $session->termination_reason ?? 'Accumulated infraction strikes beyond policy limit.' }}</p>
                </div>

                <!-- Infractions Audit Ledger -->
                <div class="space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Forensic Incident Evidence Log</h3>
                    <div class="space-y-2 max-h-60 overflow-y-auto">
                        @foreach($session->infractions as $inf)
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-3">
                                    <span class="w-6 h-6 rounded-md bg-rose-100 text-rose-700 font-bold text-[10px] flex items-center justify-center font-mono">
                                        #{{ $inf->strike_number }}
                                    </span>
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $inf->formatted_type }}</p>
                                        <p class="text-[10px] text-slate-500 font-mono">{{ $inf->created_at->format('H:i:s') }} • IP: {{ $inf->ip_address }}</p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-rose-700 uppercase font-mono bg-rose-50 border border-rose-200 px-2 py-0.5 rounded">{{ $inf->severity }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-200 text-center text-xs text-slate-500">
                    A formal incident report and forensic watermark capture have been submitted to the examination board.
                </div>
            </div>

        @else
            <!-- NORMAL EXAMINATION RESULT CARD -->
            <div class="bg-white border border-slate-200/80 rounded-3xl p-8 md:p-10 shadow-[0_2px_8px_rgba(0,0,0,0.04)] space-y-6">
                <div class="text-center">
                    <div class="w-14 h-14 rounded-2xl {{ $session->passed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }} flex items-center justify-center mx-auto mb-4">
                        @if($session->passed)
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @else
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        @endif
                    </div>

                    <span class="inline-flex items-center px-3.5 py-1 rounded-full {{ $session->passed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }} font-bold text-xs uppercase tracking-wider">
                        {{ $session->passed ? 'EXAMINATION PASSED' : 'NOT PASSED' }}
                    </span>

                    <h1 class="text-2xl font-bold text-slate-900 mt-3">{{ $session->exam->title }}</h1>
                    <p class="text-xs text-slate-500 mt-1">Official Candidate Score Report</p>
                </div>

                <!-- Score KPI Cards -->
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-medium">Final Percentage</span>
                        <span class="text-2xl font-black font-mono {{ $session->passed ? 'text-emerald-700' : 'text-slate-900' }} mt-1 block">
                            {{ number_format($session->percentage, 1) }}%
                        </span>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-medium">Points Scored</span>
                        <span class="text-2xl font-black font-mono text-slate-900 mt-1 block">
                            {{ number_format($session->score, 1) }} / {{ number_format($session->max_score, 1) }}
                        </span>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-medium">Passing Threshold</span>
                        <span class="text-2xl font-black font-mono text-slate-700 mt-1 block">
                            {{ number_format($session->exam->pass_percentage, 1) }}%
                        </span>
                    </div>
                </div>

                <!-- Candidate Details Summary -->
                <div class="p-5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Candidate:</span>
                        <span class="font-semibold text-slate-900">{{ $session->student_name }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Student ID:</span>
                        <span class="font-mono text-slate-800 font-semibold">{{ $session->student_id_code }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Submission Timestamp:</span>
                        <span class="font-mono text-slate-600">{{ $session->submitted_at ? $session->submitted_at->toDayDateTimeString() : now()->toDayDateTimeString() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Security Infractions:</span>
                        <span class="font-mono {{ $session->strikes_count > 0 ? 'text-amber-800 font-bold' : 'text-emerald-700 font-semibold' }}">
                            {{ $session->strikes_count }} strikes recorded
                        </span>
                    </div>
                </div>

                <!-- SMTP Notification confirmation badge -->
                <div class="p-3.5 rounded-xl bg-indigo-50/70 border border-indigo-100 flex items-center justify-between text-xs text-indigo-950">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Score report dispatched to: <strong>{{ $session->student_email }}</strong>
                    </span>
                    <span class="text-indigo-700 font-bold text-[10px] bg-white border border-indigo-200 px-2 py-0.5 rounded">VERIFIED</span>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-center">
                    <a href="{{ route('home') }}" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm hover:shadow-md active:scale-95 transition-all duration-200">
                        Return to Portal Homepage &rarr;
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
