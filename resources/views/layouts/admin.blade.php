<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin CMS') - ProctorGuard Enterprise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        code, pre { font-family: 'JetBrains Mono', Consolas, monospace; }
    </style>
    @stack('styles')
</head>
<body class="h-full flex overflow-hidden bg-slate-50 text-slate-800 antialiased">

    <!-- Admin Sidebar (Clean Light SaaS) -->
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between flex-shrink-0 z-30">
        <div>
            <!-- Branding Header -->
            <div class="h-16 px-6 flex items-center gap-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                    <h1 class="font-extrabold text-sm tracking-tight text-slate-900 leading-tight">ProctorGuard</h1>
                    <p class="text-[10px] text-slate-500 font-medium">Enterprise Exam CMS</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="p-3 space-y-1 text-xs font-medium">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold border border-indigo-100/80 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>

                <!-- Exam Builder -->
                <a href="{{ route('admin.exams.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.exams*') ? 'bg-indigo-50 text-indigo-700 font-semibold border border-indigo-100/80 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.exams*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Exam Builder
                </a>

                <!-- Live Proctoring Ledger -->
                <a href="{{ route('admin.proctoring.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.proctoring*') ? 'bg-rose-50 text-rose-800 font-semibold border border-rose-200/80 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                    <span class="flex items-center gap-3">
                        <svg class="w-4 h-4 {{ request()->routeIs('admin.proctoring*') ? 'text-rose-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Live Proctoring
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">
                        Live
                    </span>
                </a>

                <!-- Media Library -->
                <a href="{{ route('admin.media.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.media*') ? 'bg-indigo-50 text-indigo-700 font-semibold border border-indigo-100/80 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.media*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Media Library
                </a>

                <!-- Widget Page Builder -->
                <a href="{{ route('admin.pages.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.pages*') ? 'bg-indigo-50 text-indigo-700 font-semibold border border-indigo-100/80 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.pages*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Page Builder CMS
                </a>

                <!-- Forms & Leads -->
                <a href="{{ route('admin.forms.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.forms*') ? 'bg-indigo-50 text-indigo-700 font-semibold border border-indigo-100/80 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.forms*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Leads & Inquiries
                </a>

                <div class="pt-4 pb-1">
                    <p class="px-3.5 text-[10px] font-bold uppercase text-slate-400 tracking-wider">System Setup</p>
                </div>

                <!-- SMTP & Global SEO Settings -->
                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.settings*') ? 'bg-indigo-50 text-indigo-700 font-semibold border border-indigo-100/80 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.settings*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    SMTP & Global SEO
                </a>

                <!-- Email Templates -->
                <a href="{{ route('admin.templates.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.templates*') ? 'bg-indigo-50 text-indigo-700 font-semibold border border-indigo-100/80 shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('admin.templates*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"/></svg>
                    Email Templates
                </a>
            </nav>
        </div>

        <!-- Admin Profile & Logout -->
        <div class="p-3 border-t border-slate-100">
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center justify-between">
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-semibold text-slate-900 truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                        <p class="text-[10px] text-slate-500 truncate">{{ auth()->user()->email ?? 'admin@examcms.test' }}</p>
                    </div>
                </div>

                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Logout" class="text-slate-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-slate-200/60 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Workspace -->
    <main class="flex-1 flex flex-col overflow-hidden bg-slate-50">
        <!-- Top App Bar -->
        <header class="h-16 border-b border-slate-200 px-8 flex items-center justify-between flex-shrink-0 bg-white shadow-[0_1px_2px_rgba(0,0,0,0.03)] z-20">
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight">@yield('page_title', 'Dashboard')</h2>
                <p class="text-xs text-slate-500">@yield('page_subtitle', 'System Administration & Integrity Monitor')</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" target="_blank" class="text-xs text-slate-600 hover:text-slate-900 flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 transition shadow-sm font-medium">
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    View Live Website
                </a>
                @yield('topbar_actions')
            </div>
        </header>

        <!-- Flash Notifications -->
        @if(session('success'))
            <div class="mx-8 mt-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2 font-medium">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-base leading-none">&times;</button>
            </div>
        @endif

        @if(session('warning'))
            <div class="mx-8 mt-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2 font-medium">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ session('warning') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-amber-500 hover:text-amber-800 text-base leading-none">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="mx-8 mt-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1 shadow-sm">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>Please correct the errors below:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 pl-4 text-rose-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Scrollable Page Content -->
        <div class="flex-1 overflow-y-auto p-8">
            @yield('content')
        </div>
    </main>

    <!-- Global WordPress-Style Media Library Modal Script -->
    <script src="{{ asset('js/media-modal.js') }}"></script>
    @stack('scripts')
</body>
</html>
