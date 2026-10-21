@extends('layouts.admin')

@section('title', 'Create Page')
@section('page_title', 'Create New Page')
@section('page_subtitle', 'Setup URL slug, page title, and search engine parameters')

@section('content')
<div class="max-w-3xl mx-auto">
    <form action="{{ route('admin.pages.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-5">
            <h3 class="font-bold text-sm text-white border-b border-slate-800 pb-3">Page Metadata & Slug</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-2">Page Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Examination Code of Conduct" required
                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-2">URL Slug (leave blank to auto-generate)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" placeholder="e.g. rules"
                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-2">Publication Status</label>
                    <select name="status" class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                        <option value="published" selected>Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>

                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_home" value="1" class="rounded bg-slate-800 border-slate-700 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-xs font-bold text-white">Set as Primary Website Homepage</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
            <h3 class="font-bold text-sm text-white border-b border-slate-800 pb-3">Per-Page SEO Parameters</h3>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-2">SEO Meta Title</label>
                <input type="text" name="seo_title" value="{{ old('seo_title') }}" placeholder="Custom browser tab title..."
                    class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-2">SEO Meta Description</label>
                <textarea name="seo_description" rows="2" placeholder="Brief summary for Google search snippets..."
                    class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('seo_description') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-2">SEO Keywords</label>
                <input type="text" name="seo_keywords" value="{{ old('seo_keywords') }}" placeholder="comma-separated keywords..."
                    class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.pages.index') }}" class="text-xs text-slate-400 hover:text-white">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg transition">
                Create Page & Open Widget Builder &rarr;
            </button>
        </div>
    </form>
</div>
@endsection
