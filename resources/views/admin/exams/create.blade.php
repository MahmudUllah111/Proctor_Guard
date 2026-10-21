@extends('layouts.admin')

@section('title', 'Create Examination')
@section('page_title', 'Build New Examination')
@section('page_subtitle', 'Set parameters, duration, pass threshold, and anti-cheat proctoring strictness')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.exams.store') }}" method="POST" class="space-y-8">
        @csrf

        <!-- General Info Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-5">
            <h3 class="font-bold text-sm text-white border-b border-slate-800 pb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                General Examination Metadata
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-2">Exam Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. CSC 455 Advanced Web Development Final Exam" required
                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-2">Course Code</label>
                    <input type="text" name="course_code" value="{{ old('course_code') }}" placeholder="e.g. CSC 455"
                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-2">Description / Objectives</label>
                <textarea name="description" rows="3" placeholder="Brief summary of the topics covered in this examination..."
                    class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-2">Candidate Instructions (Shown in Waiting Room)</label>
                <textarea name="instructions" rows="3" placeholder="Candidate rules, permitted reference materials, time restrictions..."
                    class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('instructions') }}</textarea>
            </div>
        </div>

        <!-- Parameters & Scoring Card -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-5">
            <h3 class="font-bold text-sm text-white border-b border-slate-800 pb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Timing, Pass Criteria & Access
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-2">Duration (Minutes) <span class="text-rose-500">*</span></label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 45) }}" min="1" max="600" required
                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-2">Passing Grade (%) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.1" name="pass_percentage" value="{{ old('pass_percentage', 70) }}" min="0" max="100" required
                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-2">Max Infraction Strikes <span class="text-rose-500">*</span></label>
                    <input type="number" name="max_strikes" value="{{ old('max_strikes', 3) }}" min="1" max="20" required
                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-2">Access Passcode (Optional)</label>
                    <input type="text" name="access_passcode" value="{{ old('access_passcode') }}" placeholder="Leave blank if open"
                        class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- Anti-Cheat Proctoring Engine Configuration -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-5">
            <h3 class="font-bold text-sm text-white border-b border-slate-800 pb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Anti-Cheat Proctoring Engine & Canvas Security
            </h3>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-2">Enforcement Strictness Mode</label>
                <select name="anti_cheat_strictness" class="w-full md:w-72 px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="strict" selected>Strict (Full Canvas + Immediate Auto-Lockout)</option>
                    <option value="medium">Medium (Warnings + High Threshold)</option>
                    <option value="lenient">Lenient (Telemetry Logging Only)</option>
                </select>
            </div>

            <!-- Checkbox matrix -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <label class="p-4 rounded-xl bg-slate-950 border border-slate-800 flex items-start gap-3 cursor-pointer hover:border-slate-700 transition">
                    <input type="checkbox" name="enforce_fullscreen" value="1" checked class="mt-1 rounded bg-slate-800 border-slate-700 text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <span class="block text-xs font-bold text-white">Enforce Fullscreen Examination</span>
                        <span class="text-[11px] text-slate-400">Forces full screen on start; flags warning strike on exit.</span>
                    </div>
                </label>

                <label class="p-4 rounded-xl bg-slate-950 border border-slate-800 flex items-start gap-3 cursor-pointer hover:border-slate-700 transition">
                    <input type="checkbox" name="block_clipboard" value="1" checked class="mt-1 rounded bg-slate-800 border-slate-700 text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <span class="block text-xs font-bold text-white">Block Clipboard & Context Menu</span>
                        <span class="text-[11px] text-slate-400">Disables right-click, text selection, copy, cut, and paste.</span>
                    </div>
                </label>

                <label class="p-4 rounded-xl bg-slate-950 border border-slate-800 flex items-start gap-3 cursor-pointer hover:border-slate-700 transition">
                    <input type="checkbox" name="block_devtools" value="1" checked class="mt-1 rounded bg-slate-800 border-slate-700 text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <span class="block text-xs font-bold text-white">Detect DevTools & Inspect Shortcuts</span>
                        <span class="text-[11px] text-slate-400">Blocks F12, Ctrl+Shift+I/J/C; detects docked inspection windows.</span>
                    </div>
                </label>

                <label class="p-4 rounded-xl bg-slate-950 border border-slate-800 flex items-start gap-3 cursor-pointer hover:border-slate-700 transition">
                    <input type="checkbox" name="watermark_enabled" value="1" checked class="mt-1 rounded bg-slate-800 border-slate-700 text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <span class="block text-xs font-bold text-white">Dynamic Forensic Canvas Watermark</span>
                        <span class="text-[11px] text-slate-400">Ticking live timestamp, student name, ID, and IP layer across view.</span>
                    </div>
                </label>

                <label class="p-4 rounded-xl bg-slate-950 border border-slate-800 flex items-start gap-3 cursor-pointer hover:border-slate-700 transition">
                    <input type="checkbox" name="randomize_questions" value="1" checked class="mt-1 rounded bg-slate-800 border-slate-700 text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <span class="block text-xs font-bold text-white">Randomize Question Sequence</span>
                        <span class="text-[11px] text-slate-400">Shuffles questions uniquely per candidate attempt.</span>
                    </div>
                </label>

                <label class="p-4 rounded-xl bg-slate-950 border border-slate-800 flex items-start gap-3 cursor-pointer hover:border-slate-700 transition">
                    <input type="checkbox" name="randomize_options" value="1" checked class="mt-1 rounded bg-slate-800 border-slate-700 text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <span class="block text-xs font-bold text-white">Randomize Answer Options</span>
                        <span class="text-[11px] text-slate-400">Shuffles multiple choice order per question.</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Publishing Status -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl flex items-center justify-between">
            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" checked class="rounded bg-slate-800 border-slate-700 text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <span class="block text-xs font-bold text-white">Publish Examination on Student Portal</span>
                        <span class="text-[11px] text-slate-400">Candidates can enter waiting room once published.</span>
                    </div>
                </label>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('admin.exams.index') }}" class="text-xs text-slate-400 hover:text-white">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 transition">
                    Save Exam & Add Questions &rarr;
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
