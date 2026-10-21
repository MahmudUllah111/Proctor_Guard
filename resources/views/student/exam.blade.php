@extends('layouts.student')

@section('title', 'Examination in Progress: ' . $session->exam->title)

@section('content')
<div id="exam-active-area" class="h-full flex flex-col bg-slate-950 overflow-hidden relative">

    <!-- Top Proctor Navigation & Timer Bar -->
    <header class="h-16 border-b border-slate-800 bg-slate-900/90 backdrop-blur-md px-6 flex items-center justify-between flex-shrink-0 z-20">
        <!-- Exam title & candidate info -->
        <div class="flex items-center gap-4">
            <div class="w-8 h-8 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold text-xs shadow-md shadow-indigo-600/30">
                PG
            </div>
            <div>
                <h2 class="text-sm font-extrabold text-white leading-tight flex items-center gap-2">
                    <span>{{ $session->exam->title }}</span>
                    <span class="px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 font-mono text-[10px]">{{ $session->exam->course_code ?? 'EXAM' }}</span>
                </h2>
                <p class="text-[11px] text-slate-400">
                    Candidate: <strong class="text-slate-200">{{ $session->student_name }}</strong> (<span class="font-mono text-indigo-300">{{ $session->student_id_code }}</span>)
                </p>
            </div>
        </div>

        <!-- Center: Authoritative Server Timer -->
        <div class="flex items-center gap-3 bg-slate-950 px-5 py-2 rounded-2xl border border-slate-800 shadow-inner">
            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Remaining:</span>
            <span id="timer-display" class="font-mono text-base font-extrabold text-white tracking-widest">
                --:--:--
            </span>
        </div>

        <!-- Right: Security Strikes Indicator & Submit Button -->
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-xs">
                <span class="text-slate-400 text-[11px]">Infraction Strikes:</span>
                <span id="strikes-counter-badge" class="px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-300 font-mono font-bold text-xs">
                    {{ $session->strikes_count }} / {{ $session->max_strikes }}
                </span>
            </div>

            <button id="submit-exam-btn" type="button" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/30 transition">
                Submit Exam
            </button>
        </div>
    </header>

    <!-- Main Workspace (Center: Question Canvas & Options, Right: Question Palette) -->
    <div class="flex-1 flex overflow-hidden">

        <!-- Question Viewport -->
        <div class="flex-1 overflow-y-auto p-6 md:p-8 flex flex-col items-center">
            <div class="max-w-3xl w-full space-y-6">

                <!-- 1. HTML5 Canvas Question & Code Pixel Buffer Area -->
                <div id="canvas-question-container" class="w-full">
                    <div class="p-12 text-center text-slate-500 bg-slate-900 border border-slate-800 rounded-2xl animate-pulse">
                        Rendering question into secure canvas pixel buffer...
                    </div>
                </div>

                <!-- 2. Multiple Choice Options Form -->
                <div class="bg-slate-900/60 border border-slate-800/80 rounded-2xl p-6 shadow-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Select Answer Option:</span>
                        <span id="current-question-num" class="text-xs font-mono text-indigo-400">Loading...</span>
                    </div>

                    <!-- Dynamic options buttons injected by ProctorEngine -->
                    <div id="question-options-list" class="space-y-3">
                        <!-- Populated dynamically via ProctorEngine -->
                    </div>
                </div>

                <!-- 3. Bottom Step Navigation Controls -->
                <div class="flex items-center justify-between pt-2">
                    <button id="prev-question-btn" type="button" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-slate-300 font-bold text-xs rounded-xl border border-slate-800 transition disabled:opacity-30 disabled:cursor-not-allowed">
                        &larr; Previous Question
                    </button>
                    <button id="next-question-btn" type="button" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition">
                        Next Question &rarr;
                    </button>
                </div>

            </div>
        </div>

        <!-- Right Side: Question Navigation Palette Sidebar -->
        <aside class="w-72 border-l border-slate-800 bg-slate-900/60 p-6 flex flex-col justify-between hidden lg:flex">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 flex items-center justify-between">
                    <span>Question Palette</span>
                    <span class="text-[10px] text-indigo-400 font-mono">Randomized Sequence</span>
                </h3>

                <!-- Grid of question numbers -->
                <div id="question-palette-grid" class="grid grid-cols-5 gap-2.5">
                    <!-- Populated dynamically via ProctorEngine -->
                </div>

                <div class="mt-6 pt-4 border-t border-slate-800 space-y-2 text-[11px] text-slate-400">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded bg-emerald-500"></span>
                        <span>Answered Question</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded bg-indigo-600"></span>
                        <span>Current Active Question</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded bg-slate-800"></span>
                        <span>Unanswered Question</span>
                    </div>
                </div>
            </div>

            <!-- Anti-Cheat Status Badge -->
            <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 text-center">
                <div class="flex items-center justify-center gap-1.5 text-xs font-bold text-emerald-400 mb-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Canvas Proctor Active
                </div>
                <p class="text-[10px] text-slate-500">DOM Scraping Disabled • Watermark Live</p>
            </div>
        </aside>

    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/proctor-engine.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.examEngine = new ProctorEngine({
            token: '{{ $session->session_token }}',
            apiUrl: '/api/proctor',
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.content,
            examTitle: '{{ addslashes($session->exam->title) }}',
            studentName: '{{ addslashes($session->student_name) }}',
            studentId: '{{ addslashes($session->student_id_code) }}',
            ipAddress: '{{ request()->ip() }}',
            maxStrikes: {{ $session->max_strikes }},
            strikesCount: {{ $session->strikes_count }},
            remainingSeconds: {{ $session->remainingSeconds() }},
            submitUrl: '{{ route("student.submit", $session->session_token) }}',
            resultUrl: '{{ route("student.result", $session->session_token) }}',
            watermarkOpacity: {{ \App\Models\SystemSetting::get("watermark_opacity", 0.12) }},
            enforceFullscreen: {{ $session->exam->enforce_fullscreen ? "true" : "false" }},
        });

        // Trigger fullscreen on initial user interaction if supported
        if ({{ $session->exam->enforce_fullscreen ? 'true' : 'false' }}) {
            window.addEventListener('click', () => {
                if (!document.fullscreenElement) {
                    window.examEngine.requestFullscreen();
                }
            }, { once: true });
        }
    });
</script>
@endpush
