@extends('layouts.admin')

@section('title', 'Create Page')
@section('page_title', 'Create New Page')
@section('page_subtitle', 'Setup URL slug, page title, and search engine parameters')

@section('content')
<div class="max-w-3xl mx-auto">
    <form action="{{ route('admin.pages.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-5">
            <h3 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-3">Page Metadata & Slug</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Page Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Examination Code of Conduct" required
                        class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">URL Slug (leave blank to auto-generate)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" placeholder="e.g. rules"
                        class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-mono transition">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Publication Status</label>
                    <select name="status" class="w-full px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                        <option value="published" selected>Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>

                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_home" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                        <span class="text-xs font-bold text-slate-900">Set as Primary Website Homepage</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-3">Per-Page SEO Parameters</h3>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">SEO Meta Title</label>
                <input type="text" name="seo_title" value="{{ old('seo_title') }}" placeholder="Custom browser tab title..."
                    class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">SEO Meta Description</label>
                <textarea name="seo_description" rows="2" placeholder="Brief summary for Google search snippets..."
                    class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">{{ old('seo_description') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.pages.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-medium">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm transition">
                Create Page & Open Widget Canvas &rarr;
            </button>
        </div>
    </form>
</div>
@endsection
