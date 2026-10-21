<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-900 select-none">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Proctored Examination') · {{ $siteName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    @include('layouts.partials.assets')
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        code, pre { font-family: 'JetBrains Mono', monospace; }
        /* Disable print */
        @media print {
            body { display: none !important; }
        }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col overflow-hidden bg-slate-50 text-slate-900 antialiased">

    <!-- Anti-Cheat Warning Toast / Alert Bar -->
    <div id="proctor-warning-alert" class="fixed top-5 left-1/2 -translate-x-1/2 z-50 hidden max-w-lg w-full px-4 animate-in fade-in slide-in-from-top-4 duration-200">
        <div class="card p-3.5 shadow-xl flex items-center justify-between gap-3 border-amber-200 ring-1 ring-amber-500/10">
            <div class="flex items-center gap-3">
                <span class="p-2 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg">
                    <x-icon name="triangle-alert" class="w-5 h-5" />
                </span>
                <div>
                    <p class="font-bold text-xs uppercase tracking-wider text-amber-800">Integrity Violation Detected</p>
                    <p id="proctor-warning-msg" class="text-xs font-medium text-slate-600">Strike recorded. Return immediately to the exam window.</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('proctor-warning-alert').classList.add('hidden')" class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition">
                <x-icon name="x" class="w-4 h-4" />
            </button>
        </div>
    </div>

    <!-- Fullscreen Recovery Modal -->
    <div id="fullscreen-recovery-modal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="card p-8 max-w-md w-full text-center shadow-2xl">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center mx-auto mb-4">
                <x-icon name="shield-alert" class="w-6 h-6" />
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2">Fullscreen Mode Required</h3>
            <p class="text-xs text-slate-600 mb-6 leading-relaxed">Exiting fullscreen mode violates examination security protocol and records integrity strikes. Re-enter fullscreen to continue your test immediately.</p>
            <button type="button" onclick="if(window.examEngine){ window.examEngine.requestFullscreen(); document.getElementById('fullscreen-recovery-modal').classList.add('hidden'); }" class="w-full btn-primary h-9">
                Re-enter Fullscreen Mode
            </button>
        </div>
    </div>

    <!-- Disqualification Lockout Overlay (Triggered upon exceeding maximum strikes) -->
    <div id="proctor-lockout-screen" class="fixed inset-0 z-[9999999] hidden bg-slate-900/60 backdrop-blur-md flex flex-col items-center justify-center p-6 text-center">
        <div class="card p-8 md:p-10 max-w-lg w-full text-center shadow-2xl space-y-5 border-rose-200">
            <div class="w-14 h-14 rounded-xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center mx-auto shadow-sm">
                <x-icon name="shield-alert" class="w-7 h-7 text-rose-600" />
            </div>
            <div>
                <span class="pill-rose uppercase tracking-wider mb-2">Security Disqualification</span>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-2">EXAMINATION DISQUALIFIED</h1>
                <p class="text-xs text-slate-600 mt-2 leading-relaxed">Your examination session has been permanently terminated due to repeated anti-cheat integrity violations. In accordance with university policy, your final score is recorded at 0.00%.</p>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-left text-xs text-slate-700 space-y-1">
                <p class="font-bold text-slate-900 uppercase text-[10px] tracking-wider text-slate-500">Termination Audit Log</p>
                <p><strong class="text-slate-900">REASON:</strong> <span id="lockout-reason-text" class="text-rose-700 font-medium">Multiple infraction strikes exceeded administrative policy limit.</span></p>
            </div>
            <p class="text-xs text-slate-500">Redirecting to candidate audit summary in a few seconds...</p>
        </div>
    </div>

    <!-- Main View Content -->
    <div class="flex-1 flex flex-col overflow-hidden relative">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
