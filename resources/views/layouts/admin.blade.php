<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-800">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') · {{ $siteName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    @include('layouts.partials.assets')
    @stack('styles')
</head>
<body class="h-full flex overflow-hidden bg-slate-50 text-slate-800 antialiased">
@php
    $nav = [
        ['Workspace', [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'icon' => 'layout-dashboard'],
            ['label' => 'Exam Builder', 'route' => 'admin.exams.index', 'match' => 'admin.exams*', 'icon' => 'clipboard-list'],
            ['label' => 'Live Proctoring', 'route' => 'admin.proctoring.index', 'match' => 'admin.proctoring*', 'icon' => 'activity', 'live' => $liveSessionCount],
            ['label' => 'Media Library', 'route' => 'admin.media.index', 'match' => 'admin.media*', 'icon' => 'image'],
            ['label' => 'Page Builder', 'route' => 'admin.pages.index', 'match' => 'admin.pages*', 'icon' => 'file-text'],
            ['label' => 'Leads & Inquiries', 'route' => 'admin.forms.index', 'match' => 'admin.forms*', 'icon' => 'inbox', 'count' => $newLeadCount],
        ]],
        ['System', [
            ['label' => 'SMTP & Global SEO', 'route' => 'admin.settings.index', 'match' => 'admin.settings*', 'icon' => 'settings'],
            ['label' => 'Email Templates', 'route' => 'admin.templates.index', 'match' => 'admin.templates*', 'icon' => 'mail'],
        ]],
    ];
@endphp

    <aside class="w-60 bg-white border-r border-slate-200 flex flex-col flex-shrink-0 z-30">
        <div class="h-16 px-5 flex items-center gap-2.5 border-b border-slate-100">
            <div class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center text-white shadow-sm">
                <x-icon name="shield-check" class="w-4 h-4" />
            </div>
            <span class="font-bold text-[13px] tracking-tight text-slate-900 truncate">{{ $siteName }}</span>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6 text-[13px]">
            @foreach($nav as [$section, $items])
                <div>
                    <p class="px-2.5 mb-1.5 text-[10.5px] font-semibold uppercase tracking-[0.06em] text-slate-400">{{ $section }}</p>
                    <div class="space-y-0.5">
                        @foreach($items as $item)
                            @php $active = request()->routeIs($item['match']); @endphp
                            <a href="{{ route($item['route']) }}" @if($active) aria-current="page" @endif
                               class="flex items-center gap-2.5 h-8 px-2.5 rounded-lg transition {{ $active ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                <x-icon :name="$item['icon']" class="w-4 h-4 {{ $active ? 'text-indigo-600' : 'text-slate-400' }}" />
                                <span class="flex-1 truncate">{{ $item['label'] }}</span>
                                @if(!empty($item['live']))
                                    <span class="inline-flex items-center gap-1 text-[10.5px] font-semibold text-emerald-700 num" title="{{ $item['live'] }} active sessions">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>{{ $item['live'] }}
                                    </span>
                                @elseif(!empty($item['count']))
                                    <span class="min-w-5 h-5 px-1.5 rounded-md bg-indigo-50 text-indigo-700 text-[10.5px] font-semibold num inline-flex items-center justify-center" title="{{ $item['count'] }} new">{{ $item['count'] }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        <div class="p-3 border-t border-slate-100">
            <div class="flex items-center gap-2.5 px-2 py-1.5">
                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-semibold text-xs flex items-center justify-center flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email }}</p>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <x-icon-button type="submit" icon="log-out" label="Sign out" tone="danger" />
                </form>
            </div>
        </div>
    </aside>

    <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <header class="h-16 px-8 flex items-center justify-between gap-6 flex-shrink-0 bg-white border-b border-slate-200 z-20">
            <div class="min-w-0">
                <h1 class="text-[15px] font-semibold text-slate-900 tracking-tight truncate leading-5">@yield('page_title', 'Dashboard')</h1>
                <p class="text-xs text-slate-500 truncate leading-4 mt-0.5">@yield('page_subtitle')</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn-secondary">
                    <x-icon name="external-link" class="w-3.5 h-3.5 text-slate-400" />
                    View site
                </a>
                @yield('topbar_actions')
            </div>
        </header>

        <div class="flex-1 overflow-y-auto">
            <div class="px-8 py-6 space-y-4">
                @foreach(['success' => ['bg-emerald-50 border-emerald-200 text-emerald-800', 'circle-check'], 'warning' => ['bg-amber-50 border-amber-200 text-amber-800', 'triangle-alert']] as $key => [$tone, $icon])
                    @if(session($key))
                        <div role="status" class="flex items-center justify-between gap-3 px-4 py-2.5 rounded-lg border text-xs font-medium {{ $tone }}">
                            <span class="flex items-center gap-2"><x-icon :name="$icon" class="w-4 h-4" />{{ session($key) }}</span>
                            <button type="button" onclick="this.parentElement.remove()" aria-label="Dismiss" class="opacity-60 hover:opacity-100"><x-icon name="x" class="w-4 h-4" /></button>
                        </div>
                    @endif
                @endforeach

                @if($errors->any())
                    <div role="alert" class="px-4 py-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                        <p class="flex items-center gap-2 font-semibold mb-1"><x-icon name="triangle-alert" class="w-4 h-4" />Please correct the errors below</p>
                        <ul class="list-disc pl-10 space-y-0.5 text-rose-700">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </main>

    <script src="{{ asset('js/media-modal.js') }}"></script>
    @stack('scripts')
</body>
</html>
