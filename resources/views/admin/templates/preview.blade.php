@extends('layouts.admin')

@section('title', 'Preview: ' . $template->name)
@section('page_title', 'Rendered Email Preview: ' . $template->name)
@section('page_subtitle', 'Subject: ' . $rendered['subject'])

@section('topbar_actions')
    <a href="{{ route('admin.templates.edit', $template) }}" class="px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-sm transition">
        Edit Source HTML
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="p-4 rounded-xl bg-white border border-slate-200/80 shadow-sm text-xs text-slate-600">
        <p><strong class="text-slate-900">Simulated Subject:</strong> <span class="text-indigo-700 font-mono">{{ $rendered['subject'] }}</span></p>
    </div>

    <!-- Rendered Email Box -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-8 text-slate-900 overflow-hidden">
        {!! $rendered['body_html'] !!}
    </div>

</div>
@endsection
