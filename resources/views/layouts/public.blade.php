@php
    $siteName = \App\Models\SystemSetting::get('site_name', 'ProctorGuard Exam CMS');
    $defaultTitle = \App\Models\SystemSetting::get('seo_title_default', 'ProctorGuard - Enterprise Quiz & Proctoring CMS');
    $defaultDesc = \App\Models\SystemSetting::get('seo_description_default', 'Zero-leakage, proctored online examinations with canvas pixel buffer rendering and forensic watermarking.');
    $defaultKeywords = \App\Models\SystemSetting::get('seo_keywords_default', 'proctoring, online exam, anti-cheat, canvas proctor, quiz cms');
    $ogImage = !empty($page->og_image) ? $page->og_image : \App\Models\SystemSetting::get('seo_og_image', '');
    $robots = \App\Models\SystemSetting::get('seo_robots', 'index, follow');

    $metaTitle = !empty($page->seo_title) ? $page->seo_title : (!empty($page->title) ? $page->title . ' - ' . $siteName : $defaultTitle);
    $metaDesc = !empty($page->seo_description) ? $page->seo_description : $defaultDesc;
    $metaKeywords = !empty($page->seo_keywords) ? $page->seo_keywords : $defaultKeywords;
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Primary Meta SEO Tags -->
    <title>{{ $metaTitle }}</title>
    <meta name="title" content="{{ $metaTitle }}">
    <meta name="description" content="{{ $metaDesc }}">
    <meta name="keywords" content="{{ $metaKeywords }}">
    <meta name="robots" content="{{ $robots }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    @if(!empty($ogImage))
        <meta property="og:image" content="{{ $ogImage }}">
    @endif

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $metaTitle }}">
    <meta property="twitter:description" content="{{ $metaDesc }}">
    @if(!empty($ogImage))
        <meta property="twitter:image" content="{{ $ogImage }}">
    @endif

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        code, pre { font-family: 'JetBrains Mono', monospace; }
    </style>
    @stack('styles')
</head>
<body class="min-h-full flex flex-col bg-slate-950 text-slate-100 selection:bg-indigo-500 selection:text-white">

    <!-- Top Navigation Header -->
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-600/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                    <span class="font-extrabold text-base text-white tracking-tight">{{ $siteName }}</span>
                    <span class="block text-[10px] text-indigo-400 font-semibold uppercase tracking-wider">Secure Assessment Platform</span>
                </div>
            </a>

            <nav class="flex items-center gap-6 text-xs font-semibold">
                <a href="{{ route('home') }}" class="text-slate-300 hover:text-white transition">Home</a>
                <a href="{{ route('public.page', 'rules') }}" class="text-slate-300 hover:text-white transition">Exam Rules & Integrity</a>
                <a href="{{ route('admin.login') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Proctor / Admin CMS
                </a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 bg-slate-900/60 py-10 mt-20">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6 text-xs text-slate-500">
            <div class="flex items-center gap-3">
                <div class="w-6 h-6 rounded-lg bg-indigo-600/30 text-indigo-400 flex items-center justify-center font-bold text-xs">PG</div>
                <p>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved. Zero-leakage anti-cheat proctoring engine.</p>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="hover:text-slate-300">Home</a>
                <a href="{{ route('public.page', 'rules') }}" class="hover:text-slate-300">Integrity Code</a>
                <a href="{{ route('admin.login') }}" class="hover:text-slate-300">Staff Portal</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
