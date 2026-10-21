@extends('layouts.admin')

@section('title', 'Edit Examination')
@section('page_title', 'Edit Examination: ' . $exam->title)
@section('page_subtitle', 'Update timing, pass mark, proctoring rules, and access code')

@section('topbar_actions')
    <a href="{{ route('admin.exams.questions.index', $exam) }}" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Manage Questions ({{ $exam->questions()->count() }})
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.exams.update', $exam) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- General Info Card -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-5">
            <h3 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                General Examination Metadata
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Exam Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $exam->title) }}" required
                        class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Course Code</label>
                    <input type="text" name="course_code" value="{{ old('course_code', $exam->course_code) }}"
                        class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Description / Objectives</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">{{ old('description', $exam->description) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Candidate Instructions (Shown in Waiting Room)</label>
                <textarea name="instructions" rows="3"
                    class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">{{ old('instructions', $exam->instructions) }}</textarea>
            </div>
        </div>

        <!-- Parameters & Scoring Card -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-5">
            <h3 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Timing, Pass Criteria & Access
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Duration (Minutes) <span class="text-rose-500">*</span></label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" min="1" max="600" required
                        class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Passing Grade (%) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.1" name="pass_percentage" value="{{ old('pass_percentage', $exam->pass_percentage) }}" min="0" max="100" required
                        class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Max Infraction Strikes <span class="text-rose-500">*</span></label>
                    <input type="number" name="max_strikes" value="{{ old('max_strikes', $exam->max_strikes) }}" min="1" max="20" required
                        class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Access Passcode (Optional)</label>
                    <input type="text" name="access_passcode" value="{{ old('access_passcode', $exam->access_passcode) }}" placeholder="Leave blank if open"
                        class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                </div>
            </div>
        </div>

        <!-- Anti-Cheat Proctoring Engine Configuration -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-5">
            <h3 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Anti-Cheat Proctoring Engine & Canvas Security
            </h3>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Enforcement Strictness Mode</label>
                <select name="anti_cheat_strictness" class="w-full md:w-72 px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                    <option value="strict" {{ $exam->anti_cheat_strictness === 'strict' ? 'selected' : '' }}>Strict (Full Canvas + Immediate Auto-Lockout)</option>
                    <option value="medium" {{ $exam->anti_cheat_strictness === 'medium' ? 'selected' : '' }}>Medium (Warnings + High Threshold)</option>
                    <option value="lenient" {{ $exam->anti_cheat_strictness === 'lenient' ? 'selected' : '' }}>Lenient (Telemetry Logging Only)</option>
                </select>
            </div>

            <!-- Checkbox matrix -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <label class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3 cursor-pointer hover:border-slate-300 transition">
                    <input type="checkbox" name="enforce_fullscreen" value="1" {{ $exam->enforce_fullscreen ? 'checked' : '' }} class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                    <div>
                        <span class="block text-xs font-bold text-slate-900">Enforce Fullscreen Examination</span>
                        <span class="text-[11px] text-slate-500">Forces full screen on start; flags warning strike on exit.</span>
                    </div>
                </label>

                <label class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3 cursor-pointer hover:border-slate-300 transition">
                    <input type="checkbox" name="block_clipboard" value="1" {{ $exam->block_clipboard ? 'checked' : '' }} class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                    <div>
                        <span class="block text-xs font-bold text-slate-900">Block Clipboard & Context Menu</span>
                        <span class="text-[11px] text-slate-500">Disables right-click, text selection, copy, cut, and paste.</span>
                    </div>
                </label>

                <label class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3 cursor-pointer hover:border-slate-300 transition">
                    <input type="checkbox" name="block_devtools" value="1" {{ $exam->block_devtools ? 'checked' : '' }} class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                    <div>
                        <span class="block text-xs font-bold text-slate-900">Detect DevTools & Inspect Shortcuts</span>
                        <span class="text-[11px] text-slate-500">Blocks F12, Ctrl+Shift+I/J/C; detects docked inspection windows.</span>
                    </div>
                </label>

                <label class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3 cursor-pointer hover:border-slate-300 transition">
                    <input type="checkbox" name="watermark_enabled" value="1" {{ $exam->watermark_enabled ? 'checked' : '' }} class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                    <div>
                        <span class="block text-xs font-bold text-slate-900">Dynamic Forensic Canvas Watermark</span>
                        <span class="text-[11px] text-slate-500">Ticking live timestamp, student name, ID, and IP layer across view.</span>
                    </div>
                </label>

                <label class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3 cursor-pointer hover:border-slate-300 transition">
                    <input type="checkbox" name="randomize_questions" value="1" {{ $exam->randomize_questions ? 'checked' : '' }} class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                    <div>
                        <span class="block text-xs font-bold text-slate-900">Randomize Question Sequence</span>
                        <span class="text-[11px] text-slate-500">Shuffles questions uniquely per candidate attempt.</span>
                    </div>
                </label>

                <label class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3 cursor-pointer hover:border-slate-300 transition">
                    <input type="checkbox" name="randomize_options" value="1" {{ $exam->randomize_options ? 'checked' : '' }} class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                    <div>
                        <span class="block text-xs font-bold text-slate-900">Randomize Answer Options</span>
                        <span class="text-[11px] text-slate-500">Shuffles multiple choice order per question.</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Publishing Status -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm flex items-center justify-between">
            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" {{ $exam->is_published ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20">
                    <div>
                        <span class="block text-xs font-bold text-slate-900">Publish Examination on Student Portal</span>
                        <span class="text-[11px] text-slate-500">Candidates can enter waiting room once published.</span>
                    </div>
                </label>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('admin.exams.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-medium">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm transition">
                    Update Examination Settings &rarr;
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
