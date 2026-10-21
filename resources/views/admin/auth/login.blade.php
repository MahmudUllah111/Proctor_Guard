<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-900 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In · {{ $siteName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    @include('layouts.partials.assets')
    <style>
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
                <x-icon name="shield-check" class="w-6 h-6" />
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $siteName }}</h1>
            <p class="text-xs text-slate-500 mt-1.5 font-normal">Authorized proctor & administrator sign in</p>
        </div>

        <!-- Login Card -->
        <div class="card p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] backdrop-blur-sm">
            <!-- Errors -->
            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2.5">
                    <x-icon name="triangle-alert" class="w-4 h-4 text-rose-600 shrink-0" />
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
                    <input type="email" id="email" name="email" value="{{ old('email', 'admin@examcms.test') }}" required autofocus
                        class="input">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-700">Access Key</label>
                    </div>
                    <input type="password" id="password" name="password" value="AdminSecret2026!" required
                        class="input">
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 text-slate-600 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                        <span class="text-xs">Remember session</span>
                    </label>
                    <a href="{{ route('home') }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium transition flex items-center gap-1">
                        <span>Candidate Portal</span>
                        <x-icon name="arrow-right" class="w-3 h-3" />
                    </a>
                </div>

                <button type="submit" class="w-full mt-2 h-9 btn-primary shadow-sm">
                    Sign In to Console
                </button>
            </form>

            <!-- Pre-configured Credentials Box -->
            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-[11px] text-slate-400 font-medium mb-2">Default Development Credentials</p>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200/80 text-[11px] font-mono text-slate-600 shadow-xs">
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
