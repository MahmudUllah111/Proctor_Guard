@extends('layouts.admin')

@section('title', 'Add Question')
@section('page_title', 'Add Question to ' . ($exam->course_code ?? 'Exam'))
@section('page_subtitle', $exam->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.exams.questions.store', $exam) }}" method="POST" class="space-y-6">
        @csrf

        <!-- Question Prompt & Code Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-5">
            <h3 class="font-bold text-sm text-white border-b border-slate-800 pb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Question Statement
            </h3>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-2">Question Prompt <span class="text-rose-500">*</span></label>
                <textarea name="prompt" rows="3" required placeholder="Enter the question text or problem statement..."
                    class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('prompt') }}</textarea>
                <p class="text-[11px] text-slate-500 mt-1">Note: This prompt will be rendered into the Canvas pixel buffer to prevent AI extension scraping.</p>
            </div>

            <!-- Code snippet section -->
            <div class="pt-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-semibold text-slate-300">Code Snippet (Optional)</label>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] text-slate-500">Language:</span>
                        <select name="language" class="px-3 py-1 bg-slate-800 border border-slate-700 rounded-lg text-xs text-white">
                            <option value="php">PHP 8.3</option>
                            <option value="javascript">JavaScript</option>
                            <option value="sql">SQL / MySQL</option>
                            <option value="python">Python</option>
                            <option value="html">HTML5 / CSS</option>
                            <option value="bash">Shell / Bash</option>
                        </select>
                    </div>
                </div>
                <textarea name="code_snippet" rows="6" placeholder="// Paste optional code snippet here..."
                    class="w-full px-4 py-3 bg-slate-950 font-mono border border-slate-800 rounded-xl text-xs text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('code_snippet') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-2">Question Type</label>
                    <select name="question_type" class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                        <option value="multiple_choice" selected>Multiple Choice (Single Correct Answer)</option>
                        <option value="code_output">Code Output Analysis</option>
                        <option value="true_false">True / False</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-2">Points Value <span class="text-rose-500">*</span></label>
                    <input type="number" name="points" value="{{ old('points', 1) }}" min="1" max="100" required
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
                @for($i = 0; $i < 4; $i++)
                    <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 flex items-center gap-3">
                        <label class="flex items-center gap-2 cursor-pointer flex-shrink-0">
                            <input type="radio" name="correct_option_index" value="{{ $i }}" {{ $i === 0 ? 'checked' : '' }}
                                class="w-4 h-4 text-emerald-600 bg-slate-800 border-slate-700 focus:ring-emerald-500">
                            <span class="w-6 h-6 rounded-md bg-slate-800 text-slate-300 flex items-center justify-center font-bold text-xs">
                                {{ chr(65 + $i) }}
                            </span>
                        </label>
                        <input type="text" name="options[{{ $i }}][text]" value="{{ old("options.{$i}.text") }}" required
                            placeholder="Option {{ chr(65 + $i) }} text..."
                            class="flex-1 px-3.5 py-2 bg-slate-800/80 border border-slate-700 rounded-lg text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
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
            <textarea name="explanation" rows="2" placeholder="Why this is the correct answer (shown in post-exam candidate breakdown and proctor audits)..."
                class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('explanation') }}</textarea>
        </div>

        <!-- Submit buttons -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.exams.questions.index', $exam) }}" class="text-xs text-slate-400 hover:text-white">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition">
                Save Question &rarr;
            </button>
        </div>
    </form>
</div>
@endsection
