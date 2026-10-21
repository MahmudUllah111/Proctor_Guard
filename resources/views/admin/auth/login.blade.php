<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Sign In - ProctorGuard CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="h-full flex items-center justify-center p-6 bg-slate-950 relative overflow-hidden">

    <!-- Ambient background glow -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full relative z-10">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-indigo-600 flex items-center justify-center text-white mx-auto mb-4 shadow-xl shadow-indigo-600/30">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">ProctorGuard CMS</h1>
            <p class="text-xs text-slate-400 mt-1">Administrator & Proctor Command Console</p>
        </div>

        <!-- Login Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl">
            <!-- Errors -->
            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-950/60 border border-red-700/60 text-red-200 text-xs">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('info'))
                <div class="mb-6 p-4 rounded-xl bg-slate-800 border border-slate-700 text-slate-300 text-xs">
                    {{ session('info') }}
                </div>
            @endif

            <form action="{{ route('admin.login') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 mb-2">Administrator Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', 'admin@examcms.test') }}" required autofocus
                        class="w-full px-4 py-3 bg-slate-800/80 border border-slate-700 rounded-xl text-white text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-300 mb-2">Password</label>
                    <input type="password" id="password" name="password" value="AdminSecret2026!" required
                        class="w-full px-4 py-3 bg-slate-800/80 border border-slate-700 rounded-xl text-white text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-slate-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded bg-slate-800 border-slate-700 text-indigo-600 focus:ring-indigo-500">
                        <span>Remember session</span>
                    </label>
                    <a href="{{ route('home') }}" class="text-indigo-400 hover:text-indigo-300">Back to Website</a>
                </div>

                <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition">
                    Sign In to Admin Console
                </button>
            </form>

            <!-- Default credentials hint box -->
            <div class="mt-6 pt-6 border-t border-slate-800/80 text-center">
                <p class="text-[11px] text-slate-400 mb-2">Pre-configured System Credentials:</p>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-800/60 border border-slate-700 text-[11px] font-mono text-indigo-300">
                    <span>admin@examcms.test</span>
                    <span class="text-slate-600">•</span>
                    <span>AdminSecret2026!</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
