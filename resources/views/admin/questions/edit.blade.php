@extends('layouts.admin')

@section('title', 'Edit Question')
@section('page_title', 'Edit Question #' . $question->id)
@section('page_subtitle', $exam->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.exams.questions.update', [$exam, $question]) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Question Prompt & Code Card -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-5">
            <h3 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Question Statement
            </h3>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Question Prompt <span class="text-rose-500">*</span></label>
                <textarea name="prompt" rows="3" required
                    class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">{{ old('prompt', $question->prompt) }}</textarea>
            </div>

            <!-- Code snippet section -->
            <div class="pt-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-semibold text-slate-700">Code Snippet (Optional)</label>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] text-slate-500">Language:</span>
                        <select name="language" class="px-3 py-1 bg-white border border-slate-200 rounded-lg text-xs text-slate-800 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            <option value="php" {{ $question->language === 'php' ? 'selected' : '' }}>PHP 8.3</option>
                            <option value="javascript" {{ $question->language === 'javascript' ? 'selected' : '' }}>JavaScript</option>
                            <option value="sql" {{ $question->language === 'sql' ? 'selected' : '' }}>SQL / MySQL</option>
                            <option value="python" {{ $question->language === 'python' ? 'selected' : '' }}>Python</option>
                            <option value="html" {{ $question->language === 'html' ? 'selected' : '' }}>HTML5 / CSS</option>
                            <option value="bash" {{ $question->language === 'bash' ? 'selected' : '' }}>Shell / Bash</option>
                        </select>
                    </div>
                </div>
                <textarea name="code_snippet" rows="6"
                    class="w-full px-4 py-3 bg-slate-900 font-mono border border-slate-800 rounded-xl text-xs text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 shadow-inner">{{ old('code_snippet', $question->code_snippet) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Question Type</label>
                    <select name="question_type" class="w-full px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                        <option value="multiple_choice" {{ $question->question_type === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice (Single Correct Answer)</option>
                        <option value="code_output" {{ $question->question_type === 'code_output' ? 'selected' : '' }}>Code Output Analysis</option>
                        <option value="true_false" {{ $question->question_type === 'true_false' ? 'selected' : '' }}>True / False</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Points Value <span class="text-rose-500">*</span></label>
                    <input type="number" name="points" value="{{ old('points', $question->points) }}" min="1" max="100" required
                        class="w-full px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                </div>
            </div>
        </div>

        <!-- Multiple Choice Options Card -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Answer Options
                </h3>
                <span class="text-[11px] text-slate-500">Select the radio button next to the <strong>Correct Answer</strong>.</span>
            </div>

            <div id="options-container" class="space-y-3">
                @php $options = $question->options; @endphp
                @for($i = 0; $i < max(4, count($options)); $i++)
                    @php
                        $opt = $options[$i] ?? null;
                        $isCorrect = $opt ? $opt->is_correct : ($i === 0);
                        $optText = $opt ? $opt->option_text : '';
                    @endphp
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center gap-3">
                        <label class="flex items-center gap-2 cursor-pointer flex-shrink-0">
                            <input type="radio" name="correct_option_index" value="{{ $i }}" {{ $isCorrect ? 'checked' : '' }}
                                class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500/20">
                            <span class="w-6 h-6 rounded-md bg-white border border-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs shadow-sm">
                                {{ chr(65 + $i) }}
                            </span>
                        </label>
                        <input type="text" name="options[{{ $i }}][text]" value="{{ old("options.{$i}.text", $optText) }}" required
                            placeholder="Option {{ chr(65 + $i) }} text..."
                            class="flex-1 px-3.5 py-2 bg-white border border-slate-200 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                    </div>
                @endfor
            </div>
        </div>

        <!-- Explanation Card -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Answer Rationale & Audit Explanation (Optional)
            </h3>
            <textarea name="explanation" rows="2"
                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">{{ old('explanation', $question->explanation) }}</textarea>
        </div>

        <!-- Submit buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.exams.questions.index', $exam) }}" class="text-xs text-slate-500 hover:text-slate-800 font-medium">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm transition">
                Update Question &rarr;
            </button>
        </div>
    </form>
</div>
@endsection
