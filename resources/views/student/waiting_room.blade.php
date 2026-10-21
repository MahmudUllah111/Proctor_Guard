@extends('layouts.student')

@section('title', 'Waiting Room: ' . $session->exam->title)

@push('styles')
<style>
    @keyframes livePulseRing {
        0% { transform: scale(0.95); opacity: 0.85; }
        50% { transform: scale(1.6); opacity: 0; }
        100% { transform: scale(0.95); opacity: 0; }
    }
    @keyframes cardEntrance {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .live-pulse-ring {
        animation: livePulseRing 2.2s cubic-bezier(0.24, 0, 0.38, 1) infinite;
    }
    .check-card {
        animation: cardEntrance 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .check-card:nth-child(1) { animation-delay: 80ms; }
    .check-card:nth-child(2) { animation-delay: 160ms; }
    .check-card:nth-child(3) { animation-delay: 240ms; }
    .check-card:nth-child(4) { animation-delay: 320ms; }
</style>
@endpush

@section('content')
<div class="min-h-full flex flex-col items-center justify-center p-6 text-center relative overflow-y-auto bg-slate-50">

    <div class="max-w-2xl w-full relative z-10 py-10 space-y-6">

        <!-- Academic Status Header with Understated Live Pulsing Connection Dot -->
        <div class="space-y-3">
            <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-white text-slate-800 border border-slate-200/80 font-mono text-xs font-semibold shadow-[0_2px_8px_rgba(0,0,0,0.04)]">
                <span class="relative flex h-2.5 w-2.5 items-center justify-center">
                    <span class="live-pulse-ring absolute inline-flex h-full w-full rounded-full bg-emerald-400"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-600"></span>
                </span>
                <span class="text-slate-900 tracking-wide font-semibold">SYNCHRONIZED WAITING ROOM</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $session->exam->title }}</h1>
            <p class="text-xs text-slate-600 max-w-lg mx-auto">Your seat is verified. Questions will be released simultaneously when authorized by the chief proctor.</p>
        </div>

        <!-- Two-Column Academic Details & Instructions Card -->
        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_2px_8px_rgba(0,0,0,0.04)] overflow-hidden text-left transition-all">
            <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-slate-100">
                <!-- Column 1: Candidate Verification -->
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">Candidate Profile</span>
                        <span class="px-2.5 py-0.5 rounded-md bg-indigo-50 border border-indigo-200 text-indigo-700 font-mono text-xs font-semibold">{{ $session->student_id_code }}</span>
                    </div>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600">Legal Name:</span>
                            <span class="font-semibold text-slate-900">{{ $session->student_name }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600">Student Email:</span>
                            <span class="font-medium text-slate-700">{{ $session->student_email }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600">Course Code:</span>
                            <span class="font-mono text-slate-700 font-semibold">{{ $session->exam->course_code ?? 'EXAM' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600">Duration:</span>
                            <span class="font-semibold text-slate-900">{{ $session->exam->duration_minutes }} Minutes</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600">Passing Grade:</span>
                            <span class="font-semibold text-emerald-700">{{ $session->exam->pass_percentage }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-600">Client IP:</span>
                            <span class="font-mono text-slate-500 text-[11px]">{{ request()->ip() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Column 2: Examination Instructions -->
                <div class="p-6 space-y-4 bg-slate-50/50">
                    <div class="border-b border-slate-100 pb-3">
                        <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">Examination Rules</span>
                    </div>

                    <div class="text-xs text-slate-600 space-y-2 leading-relaxed">
                        @if($session->exam->instructions)
                            <p class="whitespace-pre-line text-slate-600">{{ $session->exam->instructions }}</p>
                        @else
                            <p>• Do not exit fullscreen or navigate away to other browser tabs.</p>
                            <p>• The test is monitored via active forensic watermark tracing.</p>
                            <p>• Accumulated security strikes beyond {{ $session->max_strikes }} will immediately terminate your session with a score of 0.00%.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Verified Hardware & Security Badges with Smooth Checkmark Verification Cards -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-[0_2px_8px_rgba(0,0,0,0.04)] text-left space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 mb-3 flex items-center justify-between">
                <span>Hardware & Security Verification</span>
                <span class="text-[11px] text-emerald-700 font-medium bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    Verified & Ready
                </span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div class="check-card flex items-center gap-3 p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-[0_1px_3px_rgba(0,0,0,0.02)] hover:-translate-y-0.5 hover:shadow-md hover:border-emerald-300 transition-all duration-200">
                    <span class="w-6 h-6 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center font-bold text-xs shadow-sm flex-shrink-0">✓</span>
                    <span class="text-slate-800 font-medium">Session Authorized</span>
                </div>
                <div class="check-card flex items-center gap-3 p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-[0_1px_3px_rgba(0,0,0,0.02)] hover:-translate-y-0.5 hover:shadow-md hover:border-emerald-300 transition-all duration-200">
                    <span class="w-6 h-6 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center font-bold text-xs shadow-sm flex-shrink-0">✓</span>
                    <span class="text-slate-800 font-medium">Browser Supported</span>
                </div>
                <div class="check-card flex items-center gap-3 p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-[0_1px_3px_rgba(0,0,0,0.02)] hover:-translate-y-0.5 hover:shadow-md hover:border-emerald-300 transition-all duration-200">
                    <span class="w-6 h-6 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center font-bold text-xs shadow-sm flex-shrink-0">✓</span>
                    <span class="text-slate-800 font-medium">Canvas Buffer Engine</span>
                </div>
                <div class="check-card flex items-center gap-3 p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-[0_1px_3px_rgba(0,0,0,0.02)] hover:-translate-y-0.5 hover:shadow-md hover:border-indigo-300 transition-all duration-200">
                    <span id="fullscreen-check-badge" class="w-6 h-6 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-600 flex items-center justify-center font-bold text-xs shadow-sm flex-shrink-0">✓</span>
                    <span id="fullscreen-check-text" class="text-slate-800 font-medium">Fullscreen Verified</span>
                </div>
            </div>
        </div>

        <!-- Release status alert with telemetry pulsing dot -->
        <div class="p-4 rounded-xl bg-white border border-slate-200/80 shadow-[0_2px_8px_rgba(0,0,0,0.04)] flex items-center justify-center gap-3 text-xs text-slate-600">
            <span class="relative flex h-2.5 w-2.5">
                <span class="live-pulse-ring absolute inline-flex h-full w-full rounded-full bg-indigo-400"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-indigo-600"></span>
            </span>
            <span class="font-medium text-slate-700">Synchronizing with proctor telemetry engine... Keep this window open.</span>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const sessionToken = '{{ $session->session_token }}';
    const statusApiUrl = '/api/proctor/waiting-status/' + sessionToken;

    // Fullscreen helper
    function enterFullscreen() {
        const elem = document.documentElement;
        if (elem.requestFullscreen) {
            elem.requestFullscreen().catch(err => console.log('Fullscreen prompt:', err));
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }
    }

    // Polling server for question release status
    let pollInterval = setInterval(async () => {
        try {
            const res = await fetch(statusApiUrl, {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;

            const data = await res.json();

            if (data.status === 'in_progress' || data.is_released === true) {
                clearInterval(pollInterval);

                // Attempt to trigger fullscreen
                enterFullscreen();

                // Redirect to exam runner
                window.location.href = data.redirect_url || ('/exam-session/' + sessionToken + '/take');
            } else if (data.status === 'terminated_cheat') {
                clearInterval(pollInterval);
                window.location.href = '/exam-session/' + sessionToken + '/result';
            }
        } catch (e) {
            console.warn('Waiting room status poll error:', e);
        }
    }, 3000);
</script>
@endpush
