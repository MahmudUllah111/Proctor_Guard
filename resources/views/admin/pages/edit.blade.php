@extends('layouts.admin')

@section('title', 'Page Builder: ' . $page->title)
@section('page_title', 'Widget Page Builder: ' . $page->title)
@section('page_subtitle', 'Modular CMS blocks • Slug: ' . ($page->is_home ? '/' : '/page/' . $page->slug))

@section('topbar_actions')
    <div class="flex items-center gap-3">
        <a href="{{ $page->is_home ? route('home') : route('public.page', $page->slug) }}" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 transition flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            Live Preview
        </a>
    </div>
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <form id="page-builder-form" action="{{ route('admin.pages.update', $page) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        <input type="hidden" name="widgets_json" id="widgets-json-input" value="{{ json_encode($page->content_widgets ?? []) }}">

        <!-- Page Configuration Settings -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="font-bold text-sm text-white">Page Settings & Metadata</h3>
                <span class="text-xs text-slate-400">Status: <strong class="text-emerald-400">{{ ucfirst($page->status) }}</strong></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Page Title</label>
                    <input type="text" name="title" value="{{ old('title', $page->title) }}" required
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">URL Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $page->slug) }}" required
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Publication Status</label>
                    <select name="status" class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                        <option value="published" {{ $page->status === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ $page->status === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_home" value="1" {{ $page->is_home ? 'checked' : '' }} class="rounded bg-slate-800 border-slate-700 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs font-bold text-white">Designate as Primary Website Homepage</span>
                </label>
            </div>
        </div>

        <!-- Widget Canvas & Builder Section -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div>
                    <h3 class="font-bold text-sm text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Modular Content Widgets
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Add, reorder, or edit content components dynamically</p>
                </div>

                <!-- Add Widget Dropdown -->
                <div class="flex items-center gap-2">
                    <select id="widget-type-select" class="px-3.5 py-1.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                        <option value="hero">Hero Banner Widget</option>
                        <option value="features">Features Grid Widget</option>
                        <option value="text_html">Text & HTML Widget</option>
                        <option value="cta">Call to Action Banner</option>
                        <option value="accordion">FAQ / Rules Accordion</option>
                        <option value="form_embed">Contact & Lead Form Widget</option>
                    </select>
                    <button type="button" onclick="addNewWidget()" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Widget
                    </button>
                </div>
            </div>

            <!-- Widgets List Container -->
            <div id="widgets-container" class="space-y-4">
                <!-- Rendered dynamically via JavaScript from widgets-json-input -->
            </div>
        </div>

        <!-- Per-Page SEO Controls Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
            <h3 class="font-bold text-sm text-white border-b border-slate-800 pb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Per-Page SEO Controls
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">SEO Meta Title</label>
                    <input type="text" name="seo_title" value="{{ old('seo_title', $page->seo_title) }}" placeholder="Custom Google title..."
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">SEO Keywords</label>
                    <input type="text" name="seo_keywords" value="{{ old('seo_keywords', $page->seo_keywords) }}" placeholder="web development, php, exam..."
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">SEO Meta Description</label>
                <textarea name="seo_description" rows="2" placeholder="Search snippet text..."
                    class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('seo_description', $page->seo_description) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">OpenGraph Share Image (Social Cards)</label>
                <div class="flex items-center gap-3">
                    <input type="text" id="og-image-input" name="og_image" value="{{ old('og_image', $page->og_image) }}" placeholder="https://... or select from media library"
                        class="flex-1 px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <button type="button" onclick="openMediaModal((asset) => document.getElementById('og-image-input').value = asset.url)" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-indigo-400 font-semibold text-xs rounded-xl border border-slate-700 transition">
                        Select from Media Library
                    </button>
                </div>
            </div>
        </div>

        <!-- Submit & Save Bar -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.pages.index') }}" class="text-xs text-slate-400 hover:text-white">Cancel</a>
            <button type="submit" onclick="syncWidgetsToInput()" class="px-7 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition">
                Save Page & Publish Widgets &rarr;
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    let widgets = [];
    try {
        widgets = JSON.parse(document.getElementById('widgets-json-input').value) || [];
    } catch (e) {
        widgets = [];
    }

    function renderWidgets() {
        const container = document.getElementById('widgets-container');
        if (widgets.length === 0) {
            container.innerHTML = `
            <div class="py-12 text-center text-slate-500 border border-dashed border-slate-800 rounded-2xl">
                <p class="text-xs font-semibold text-slate-400">No content widgets attached yet</p>
                <p class="text-[11px] text-slate-500 mt-1">Select a widget type above and click "Add Widget" to build this page.</p>
            </div>`;
            return;
        }

        container.innerHTML = widgets.map((w, idx) => {
            return `
            <div class="p-5 rounded-2xl bg-slate-950 border border-slate-800/90 space-y-4 hover:border-slate-700 transition">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-md bg-indigo-600/20 text-indigo-400 font-mono font-bold text-xs flex items-center justify-center">
                            ${idx + 1}
                        </span>
                        <span class="font-bold text-white text-xs uppercase tracking-wider">${formatWidgetName(w.type)}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        ${idx > 0 ? `<button type="button" onclick="moveWidget(${idx}, -1)" class="p-1 text-slate-400 hover:text-white" title="Move Up">&uarr;</button>` : ''}
                        ${idx < widgets.length - 1 ? `<button type="button" onclick="moveWidget(${idx}, 1)" class="p-1 text-slate-400 hover:text-white" title="Move Down">&darr;</button>` : ''}
                        <button type="button" onclick="removeWidget(${idx})" class="p-1 text-rose-400 hover:text-rose-300 ml-2" title="Remove Widget">&times;</button>
                    </div>
                </div>

                <!-- Widget Specific Form Controls -->
                ${renderWidgetFields(w, idx)}
            </div>`;
        }).join('');
    }

    function formatWidgetName(type) {
        return {
            'hero': 'Hero Banner Widget',
            'features': 'Feature Highlights Grid',
            'text_html': 'Rich Text & HTML Widget',
            'cta': 'Call to Action Banner',
            'accordion': 'Rules & FAQ Accordion',
            'form_embed': 'Inquiry & Lead Form Embed',
        }[type] || type;
    }

    function renderWidgetFields(w, idx) {
        if (w.type === 'hero') {
            return `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                <div>
                    <label class="block text-slate-400 mb-1">Headline</label>
                    <input type="text" value="${escapeHtml(w.headline || '')}" oninput="widgets[${idx}].headline = this.value" class="w-full px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-lg text-white">
                </div>
                <div>
                    <label class="block text-slate-400 mb-1">Subheadline</label>
                    <input type="text" value="${escapeHtml(w.subhead || '')}" oninput="widgets[${idx}].subhead = this.value" class="w-full px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-lg text-white">
                </div>
                <div>
                    <label class="block text-slate-400 mb-1">Button Text</label>
                    <input type="text" value="${escapeHtml(w.button_text || '')}" oninput="widgets[${idx}].button_text = this.value" class="w-full px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-lg text-white">
                </div>
                <div>
                    <label class="block text-slate-400 mb-1">Button URL</label>
                    <input type="text" value="${escapeHtml(w.button_url || '')}" oninput="widgets[${idx}].button_url = this.value" class="w-full px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-lg text-white">
                </div>
                <div class="col-span-full">
                    <label class="block text-slate-400 mb-1">Background Image URL (Optional)</label>
                    <div class="flex gap-2">
                        <input type="text" id="hero-bg-${idx}" value="${escapeHtml(w.bg_image || '')}" oninput="widgets[${idx}].bg_image = this.value" class="flex-1 px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-lg text-white">
                        <button type="button" onclick="openMediaModal((a) => { widgets[${idx}].bg_image = a.url; document.getElementById('hero-bg-${idx}').value = a.url; })" class="px-3 py-1.5 bg-slate-800 text-indigo-400 border border-slate-700 rounded-lg">Media</button>
                    </div>
                </div>
            </div>`;
        } else if (w.type === 'text_html') {
            return `
            <div class="text-xs">
                <label class="block text-slate-400 mb-1">HTML / Text Content</label>
                <textarea rows="5" oninput="widgets[${idx}].content = this.value" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white font-mono">${escapeHtml(w.content || '')}</textarea>
            </div>`;
        } else if (w.type === 'cta') {
            return `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                <div>
                    <label class="block text-slate-400 mb-1">Call to Action Title</label>
                    <input type="text" value="${escapeHtml(w.title || '')}" oninput="widgets[${idx}].title = this.value" class="w-full px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-lg text-white">
                </div>
                <div>
                    <label class="block text-slate-400 mb-1">Action Button Text</label>
                    <input type="text" value="${escapeHtml(w.button_text || '')}" oninput="widgets[${idx}].button_text = this.value" class="w-full px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-lg text-white">
                </div>
                <div class="col-span-full">
                    <label class="block text-slate-400 mb-1">Message</label>
                    <input type="text" value="${escapeHtml(w.message || '')}" oninput="widgets[${idx}].message = this.value" class="w-full px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-lg text-white">
                </div>
            </div>`;
        } else if (w.type === 'accordion') {
            return `
            <div class="text-xs space-y-2">
                <label class="block text-slate-400">Accordion Title</label>
                <input type="text" value="${escapeHtml(w.title || '')}" oninput="widgets[${idx}].title = this.value" class="w-full px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-lg text-white mb-2">
                <p class="text-slate-400 text-[11px]">Enter questions and answers (one item per line formatted as "Question | Answer"):</p>
                <textarea rows="4" oninput="widgets[${idx}].raw_items = this.value" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white font-mono" placeholder="Are headphones permitted? | No, audio devices are prohibited.">${escapeHtml(w.raw_items || '')}</textarea>
            </div>`;
        } else if (w.type === 'features') {
            return `
            <div class="text-xs space-y-2">
                <label class="block text-slate-400">Section Title</label>
                <input type="text" value="${escapeHtml(w.title || '')}" oninput="widgets[${idx}].title = this.value" class="w-full px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-lg text-white mb-2">
                <p class="text-slate-400 text-[11px]">Feature cards (format: "Title | Description"):</p>
                <textarea rows="4" oninput="widgets[${idx}].raw_items = this.value" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white font-mono" placeholder="Canvas Pixel Buffer | Prevents AI DOM text scrapers.">${escapeHtml(w.raw_items || '')}</textarea>
            </div>`;
        } else if (w.type === 'form_embed') {
            return `
            <div class="text-xs grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-400 mb-1">Form Heading</label>
                    <input type="text" value="${escapeHtml(w.heading || 'Candidate Inquiries & Support')}" oninput="widgets[${idx}].heading = this.value" class="w-full px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-lg text-white">
                </div>
                <div>
                    <label class="block text-slate-400 mb-1">Form Tag / Category</label>
                    <input type="text" value="${escapeHtml(w.form_name || 'contact')}" oninput="widgets[${idx}].form_name = this.value" class="w-full px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-lg text-white">
                </div>
            </div>`;
        }
        return '';
    }

    function addNewWidget() {
        const type = document.getElementById('widget-type-select').value;
        const newWidget = { type: type };
        if (type === 'hero') {
            newWidget.headline = 'Secure Examination Portal';
            newWidget.subhead = 'Enterprise proctoring engine with canvas pixel buffers and forensic watermarking.';
            newWidget.button_text = 'Take Assessment';
            newWidget.button_url = '/';
        } else if (type === 'cta') {
            newWidget.title = 'Need to schedule a proctored assessment?';
            newWidget.message = 'Connect with our examination administration team for university-wide deployment.';
            newWidget.button_text = 'Contact Proctoring Office';
        } else if (type === 'accordion') {
            newWidget.title = 'Candidate Code of Conduct & Integrity Guidelines';
            newWidget.raw_items = "Can I switch browser tabs? | No, switching tabs immediately records an infraction strike.\nWhat happens if DevTools are opened? | Opening Developer Tools triggers an automatic disqualification.";
        }
        widgets.push(newWidget);
        renderWidgets();
    }

    function removeWidget(idx) {
        if (confirm('Delete this widget block?')) {
            widgets.splice(idx, 1);
            renderWidgets();
        }
    }

    function moveWidget(idx, dir) {
        const target = idx + dir;
        if (target >= 0 && target < widgets.length) {
            const item = widgets.splice(idx, 1)[0];
            widgets.splice(target, 0, item);
            renderWidgets();
        }
    }

    function syncWidgetsToInput() {
        document.getElementById('widgets-json-input').value = JSON.stringify(widgets);
    }

    function escapeHtml(text) {
        return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    renderWidgets();
</script>
@endpush
