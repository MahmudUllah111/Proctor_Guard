@extends('layouts.admin')

@section('title', 'Questions: ' . $exam->title)
@section('page_title', 'Question Builder: ' . $exam->title)
@section('page_subtitle', $exam->course_code . ' • ' . $questions->count() . ' Total Questions • ' . $exam->totalPoints() . ' Total Points')

@section('topbar_actions')
    <a href="{{ route('admin.exams.index') }}" class="btn-secondary"><x-icon name="arrow-left" class="w-3.5 h-3.5" />Exams</a>
    <a href="{{ route('admin.exams.questions.create', $exam) }}" class="btn-primary"><x-icon name="plus" class="w-3.5 h-3.5" />Add question</a>
@endsection

@section('content')
<div>
    <div class="space-y-3">
        @forelse($questions as $index => $q)
            <div class="card p-5">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 font-semibold text-xs flex items-center justify-center num">
                            {{ $index + 1 }}
                        </span>
                        <span class="pill-indigo">
                            {{ ucfirst(str_replace('_', ' ', $q->question_type)) }}
                        </span>
                        <span class="pill-slate num">
                            {{ $q->points }} Point{{ $q->points > 1 ? 's' : '' }}
                        </span>
                    </div>

                    <div class="icon-group">
                        <x-icon-button :href="route('admin.exams.questions.edit', [$exam, $q])" icon="pencil" label="Edit question" tone="primary" />
                        <form action="{{ route('admin.exams.questions.destroy', [$exam, $q]) }}" method="POST" onsubmit="return confirm('Delete this question?')">
                            @csrf
                            @method('DELETE')
                            <x-icon-button type="submit" icon="trash-2" label="Delete question" tone="danger" />
                        </form>
                    </div>
                </div>

                <!-- Prompt -->
                <p class="text-sm font-semibold text-slate-900 mb-4 leading-relaxed">{{ $q->prompt }}</p>

                <!-- Code snippet preview if present -->
                @if($q->code_snippet)
                    <div class="mb-4 rounded-xl bg-slate-900 border border-slate-800 p-4 font-mono text-xs text-slate-200 overflow-x-auto shadow-inner">
                        <div class="flex items-center justify-between text-[10px] text-slate-400 border-b border-slate-800 pb-2 mb-2 font-sans font-bold">
                            <span>CODE SNIPPET</span>
                            <span class="text-indigo-400">{{ strtoupper($q->language ?? 'code') }}</span>
                        </div>
                        <pre class="leading-relaxed whitespace-pre">{{ $q->code_snippet }}</pre>
                    </div>
                @endif

                <!-- Options -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                    @foreach($q->options as $optIndex => $opt)
                        <div class="p-3 rounded-xl border flex items-center justify-between gap-3 {{ $opt->is_correct ? 'bg-emerald-50/70 border-emerald-300 text-emerald-900' : 'bg-slate-50 border-slate-200/80 text-slate-700' }}">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-md flex items-center justify-center font-bold text-[11px] {{ $opt->is_correct ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                                    {{ chr(65 + $optIndex) }}
                                </span>
                                <span class="font-medium">{{ $opt->option_text }}</span>
                            </div>
                            @if($opt->is_correct)
                                <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider bg-emerald-100 border border-emerald-200 px-2 py-0.5 rounded">Correct</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($q->explanation)
                    <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-500">
                        <strong class="text-slate-700">Explanation:</strong> {{ $q->explanation }}
                    </div>
                @endif
            </div>
        @empty
            <div class="py-16 text-center bg-white border border-slate-200/80 rounded-3xl shadow-sm">
                <svg class="w-12 h-12 text-slate-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-semibold text-slate-900 mb-1">No questions added yet</p>
                <p class="text-xs text-slate-500 mb-4">Populate this examination with multiple choice, code output, or true/false questions.</p>
                <a href="{{ route('admin.exams.questions.create', $exam) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm transition">Add First Question</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
