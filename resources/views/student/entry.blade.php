@extends('layouts.student')

@section('title', 'Examination Entry: ' . $exam->title)

@section('content')
<div class="min-h-full flex items-center justify-center p-6 relative overflow-y-auto bg-slate-50">

    <div class="max-w-xl w-full relative z-10 py-10">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-xl bg-indigo-600 flex items-center justify-center text-white mx-auto mb-4 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200/80 font-mono text-xs font-semibold mb-2">
                {{ $exam->course_code ?? 'EXAMINATION' }}
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $exam->title }}</h1>
            <p class="text-xs text-slate-500 mt-1">Enterprise Proctored Assessment Portal</p>
        </div>

        <!-- Entry Card -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-8 shadow-[0_2px_8px_rgba(0,0,0,0.04)] space-y-6">

            <!-- Examination Parameters Grid -->
            <div class="grid grid-cols-3 gap-3 text-center">
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                    <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-medium">Duration</span>
                    <span class="text-sm font-bold text-slate-900">{{ $exam->duration_minutes }} Mins</span>
                </div>
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                    <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-medium">Passing Grade</span>
                    <span class="text-sm font-bold text-emerald-700">{{ $exam->pass_percentage }}%</span>
                </div>
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                    <span class="text-[10px] text-slate-500 uppercase tracking-wider block font-medium">Strike Limit</span>
                    <span class="text-sm font-bold text-rose-700">{{ $exam->max_strikes }} Allowed</span>
                </div>
            </div>

            @if($exam->instructions)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-xs text-slate-600 space-y-1">
                    <strong class="text-slate-900 block font-semibold mb-1">Candidate Instructions:</strong>
                    <p class="leading-relaxed whitespace-pre-line text-slate-600">{{ $exam->instructions }}</p>
                </div>
            @endif

            <!-- Anti-Cheat Notice Warning -->
            <div class="p-4 rounded-xl bg-indigo-50/70 border border-indigo-200/80 flex items-start gap-3">
                <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-xs text-slate-700">
                    <p class="font-bold text-slate-900">Active Proctoring Protocol Enforced:</p>
                    <p class="text-[11px] text-slate-600 mt-0.5 leading-relaxed">Questions and code are drawn into canvas pixel buffers. Fullscreen is mandatory. Exiting fullscreen, switching tabs, or opening DevTools registers security strikes.</p>
                </div>
            </div>

            <!-- Candidate Login Form -->
            <form action="{{ route('student.enter', $exam) }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Official Student ID <span class="text-rose-500">*</span></label>
                    <input type="text" name="student_id_code" value="{{ old('student_id_code') }}" required placeholder="e.g. STU-2026-9042" autofocus
                        class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 font-mono uppercase transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Full Legal Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="student_name" value="{{ old('student_name') }}" required placeholder="Your full name"
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Student Email Address <span class="text-rose-500">*</span></label>
                        <input type="email" name="student_email" value="{{ old('student_email') }}" required placeholder="student@university.edu"
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition">
                    </div>
                </div>

                @if(!empty($exam->access_passcode))
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Examination Passcode <span class="text-rose-500">*</span></label>
                        <input type="password" name="passcode" required placeholder="Enter passcode provided by proctor..."
                            class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 font-mono transition">
                    </div>
                @endif

                <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm hover:shadow-md active:scale-95 transition-all duration-200 flex items-center justify-center gap-2">
                    Enter Synchronized Waiting Room &rarr;
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
