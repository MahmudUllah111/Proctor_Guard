@extends('layouts.admin')

@section('title', 'Email Templates')
@section('page_title', 'Dynamic HTML Email Templates')
@section('page_subtitle', 'Customize student score reports and high-priority administrative cheat strike alerts')

@section('content')
<div class="space-y-6">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($templates as $tmpl)
            <div class="card p-5 flex flex-col justify-between hover:border-slate-300 transition">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="token-chip">{{ $tmpl->template_key }}</span>
                        @if($tmpl->is_active)
                            <span class="pill-emerald">Active</span>
                        @else
                            <span class="pill-slate">Inactive</span>
                        @endif
                    </div>

                    <h3 class="font-semibold text-sm text-slate-900 mb-1">{{ $tmpl->name }}</h3>
                    <p class="text-xs text-slate-600 mb-4 truncate font-mono"><strong class="text-slate-700 font-sans">Subject:</strong> {{ $tmpl->subject }}</p>

                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/70 mb-5">
                        <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-500 block mb-2">Available Tokens</span>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($tmpl->placeholders ?? [] as $ph)
                                <code class="token-chip text-indigo-700 bg-white text-[10.5px] shadow-xs">{<span>{{ $ph }}</span>}</code>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="pt-3.5 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('admin.templates.preview', $tmpl) }}" target="_blank" class="btn-secondary">
                        <x-icon name="eye" class="w-3.5 h-3.5 text-indigo-600" />
                        <span>Render Preview</span>
                    </a>
                    <a href="{{ route('admin.templates.edit', $tmpl) }}" class="btn-primary">
                        <x-icon name="pencil" class="w-3.5 h-3.5" />
                        <span>Edit Template</span>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
