@extends('layouts.admin')

@section('title', 'Exam Builder')
@section('page_title', 'Examination Manager')
@section('page_subtitle', 'Configure examinations, question sets, proctoring strictness, and live release toggles')

@section('topbar_actions')
    <a href="{{ route('admin.exams.create') }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs flex items-center gap-2 shadow-lg shadow-indigo-600/20 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create New Exam
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Filter & Search Bar -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex flex-col md:flex-row items-center justify-between gap-4 shadow-xl">
        <form action="{{ route('admin.exams.index') }}" method="GET" class="flex items-center gap-3 w-full md:w-96">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by exam title or course code..."
                class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-xl border border-slate-700 transition">Search</button>
            @if($search)
                <a href="{{ route('admin.exams.index') }}" class="text-xs text-slate-400 hover:text-white">Clear</a>
            @endif
        </form>
        <div class="text-xs text-slate-400">
            Total Exams: <span class="font-bold text-white">{{ $exams->total() }}</span>
        </div>
    </div>

    <!-- Exam Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($exams as $exam)
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col justify-between hover:border-slate-700 transition">
                <div>
                    <!-- Header Badges -->
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-lg bg-indigo-500/20 text-indigo-300 font-mono text-xs font-bold">{{ $exam->course_code ?? 'EXAM' }}</span>

                        <!-- Release Toggle -->
                        <form action="{{ route('admin.exams.toggle-release', $exam) }}" method="POST">
                            @csrf
                            @if($exam->is_released)
                                <button type="submit" title="Click to freeze questions / revoke release" class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400 font-bold text-[10px] flex items-center gap-1.5 hover:bg-emerald-500/30 transition">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    Questions Released
                                </button>
                            @else
                                <button type="submit" title="Click to release questions to waiting room" class="px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-300 font-bold text-[10px] flex items-center gap-1.5 hover:bg-amber-500/30 transition">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                    Waiting Room Locked
                                </button>
                            @endif
                        </form>
                    </div>

                    <!-- Title & Description -->
                    <h3 class="font-extrabold text-base text-white mb-2 leading-snug">{{ $exam->title }}</h3>
                    <p class="text-xs text-slate-400 line-clamp-2 mb-4 leading-relaxed">{{ $exam->description ?? 'No description provided.' }}</p>

                    <!-- Parameters Pill Grid -->
                    <div class="grid grid-cols-2 gap-2 text-[11px] mb-6">
                        <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800/80">
                            <span class="text-slate-500 block">Duration</span>
                            <span class="font-bold text-white">{{ $exam->duration_minutes }} Minutes</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800/80">
                            <span class="text-slate-500 block">Pass Mark</span>
                            <span class="font-bold text-white">{{ $exam->pass_percentage }}%</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800/80">
                            <span class="text-slate-500 block">Max Strikes</span>
                            <span class="font-bold text-rose-400">{{ $exam->max_strikes }} Allowed</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-950 border border-slate-800/80">
                            <span class="text-slate-500 block">Questions</span>
                            <span class="font-bold text-indigo-400">{{ $exam->questions_count }} Items</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="pt-4 border-t border-slate-800 space-y-2">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.exams.questions.index', $exam) }}" class="flex-1 py-2 text-center bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 font-bold text-xs rounded-xl border border-indigo-500/30 transition">
                            Manage Questions ({{ $exam->questions_count }})
                        </a>
                        <a href="{{ route('admin.exams.edit', $exam) }}" class="p-2 text-slate-400 hover:text-white rounded-xl bg-slate-800 border border-slate-700 hover:bg-slate-700 transition" title="Edit Settings">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <a href="{{ route('admin.exams.report', $exam) }}" class="p-2 text-slate-400 hover:text-white rounded-xl bg-slate-800 border border-slate-700 hover:bg-slate-700 transition" title="Candidate Audit Report">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </a>
                    </div>
                    <div class="flex items-center justify-between text-[11px] pt-1">
                        <a href="{{ route('student.entry', $exam) }}" target="_blank" class="text-indigo-400 hover:text-indigo-300 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            Candidate Portal Link
                        </a>
                        <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" onsubmit="return confirm('Permanently delete this exam and all associated student attempts?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-400 hover:text-rose-300">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-slate-900 border border-slate-800 rounded-3xl">
                <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p class="text-sm font-semibold text-white mb-1">No examinations created yet</p>
                <p class="text-xs text-slate-400 mb-4">Build your first exam and configure anti-cheat proctoring rules.</p>
                <a href="{{ route('admin.exams.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg transition">Create Examination</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div>{{ $exams->links() }}</div>
</div>
@endsection
