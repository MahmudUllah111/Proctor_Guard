@extends('layouts.student')

@section('title', 'Examination in Progress: ' . $session->exam->title)

@section('content')
<div id="exam-active-area" class="h-full flex flex-col bg-slate-50 overflow-hidden relative">

    <!-- Top Proctor Header HUD & Authoritative Timer (Sticky Frosted-Glass) -->
    <header class="h-16 border-b border-slate-200/70 bg-white/80 backdrop-blur-md px-4 sm:px-6 flex items-center justify-between flex-shrink-0 z-20 shadow-[0_1px_3px_rgba(0,0,0,0.02)]">
        <!-- Exam title & Candidate Info -->
        <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
            <div class="w-8 h-8 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold text-xs shadow-sm shadow-indigo-600/30 flex-shrink-0">
                PG
            </div>
            <div class="min-w-0">
                <h2 class="text-xs md:text-sm font-bold text-slate-900 leading-tight flex items-center gap-1.5 min-w-0">
                    <span class="truncate max-w-[110px] sm:max-w-xs md:max-w-none">{{ $session->exam->title }}</span>
                    <span class="flex-shrink-0 px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200/80 font-mono text-[10px] font-semibold">{{ $session->exam->course_code ?? 'EXAM' }}</span>
                </h2>
                <p class="hidden sm:block text-[11px] text-slate-600 truncate">
                    Candidate: <strong class="text-slate-900 font-semibold">{{ $session->student_name }}</strong> (<span class="font-mono text-slate-600">{{ $session->student_id_code }}</span>)
                </p>
            </div>
        </div>

        <!-- Center: Authoritative Server Timer (Amber pulse under 5 minutes) -->
        <div id="timer-badge-container" class="flex items-center gap-1.5 sm:gap-2.5 bg-white px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-xl border border-slate-200/80 shadow-[0_2px_8px_rgba(0,0,0,0.04)] transition-all duration-300 flex-shrink-0">
            <svg class="w-3.5 sm:w-4 h-3.5 sm:h-4 text-slate-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="hidden md:inline text-[10px] uppercase font-semibold text-slate-500 tracking-wider">Remaining:</span>
            <span id="timer-display" class="font-mono tabular-nums text-xs sm:text-sm md:text-base font-bold text-slate-900 tracking-wider">
                --:--:--
            </span>
        </div>

        <!-- Right: Security Strikes Indicator & Submit Button -->
        <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white border border-slate-200/80 shadow-[0_1px_3px_rgba(0,0,0,0.02)] text-xs">
                <span class="text-slate-600 text-[11px] font-medium">Strikes:</span>
                <span id="strikes-counter-badge" class="px-2 py-0.5 rounded-full bg-slate-100 border border-slate-200/80 text-slate-700 font-mono font-semibold text-xs">
                    {{ $session->strikes_count }} / {{ $session->max_strikes }}
                </span>
            </div>

            <button id="submit-exam-btn" type="button" class="px-3 sm:px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm hover:shadow-md active:scale-95 transition-all duration-200 whitespace-nowrap">
                <span class="hidden sm:inline">Submit Exam</span>
                <span class="sm:hidden">Submit</span>
            </button>
        </div>
    </header>

    <!-- Main Workspace (Center: Question Canvas & Options, Right: Question Palette) -->
    <div class="flex-1 flex overflow-hidden">

        <!-- Question Viewport -->
        <div class="flex-1 overflow-y-auto p-6 md:p-8 flex flex-col items-center">
            <div class="max-w-3xl w-full space-y-6">

                <!-- 1. Elevated Pure White Paper Card wrapping HTML5 Canvas Question Buffer -->
                <div class="p-8 md:p-10 rounded-2xl border border-slate-200/80 shadow-[0_2px_8px_rgba(0,0,0,0.04)] bg-white">
                    <div id="canvas-question-container" class="w-full">
                        <div class="p-8 text-center text-slate-400 text-xs">
                            Rendering question into secure canvas pixel buffer...
                        </div>
                    </div>
                </div>

                <!-- 2. Multiple Choice Options Card -->
                <div class="bg-white border border-slate-200/80 rounded-2xl p-6 md:p-8 shadow-[0_2px_8px_rgba(0,0,0,0.04)] space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-900">Select Answer Option:</span>
                        <span id="current-question-num" class="text-xs font-mono font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200/80 px-2.5 py-0.5 rounded-full">Question 01</span>
                    </div>

                    <!-- Dynamic options buttons injected by ProctorEngine -->
                    <div id="question-options-list" class="space-y-3">
                        <!-- Populated dynamically via ProctorEngine -->
                    </div>
                </div>

                <!-- 3. Bottom Step Navigation Controls -->
                <div class="flex items-center justify-between pt-2">
                    <button id="prev-question-btn" type="button" class="px-5 py-2.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200/80 shadow-[0_1px_3px_rgba(0,0,0,0.02)] transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed">
                        &larr; Previous Question
                    </button>
                    <button id="next-question-btn" type="button" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm hover:shadow-md active:scale-95 transition-all duration-200">
                        Next Question &rarr;
                    </button>
                </div>

            </div>
        </div>

        <!-- Right Side: Question Navigation Palette Sidebar -->
        <aside class="w-72 border-l border-slate-200/80 bg-white p-6 flex flex-col justify-between hidden lg:flex shadow-[-1px_0_3px_rgba(0,0,0,0.02)]">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 mb-4 flex items-center justify-between">
                    <span>Question Palette</span>
                    <span class="text-[10px] text-slate-500 font-mono font-medium">Randomized</span>
                </h3>

                <!-- Grid of question numbers -->
                <div id="question-palette-grid" class="grid grid-cols-5 gap-2">
                    <!-- Populated dynamically via ProctorEngine -->
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 space-y-2 text-[11px] text-slate-600">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded bg-emerald-600"></span>
                        <span>Answered Question</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded bg-indigo-600"></span>
                        <span>Current Active Question</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded bg-slate-100 border border-slate-200/80"></span>
                        <span>Unanswered Question</span>
                    </div>
                </div>
            </div>

            <!-- Anti-Cheat Status Badge -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 text-center">
                <div class="flex items-center justify-center gap-1.5 text-xs font-bold text-slate-800 mb-0.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
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
            watermarkOpacity: {{ \App\Models\SystemSetting::get("watermark_opacity", 0.05) }},
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
