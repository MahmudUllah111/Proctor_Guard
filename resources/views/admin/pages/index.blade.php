@extends('layouts.admin')

@section('title', 'Page Builder CMS')
@section('page_title', 'Widget-Based Page Builder')
@section('page_subtitle', 'Manage landing pages, custom URLs, and modular layout widgets without code touch')

@section('topbar_actions')
    <a href="{{ route('admin.pages.create') }}" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create New Page
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-900">Published & Draft CMS Pages</h3>
            <span class="text-xs text-slate-500">Total: {{ $pages->count() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <th class="py-3.5 px-6 font-semibold">Page Title</th>
                        <th class="py-3.5 px-4 font-semibold">Slug / URL</th>
                        <th class="py-3.5 px-4 font-semibold">Widgets Count</th>
                        <th class="py-3.5 px-4 font-semibold">Status</th>
                        <th class="py-3.5 px-6 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pages as $page)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6 font-semibold text-slate-900">
                                <div class="flex items-center gap-2">
                                    @if($page->is_home)
                                        <span class="px-2 py-0.5 rounded bg-indigo-50 border border-indigo-200 text-indigo-700 font-bold text-[10px]">HOMEPAGE</span>
                                    @endif
                                    <span class="text-slate-900">{{ $page->title }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 font-mono text-indigo-700">
                                @if($page->is_home)
                                    /
                                @else
                                    /page/{{ $page->slug }}
                                @endif
                            </td>
                            <td class="py-4 px-4 text-slate-600">
                                {{ count($page->content_widgets ?? []) }} Widgets
                            </td>
                            <td class="py-4 px-4">
                                @if($page->status === 'published')
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold text-[10px]">Published</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200 font-semibold text-[10px]">Draft</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right space-x-2 font-medium">
                                <a href="{{ $page->is_home ? route('home') : route('public.page', $page->slug) }}" target="_blank" class="text-slate-500 hover:text-slate-800">View Live</a>
                                <span class="text-slate-300">•</span>
                                <a href="{{ route('admin.pages.edit', $page) }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">Edit Widgets</a>
                                @if(!$page->is_home)
                                    <span class="text-slate-300">•</span>
                                    <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="inline" onsubmit="return confirm('Delete this page?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-700">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">No CMS pages found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
