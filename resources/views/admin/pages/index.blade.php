@extends('layouts.admin')

@section('title', 'Page Builder')
@section('page_title', 'Widget-Based Page Builder')
@section('page_subtitle', 'Manage landing pages, custom URLs, and modular layout widgets without code changes')

@section('topbar_actions')
    <a href="{{ route('admin.pages.create') }}" class="btn-primary">
        <x-icon name="plus" class="w-3.5 h-3.5" />
        <span>Create page</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <div class="card overflow-hidden">
        <div class="card-header">
            <div>
                <h3 class="card-title">Published & Draft CMS Pages</h3>
                <p class="card-subtitle">Manage public routes and dynamic landing page compositions</p>
            </div>
            <span class="text-xs text-slate-500 font-medium">Total: <span class="num text-slate-900 font-semibold">{{ $pages->count() }}</span></span>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Page Title</th>
                        <th>Slug / Route</th>
                        <th>Widgets</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $page)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    @if($page->is_home)
                                        <span class="pill-indigo">HOMEPAGE</span>
                                    @endif
                                    <span class="font-semibold text-slate-900">{{ $page->title }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="token-chip">
                                    @if($page->is_home)
                                        /
                                    @else
                                        /page/{{ $page->slug }}
                                    @endif
                                </span>
                            </td>
                            <td class="num text-slate-600">
                                {{ count($page->content_widgets ?? []) }} widgets
                            </td>
                            <td>
                                @if($page->status === 'published')
                                    <span class="pill-emerald">Published</span>
                                @else
                                    <span class="pill-slate">Draft</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="icon-group">
                                    <x-icon-button :href="$page->is_home ? route('home') : route('public.page', $page->slug)" icon="external-link" label="View live" target="_blank" />
                                    <x-icon-button :href="route('admin.pages.edit', $page)" icon="pencil" label="Edit widgets" tone="primary" />
                                    @if(!$page->is_home)
                                        <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="inline" onsubmit="return confirm('Delete this page?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-icon-button type="submit" icon="trash-2" label="Delete page" tone="danger" />
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400">No CMS pages found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
