@extends('layouts.student')

@section('title', 'Waiting Room: ' . $session->exam->title)

@section('content')
<div class="min-h-full flex flex-col items-center justify-center p-6 text-center relative overflow-y-auto">

    <!-- Ambient glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-xl w-full relative z-10 py-10 space-y-8">

        <!-- Animated Sonar Radar -->
        <div class="relative w-32 h-32 mx-auto flex items-center justify-center">
            <span class="absolute inline-flex h-full w-full rounded-full bg-indigo-500/20 animate-ping"></span>
            <span class="absolute inline-flex h-24 w-24 rounded-full bg-indigo-500/30 animate-pulse"></span>
            <div class="relative w-16 h-16 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-2xl shadow-indigo-600/50">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 font-mono text-xs font-bold mb-3 border border-amber-500/30">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                SYNCHRONIZED WAITING ROOM
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">{{ $session->exam->title }}</h1>
            <p class="text-xs text-slate-400 mt-1">Awaiting administrative question release by exam proctor...</p>
        </div>

        <!-- Candidate Badge Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl text-left space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <span class="text-xs font-bold text-white uppercase tracking-wider">Candidate Verification</span>
                <span class="px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 font-mono text-[11px] font-bold">{{ $session->student_id_code }}</span>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                    <span class="text-slate-500 block">Candidate Name:</span>
                    <span class="font-bold text-white">{{ $session->student_name }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Registered Email:</span>
                    <span class="font-bold text-white">{{ $session->student_email }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Duration / Pass:</span>
                    <span class="font-bold text-white">{{ $session->exam->duration_minutes }} Mins • {{ $session->exam->pass_percentage }}%</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Client IP:</span>
                    <span class="font-mono text-slate-300">{{ request()->ip() }}</span>
                </div>
            </div>
        </div>

        <!-- System & Hardware Readiness Checklist -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 shadow-xl text-left space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Hardware & Integrity Pre-Check</h3>

            <div class="flex items-center gap-3 text-xs text-slate-300">
                <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-[10px]">✓</span>
                <span>Canvas Pixel Buffer Renderer Ready</span>
            </div>
            <div class="flex items-center gap-3 text-xs text-slate-300">
                <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-[10px]">✓</span>
                <span>Forensic Real-Time Watermark Layer Configured</span>
            </div>
            <div class="flex items-center gap-3 text-xs text-slate-300">
                <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-[10px]">✓</span>
                <span>Client Visibility & Window Focus Observers Active</span>
            </div>
            <div class="flex items-center gap-3 text-xs text-slate-300">
                <span id="fullscreen-check-badge" class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-[10px]">!</span>
                <span id="fullscreen-check-text">Fullscreen permission ready (will trigger automatically upon release)</span>
            </div>
        </div>

        <!-- Release status alert -->
        <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center gap-3 text-xs text-slate-400">
            <svg class="w-4 h-4 text-indigo-400 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
            <span>Synchronizing with proctor server... Keep this tab open.</span>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const sessionToken = '{{ $session->session_token }}';
    const statusApiUrl = '/api/proctor/waiting-status/' + sessionToken;

    // Request fullscreen helper
    function enterFullscreen() {
        const elem = document.documentElement;
        if (elem.requestFullscreen) {
            elem.requestFullscreen().catch(err => console.log('Fullscreen prompt:', err));
        }
    }

    // Polling interval checking if admin released the questions
    let pollInterval = setInterval(async () => {
        try {
            const res = await fetch(statusApiUrl);
            const data = await res.json();

            if (data.is_released) {
                clearInterval(pollInterval);
                enterFullscreen();
                window.location.href = data.exam_url;
            }
        } catch (e) {
            console.warn('Sync pending...');
        }
    }, 2000);
</script>
@endpush
