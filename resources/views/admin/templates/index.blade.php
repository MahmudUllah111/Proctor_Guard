@extends('layouts.admin')

@section('title', 'Email Templates')
@section('page_title', 'Dynamic HTML Email Templates')
@section('page_subtitle', 'Customize student score reports and high-priority administrative cheat strike alerts')

@section('content')
<div class="space-y-6">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($templates as $tmpl)
            <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm flex flex-col justify-between hover:border-slate-300 hover:shadow transition">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-mono text-xs font-bold border border-slate-200/60">{{ $tmpl->template_key }}</span>
                        @if($tmpl->is_active)
                            <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold text-[10px]">Active</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 font-semibold text-[10px]">Inactive</span>
                        @endif
                    </div>

                    <h3 class="font-bold text-base text-slate-900 mb-2">{{ $tmpl->name }}</h3>
                    <p class="text-xs text-slate-600 font-mono mb-4 truncate"><strong class="text-slate-700">Subject:</strong> {{ $tmpl->subject }}</p>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/60 mb-6">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-2">Available Tokens</span>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($tmpl->placeholders ?? [] as $ph)
                                <code class="px-2 py-0.5 rounded bg-white border border-slate-200 text-indigo-700 text-[10px] font-mono shadow-sm">{<span>{{ $ph }}</span>}</code>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('admin.templates.preview', $tmpl) }}" target="_blank" class="text-xs text-slate-500 hover:text-slate-800 font-medium flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Render Preview
                    </a>
                    <a href="{{ route('admin.templates.edit', $tmpl) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm transition">
                        Edit Template
                    </a>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
