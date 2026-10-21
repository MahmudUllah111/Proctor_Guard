@extends('layouts.admin')

@section('title', 'Preview: ' . $template->name)
@section('page_title', 'Rendered Email Preview: ' . $template->name)
@section('page_subtitle', 'Subject: ' . $rendered['subject'])

@section('topbar_actions')
    <a href="{{ route('admin.templates.edit', $template) }}" class="px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs transition">
        Edit Source HTML
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-300">
        <p><strong>Simulated Subject:</strong> <span class="text-white font-mono">{{ $rendered['subject'] }}</span></p>
    </div>

    <!-- Rendered Email Box -->
    <div class="bg-white rounded-2xl shadow-2xl p-6 text-slate-900 overflow-hidden">
        {!! $rendered['body_html'] !!}
    </div>

</div>
@endsection
