@extends('layouts.admin')

@section('title', 'Page Builder CMS')
@section('page_title', 'Widget-Based Page Builder')
@section('page_subtitle', 'Manage landing pages, custom URLs, and modular layout widgets without code touch')

@section('topbar_actions')
    <a href="{{ route('admin.pages.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/20 transition flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create New Page
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="font-bold text-sm text-white">Published & Draft CMS Pages</h3>
            <span class="text-xs text-slate-400">Total: {{ $pages->count() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-950/60 text-slate-400 border-b border-slate-800">
                        <th class="py-3.5 px-6 font-semibold">Page Title</th>
                        <th class="py-3.5 px-4 font-semibold">Slug / URL</th>
                        <th class="py-3.5 px-4 font-semibold">Widgets Count</th>
                        <th class="py-3.5 px-4 font-semibold">Status</th>
                        <th class="py-3.5 px-6 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($pages as $page)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-4 px-6 font-semibold text-white">
                                <div class="flex items-center gap-2">
                                    @if($page->is_home)
                                        <span class="px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-400 font-bold text-[10px]">HOMEPAGE</span>
                                    @endif
                                    <span class="text-white">{{ $page->title }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 font-mono text-indigo-300">
                                @if($page->is_home)
                                    /
                                @else
                                    /page/{{ $page->slug }}
                                @endif
                            </td>
                            <td class="py-4 px-4 text-slate-400">
                                {{ count($page->content_widgets ?? []) }} Widgets
                            </td>
                            <td class="py-4 px-4">
                                @if($page->status === 'published')
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400 font-bold text-[10px]">Published</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-slate-800 text-slate-400 font-bold text-[10px]">Draft</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ $page->is_home ? route('home') : route('public.page', $page->slug) }}" target="_blank" class="text-slate-400 hover:text-white">View Live</a>
                                <span class="text-slate-600">•</span>
                                <a href="{{ route('admin.pages.edit', $page) }}" class="text-indigo-400 hover:text-indigo-300 font-semibold">Edit Widgets</a>
                                @if(!$page->is_home)
                                    <span class="text-slate-600">•</span>
                                    <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="inline" onsubmit="return confirm('Delete this page?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-400 hover:text-rose-300">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500">No CMS pages found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
