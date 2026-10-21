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
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-5">
            <h3 class="font-bold text-sm text-white border-b border-slate-800 pb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Question Statement
            </h3>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-2">Question Prompt <span class="text-rose-500">*</span></label>
                <textarea name="prompt" rows="3" required
                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('prompt', $question->prompt) }}</textarea>
            </div>

            <!-- Code snippet section -->
            <div class="pt-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-semibold text-slate-300">Code Snippet (Optional)</label>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] text-slate-500">Language:</span>
                        <select name="language" class="px-3 py-1 bg-slate-800 border border-slate-700 rounded-lg text-xs text-white">
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
                    class="w-full px-4 py-3 bg-slate-950 font-mono border border-slate-800 rounded-xl text-xs text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('code_snippet', $question->code_snippet) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-2">Question Type</label>
                    <select name="question_type" class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                        <option value="multiple_choice" {{ $question->question_type === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice (Single Correct Answer)</option>
                        <option value="code_output" {{ $question->question_type === 'code_output' ? 'selected' : '' }}>Code Output Analysis</option>
                        <option value="true_false" {{ $question->question_type === 'true_false' ? 'selected' : '' }}>True / False</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-2">Points Value <span class="text-rose-500">*</span></label>
                    <input type="number" name="points" value="{{ old('points', $question->points) }}" min="1" max="100" required
                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- Multiple Choice Options Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="font-bold text-sm text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Answer Options
                </h3>
                <span class="text-[11px] text-slate-400">Select the radio button next to the <strong>Correct Answer</strong>.</span>
            </div>

            <div id="options-container" class="space-y-3">
                @php $options = $question->options; @endphp
                @for($i = 0; $i < max(4, count($options)); $i++)
                    @php
                        $opt = $options[$i] ?? null;
                        $isCorrect = $opt ? $opt->is_correct : ($i === 0);
                        $optText = $opt ? $opt->option_text : '';
                    @endphp
                    <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 flex items-center gap-3">
                        <label class="flex items-center gap-2 cursor-pointer flex-shrink-0">
                            <input type="radio" name="correct_option_index" value="{{ $i }}" {{ $isCorrect ? 'checked' : '' }}
                                class="w-4 h-4 text-emerald-600 bg-slate-800 border-slate-700 focus:ring-emerald-500">
                            <span class="w-6 h-6 rounded-md bg-slate-800 text-slate-300 flex items-center justify-center font-bold text-xs">
                                {{ chr(65 + $i) }}
                            </span>
                        </label>
                        <input type="text" name="options[{{ $i }}][text]" value="{{ old("options.{$i}.text", $optText) }}" required
                            placeholder="Option {{ chr(65 + $i) }} text..."
                            class="flex-1 px-3.5 py-2 bg-slate-800/80 border border-slate-700 rounded-lg text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                @endfor
            </div>
        </div>

        <!-- Explanation Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-4">
            <h3 class="font-bold text-sm text-white flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Answer Rationale & Audit Explanation (Optional)
            </h3>
            <textarea name="explanation" rows="2"
                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('explanation', $question->explanation) }}</textarea>
        </div>

        <!-- Submit buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.exams.questions.index', $exam) }}" class="text-xs text-slate-400 hover:text-white">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition">
                Update Question &rarr;
            </button>
        </div>
    </form>
</div>
@endsection
