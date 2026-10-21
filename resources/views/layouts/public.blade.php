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
<html lang="en" class="h-full bg-slate-50 text-slate-900 scroll-smooth">
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

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind Play CDN & Compiled Assets -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
                    },
                    boxShadow: {
                        'soft': '0 2px 10px -2px rgba(15, 23, 42, 0.04), 0 1px 3px -1px rgba(15, 23, 42, 0.02)',
                        'elevation': '0 10px 30px -4px rgba(15, 23, 42, 0.08), 0 4px 10px -2px rgba(15, 23, 42, 0.03)',
                    }
                }
            }
        }
    </script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        code, pre { font-family: 'JetBrains Mono', monospace; }

        @keyframes fadeInSlideUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-up {
            animation: fadeInSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .delay-75 { animation-delay: 75ms; }
        .delay-150 { animation-delay: 150ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }

        .card-interactive {
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease;
        }
        .card-interactive:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px -6px rgba(15, 23, 42, 0.08), 0 4px 12px -2px rgba(15, 23, 42, 0.04);
            border-color: rgba(99, 102, 241, 0.35);
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-full flex flex-col bg-slate-50 text-slate-900 selection:bg-indigo-100 selection:text-indigo-900 antialiased">

    <!-- Top Sticky Frosted-Glass Navbar (Completely Stripped of Admin References) -->
    <header id="site-navbar" class="border-b border-slate-200/70 bg-white/80 backdrop-blur-md sticky top-0 z-40 transition-all duration-300 shadow-[0_1px_3px_rgba(0,0,0,0.02)]">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <!-- Student Portal Identity -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-sm shadow-indigo-600/30 group-hover:scale-105 group-hover:bg-indigo-700 transition duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                </div>
                <div>
                    <span class="font-bold text-sm tracking-tight text-slate-900 group-hover:text-indigo-600 transition">{{ $siteName }}</span>
                    <span class="block text-[10px] text-slate-500 font-medium tracking-wide uppercase">Official Examination Portal</span>
                </div>
            </a>

            <!-- Public Academic Navigation Links -->
            <nav class="hidden md:flex items-center gap-7 text-xs font-semibold text-slate-600">
                <a href="{{ route('home') }}" class="hover:text-slate-900 transition flex items-center gap-1.5 py-1">
                    Home
                </a>
                <a href="{{ route('home') }}#active-exams" class="hover:text-slate-900 transition flex items-center gap-1.5 py-1">
                    Exam Schedule
                </a>
                <a href="{{ route('public.page', 'rules') }}" class="hover:text-slate-900 transition flex items-center gap-1.5 py-1">
                    Honor Code & Integrity
                </a>
                <a href="{{ route('home') }}#candidate-support" class="hover:text-slate-900 transition flex items-center gap-1.5 py-1">
                    Candidate Inquiries
                </a>
            </nav>

            <!-- Student Action CTA -->
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}#active-exams" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-1.5 active:scale-95">
                    <span>Active Assessments</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Viewport -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Student Portal Public Footer (Zero Admin Links) -->
    <footer class="border-t border-slate-200/80 bg-white py-14 mt-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-10 border-b border-slate-100 text-xs">
                <!-- Col 1: Institutional Badge -->
                <div class="md:col-span-2 space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-700 flex items-center justify-center font-bold text-xs">PG</div>
                        <span class="font-bold text-slate-900 text-sm tracking-tight">{{ $siteName }}</span>
                    </div>
                    <p class="text-slate-500 leading-relaxed max-w-md">
                        National Examination and Assessment Portal. Empowered by browser-level client verification, HTML5 canvas pixel buffer question security, and forensic watermarking.
                    </p>
                    <div class="inline-flex items-center gap-2 text-[11px] text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-2.5 py-1 rounded-full font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Security Engine Active • ProctorGuard Telemetry Live
                    </div>
                </div>

                <!-- Col 2: Navigation Links -->
                <div class="space-y-2.5">
                    <p class="font-bold text-slate-900 uppercase tracking-wider text-[11px]">Examination Portal</p>
                    <ul class="space-y-2 text-slate-600">
                        <li><a href="{{ route('home') }}" class="hover:text-indigo-600 transition">Portal Homepage</a></li>
                        <li><a href="{{ route('home') }}#active-exams" class="hover:text-indigo-600 transition">Scheduled Examinations</a></li>
                        <li><a href="{{ route('public.page', 'rules') }}" class="hover:text-indigo-600 transition">Academic Honor Code</a></li>
                        <li><a href="{{ route('home') }}#candidate-support" class="hover:text-indigo-600 transition">Technical Accommodation</a></li>
                    </ul>
                </div>

                <!-- Col 3: Student Support -->
                <div class="space-y-2.5">
                    <p class="font-bold text-slate-900 uppercase tracking-wider text-[11px]">Integrity Protocols</p>
                    <ul class="space-y-2 text-slate-600">
                        <li><span class="text-slate-500">Zero-DOM Question Rendering</span></li>
                        <li><span class="text-slate-500">Forensic Pixel Watermarking</span></li>
                        <li><span class="text-slate-500">Automated Strike Disqualification</span></li>
                        <li><span class="text-slate-500">Instant Automated Scoring</span></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved. Academic examination portal.</p>
                <div class="flex items-center gap-6">
                    <a href="{{ route('public.page', 'rules') }}" class="hover:text-slate-900 transition">Rules of Conduct</a>
                    <a href="{{ route('home') }}#candidate-support" class="hover:text-slate-900 transition">Candidate Helpdesk</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Navbar scroll elevation listener -->
    <script>
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('site-navbar');
            if (window.scrollY > 20) {
                nav.classList.add('shadow-sm', 'bg-white/95');
                nav.classList.remove('bg-white/80');
            } else {
                nav.classList.remove('shadow-sm', 'bg-white/95');
                nav.classList.add('bg-white/80');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
