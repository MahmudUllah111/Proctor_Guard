<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-900 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - ProctorGuard Administration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .grid-pattern {
            background-image: radial-gradient(rgba(148, 163, 184, 0.18) 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-6 bg-slate-50 grid-pattern relative overflow-hidden select-none">

    <!-- Ambient background glow (subtle, light mode) -->
    <div class="absolute inset-0 pointer-events-none flex items-center justify-center overflow-hidden">
        <div class="w-[500px] h-[500px] bg-indigo-50/70 rounded-full blur-3xl -translate-y-12"></div>
    </div>

    <div class="max-w-[420px] w-full relative z-10 transition-all duration-300">
        <!-- Institutional Emblem & Heading -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-indigo-600 text-white shadow-[0_4px_14px_rgba(79,70,229,0.25)] mb-4 ring-4 ring-indigo-50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">ProctorGuard Console</h1>
            <p class="text-xs text-slate-500 mt-1.5 font-normal">Authorized proctor & administrator sign in</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white border border-slate-200/90 rounded-3xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] backdrop-blur-sm">
            <!-- Errors -->
            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-5 p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-xs font-medium">
                    {{ session('info') }}
                </div>
            @endif

            <form action="{{ route('admin.login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">Staff Email Address</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" value="{{ old('email', 'admin@examcms.test') }}" required autofocus
                            class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-900 text-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:bg-white transition-all">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-700">Access Key</label>
                    </div>
                    <div class="relative">
                        <input type="password" id="password" name="password" value="AdminSecret2026!" required
                            class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-slate-900 text-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:bg-white transition-all">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 text-slate-600 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                        <span class="text-xs">Remember session</span>
                    </label>
                    <a href="{{ route('home') }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium transition">
                        Candidate Portal &rarr;
                    </a>
                </div>

                <button type="submit" class="w-full mt-2 py-3 bg-indigo-600 hover:bg-indigo-700 active:scale-[0.99] text-white font-semibold text-xs rounded-xl shadow-[0_2px_8px_rgba(79,70,229,0.25)] hover:shadow-[0_4px_12px_rgba(79,70,229,0.35)] transition-all">
                    Sign In to Console
                </button>
            </form>

            <!-- Pre-configured Credentials Box -->
            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-[11px] text-slate-400 font-medium mb-2">Default Development Credentials</p>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200/80 text-[11px] font-mono text-slate-600 shadow-sm">
                    <span class="font-medium text-slate-800">admin@examcms.test</span>
                    <span class="text-slate-300">•</span>
                    <span class="font-medium text-slate-800">AdminSecret2026!</span>
                </div>
            </div>
        </div>

        <!-- Footer Notice -->
        <p class="text-center text-[11px] text-slate-400 mt-6">
            Institutional security ledger monitored. Unauthorized access prohibited.
        </p>
    </div>
</body>
</html>
