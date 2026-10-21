@extends('layouts.admin')

@section('title', 'Questions: ' . $exam->title)
@section('page_title', 'Question Builder: ' . $exam->title)
@section('page_subtitle', $exam->course_code . ' • ' . $questions->count() . ' Total Questions • ' . $exam->totalPoints() . ' Total Points')

@section('topbar_actions')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.exams.index') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 transition">
            &larr; Exams
        </a>
        <a href="{{ route('admin.exams.questions.create', $exam) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/20 transition flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Question
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    <div class="space-y-4">
        @forelse($questions as $index => $q)
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl hover:border-slate-700 transition">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-indigo-600/20 text-indigo-400 font-bold text-xs flex items-center justify-center border border-indigo-500/30">
                            #{{ $index + 1 }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-400 font-semibold text-[11px] border border-slate-700">
                            {{ ucfirst(str_replace('_', ' ', $q->question_type)) }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/10 text-indigo-300 font-semibold text-[11px]">
                            {{ $q->points }} Point{{ $q->points > 1 ? 's' : '' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.exams.questions.edit', [$exam, $q]) }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 transition">
                            Edit
                        </a>
                        <form action="{{ route('admin.exams.questions.destroy', [$exam, $q]) }}" method="POST" onsubmit="return confirm('Delete this question?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 rounded-lg text-rose-400 hover:bg-rose-950/40 text-xs font-semibold transition">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Prompt -->
                <p class="text-sm font-semibold text-white mb-4 leading-relaxed">{{ $q->prompt }}</p>

                <!-- Code snippet preview if present -->
                @if($q->code_snippet)
                    <div class="mb-4 rounded-xl bg-slate-950 border border-slate-800 p-4 font-mono text-xs text-slate-300 overflow-x-auto">
                        <div class="flex items-center justify-between text-[10px] text-slate-500 border-b border-slate-800 pb-2 mb-2 font-sans font-bold">
                            <span>CODE SNIPPET</span>
                            <span class="text-indigo-400">{{ strtoupper($q->language ?? 'code') }}</span>
                        </div>
                        <pre class="leading-relaxed whitespace-pre">{{ $q->code_snippet }}</pre>
                    </div>
                @endif

                <!-- Options -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                    @foreach($q->options as $optIndex => $opt)
                        <div class="p-3 rounded-xl border flex items-center justify-between gap-3 {{ $opt->is_correct ? 'bg-emerald-950/30 border-emerald-600/60 text-emerald-200' : 'bg-slate-950 border-slate-800/80 text-slate-300' }}">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-md flex items-center justify-center font-bold text-[11px] {{ $opt->is_correct ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-400' }}">
                                    {{ chr(65 + $optIndex) }}
                                </span>
                                <span>{{ $opt->option_text }}</span>
                            </div>
                            @if($opt->is_correct)
                                <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider bg-emerald-500/20 px-2 py-0.5 rounded">Correct</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($q->explanation)
                    <div class="mt-4 pt-3 border-t border-slate-800/80 text-xs text-slate-400">
                        <strong class="text-slate-300">Explanation:</strong> {{ $q->explanation }}
                    </div>
                @endif
            </div>
        @empty
            <div class="py-16 text-center bg-slate-900 border border-slate-800 rounded-3xl">
                <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-semibold text-white mb-1">No questions added yet</p>
                <p class="text-xs text-slate-400 mb-4">Populate this examination with multiple choice, code output, or true/false questions.</p>
                <a href="{{ route('admin.exams.questions.create', $exam) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg transition">Add First Question</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
