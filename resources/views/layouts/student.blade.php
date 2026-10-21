<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-900 select-none">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Proctored Examination') - ProctorGuard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
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
        <div class="bg-white text-slate-900 px-5 py-3.5 rounded-2xl shadow-xl flex items-center justify-between gap-3 border border-amber-200 ring-1 ring-amber-500/10">
            <div class="flex items-center gap-3">
                <span class="p-2 bg-amber-50 text-amber-700 border border-amber-200 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
                <div>
                    <p class="font-bold text-xs uppercase tracking-wider text-amber-800">Integrity Violation Detected</p>
                    <p id="proctor-warning-msg" class="text-xs font-medium text-slate-600">Strike recorded. Return immediately to the exam window.</p>
                </div>
            </div>
            <button onclick="document.getElementById('proctor-warning-alert').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 text-lg">&times;</button>
        </div>
    </div>

    <!-- Fullscreen Recovery Modal -->
    <div id="fullscreen-recovery-modal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-8 max-w-md w-full text-center shadow-2xl">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-2">Fullscreen Mode Required</h3>
            <p class="text-xs text-slate-600 mb-6 leading-relaxed">Exiting fullscreen mode violates examination security protocol and records integrity strikes. Re-enter fullscreen to continue your test immediately.</p>
            <button onclick="if(window.examEngine){ window.examEngine.requestFullscreen(); document.getElementById('fullscreen-recovery-modal').classList.add('hidden'); }" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm transition">Re-enter Fullscreen Mode</button>
        </div>
    </div>

    <!-- Disqualification Lockout Overlay (Triggered upon exceeding maximum strikes) -->
    <div id="proctor-lockout-screen" class="fixed inset-0 z-[9999999] hidden bg-slate-900/60 backdrop-blur-md flex flex-col items-center justify-center p-6 text-center">
        <div class="bg-white border border-rose-200 rounded-3xl p-8 md:p-10 max-w-lg w-full text-center shadow-2xl space-y-5">
            <div class="w-16 h-16 rounded-2xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center mx-auto shadow-sm">
                <svg class="w-8 h-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 uppercase tracking-wider mb-2">Security Disqualification</span>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">EXAMINATION DISQUALIFIED</h1>
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
