@extends('layouts.admin')

@section('title', 'Edit Email Template: ' . $template->name)
@section('page_title', 'Edit Email Template: ' . $template->name)
@section('page_subtitle', 'Key: ' . $template->template_key)

@section('topbar_actions')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.templates.preview', $template) }}" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 transition">
            Preview Rendered
        </a>
        <a href="{{ route('admin.templates.index') }}" class="text-xs text-slate-400 hover:text-white">
            &larr; Templates
        </a>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.templates.update', $template) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Template Display Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $template->name) }}" required
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ $template->is_active ? 'checked' : '' }} class="rounded bg-slate-800 border-slate-700 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-xs font-bold text-white">Enable Automated Dynamic Dispatching</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Email Subject Line <span class="text-rose-500">*</span></label>
                <input type="text" name="subject" value="{{ old('subject', $template->subject) }}" required
                    class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Available Placeholders Reference -->
            <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Available Dynamic Replacement Tokens</span>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($template->placeholders ?? [] as $ph)
                        <button type="button" onclick="insertToken('{<span>{{ $ph }}</span>}')" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-indigo-300 text-[10px] font-mono border border-slate-700 transition" title="Click to insert">
                            {<span>{{ $ph }}</span>}
                        </button>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">HTML Email Template Body <span class="text-rose-500">*</span></label>
                <textarea id="body_html" name="body_html" rows="18" required
                    class="w-full px-4 py-3 bg-slate-950 font-mono border border-slate-800 rounded-xl text-xs text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 leading-relaxed">{{ old('body_html', $template->body_html) }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('admin.templates.index') }}" class="text-xs text-slate-400 hover:text-white">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg transition">
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
