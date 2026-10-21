@extends('layouts.admin')

@section('title', 'Edit Email Template: ' . $template->name)
@section('page_title', 'Edit Email Template: ' . $template->name)
@section('page_subtitle', 'Key: ' . $template->template_key)

@section('topbar_actions')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.templates.preview', $template) }}" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 hover:text-slate-900 text-xs font-semibold border border-slate-200 shadow-sm transition">
            Preview Rendered
        </a>
        <a href="{{ route('admin.templates.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-medium">
            &larr; Templates
        </a>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.templates.update', $template) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Template Display Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $template->name) }}" required
                        class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                </div>

                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ $template->is_active ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                        <span class="text-xs font-bold text-slate-900">Enable Automated Dynamic Dispatching</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email Subject Line <span class="text-rose-500">*</span></label>
                <input type="text" name="subject" value="{{ old('subject', $template->subject) }}" required
                    class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
            </div>

            <!-- Available Placeholders Reference -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5">Available Dynamic Replacement Tokens</span>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($template->placeholders ?? [] as $ph)
                        <button type="button" onclick="insertToken('{<span>{{ $ph }}</span>}')" class="px-2 py-0.5 rounded bg-white hover:bg-slate-50 text-indigo-700 text-[10px] font-mono border border-slate-200 shadow-sm transition" title="Click to insert">
                            {<span>{{ $ph }}</span>}
                        </button>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">HTML Email Template Body <span class="text-rose-500">*</span></label>
                <textarea id="body_html" name="body_html" rows="18" required
                    class="w-full px-4 py-3 bg-slate-900 font-mono border border-slate-800 rounded-xl text-xs text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 leading-relaxed shadow-inner">{{ old('body_html', $template->body_html) }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.templates.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-medium">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm transition">
                Save Email Template &rarr;
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function insertToken(token) {
        const textarea = document.getElementById('body_html');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + token + text.substring(end);
        textarea.focus();
        textarea.selectionEnd = start + token.length;
    }
</script>
@endpush
