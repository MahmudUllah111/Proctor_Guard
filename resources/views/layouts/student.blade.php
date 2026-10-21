<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100 select-none">
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
<body class="h-full flex flex-col overflow-hidden bg-slate-950 text-slate-100 antialiased">

    <!-- Anti-Cheat Warning Toast / Alert Bar -->
    <div id="proctor-warning-alert" class="fixed top-5 left-1/2 -translate-x-1/2 z-50 hidden max-w-lg w-full px-4 animate-in fade-in slide-in-from-top-4 duration-200">
        <div class="bg-rose-600 text-white px-5 py-3.5 rounded-2xl shadow-2xl flex items-center justify-between gap-3 border border-rose-500">
            <div class="flex items-center gap-3">
                <span class="p-2 bg-rose-700 rounded-xl">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
                <div>
                    <p class="font-bold text-xs uppercase tracking-wider text-rose-200">Integrity Violation Detected</p>
                    <p id="proctor-warning-msg" class="text-xs font-medium text-white">Strike recorded. Return immediately to the exam window.</p>
                </div>
            </div>
            <button onclick="document.getElementById('proctor-warning-alert').classList.add('hidden')" class="text-rose-200 hover:text-white">&times;</button>
        </div>
    </div>

    <!-- Fullscreen Recovery Modal -->
    <div id="fullscreen-recovery-modal" class="fixed inset-0 z-50 hidden bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8 max-w-md w-full text-center shadow-2xl">
            <div class="w-16 h-16 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center mx-auto mb-4 border border-amber-500/30">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Fullscreen Exited</h3>
            <p class="text-xs text-slate-400 mb-6 leading-relaxed">Exiting fullscreen mode violates examination protocol and accrues security strikes. Re-enter fullscreen to continue your test.</p>
            <button onclick="if(window.examEngine){ window.examEngine.requestFullscreen(); document.getElementById('fullscreen-recovery-modal').classList.add('hidden'); }" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition">Re-enter Fullscreen Mode</button>
        </div>
    </div>

    <!-- Red Security Lockout Overlay (Triggered upon exceeding maximum strikes) -->
    <div id="proctor-lockout-screen" class="fixed inset-0 z-[9999999] hidden bg-rose-950 flex flex-col items-center justify-center p-6 text-center">
        <div class="w-24 h-24 rounded-3xl bg-rose-900/60 border-2 border-rose-500 text-white flex items-center justify-center mb-6 shadow-2xl animate-pulse">
            <svg class="w-12 h-12 text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
        </div>
        <h1 class="text-3xl font-extrabold text-white mb-3 tracking-tight">EXAMINATION DISQUALIFIED</h1>
        <p class="text-rose-200 text-sm max-w-lg mb-6 leading-relaxed">Your examination session has been permanently terminated due to repeated anti-cheat integrity violations. Your score is automatically locked at 0.00%.</p>
        <div class="p-4 rounded-xl bg-rose-900/40 border border-rose-700 text-xs text-rose-300 max-w-md w-full mb-8 font-mono">
            <p><strong>REASON:</strong> <span id="lockout-reason-text">Multiple infraction strikes exceeded administrative policy limit.</span></p>
        </div>
        <p class="text-xs text-rose-400">Redirecting to candidate audit summary in a few seconds...</p>
    </div>

    <!-- Main View Content -->
    <div class="flex-1 flex flex-col overflow-hidden relative">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
