@extends('layouts.admin')

@section('title', 'Exam Builder')
@section('page_title', 'Examination Manager')
@section('page_subtitle', 'Configure examinations, question sets, proctoring strictness and release')

@section('topbar_actions')
    <a href="{{ route('admin.exams.create') }}" class="btn-primary"><x-icon name="plus" class="w-3.5 h-3.5" />New exam</a>
@endsection

@section('content')
<form action="{{ route('admin.exams.index') }}" method="GET" class="flex items-center justify-between gap-4">
    <div class="relative w-full max-w-sm">
        <x-icon name="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
        <input type="search" name="search" value="{{ $search }}" placeholder="Search title or course code" aria-label="Search exams" class="input pl-9">
    </div>
    <span class="text-xs text-slate-500 whitespace-nowrap"><span class="num font-semibold text-slate-900">{{ $exams->total() }}</span> {{ Str::plural('exam', $exams->total()) }}</span>
</form>

<div class="space-y-3">
    @forelse($exams as $exam)
        @php
            $stats = [
                ['Duration', $exam->duration_minutes . ' min', 'text-slate-900'],
                ['Pass mark', rtrim(rtrim(number_format($exam->pass_percentage, 2), '0'), '.') . '%', 'text-slate-900'],
                ['Max strikes', $exam->max_strikes, 'text-rose-600'],
                ['Questions', $exam->questions_count, 'text-indigo-600'],
            ];
        @endphp
        <article class="card p-5 grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_320px_auto] gap-5 items-center">
            <div class="min-w-0">
                <div class="flex items-center gap-2 mb-2">
                    <span class="pill-slate font-mono">{{ $exam->course_code ?? 'EXAM' }}</span>
                    <span class="{{ $exam->is_published ? 'pill-slate' : 'pill-amber' }}">{{ $exam->is_published ? 'Published' : 'Draft' }}</span>
                    <form action="{{ route('admin.exams.toggle-release', $exam) }}" method="POST">
                        @csrf
                        <button type="submit" title="{{ $exam->is_released ? 'Click to lock the waiting room' : 'Click to release questions' }}"
                            class="{{ $exam->is_released ? 'pill-emerald hover:bg-emerald-100' : 'pill-amber hover:bg-amber-100' }} transition">
                            <span class="w-1.5 h-1.5 rounded-full {{ $exam->is_released ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' }}"></span>
                            {{ $exam->is_released ? 'Released' : 'Waiting room locked' }}
                        </button>
                    </form>
                </div>
                <h2 class="text-sm font-semibold text-slate-900 tracking-tight">{{ $exam->title }}</h2>
                <p class="mt-1 text-xs text-slate-500 line-clamp-2 leading-relaxed">{{ $exam->description ?: 'No description provided.' }}</p>
            </div>

            <dl class="grid grid-cols-2 gap-px rounded-lg border border-slate-200 bg-slate-200 overflow-hidden">
                @foreach($stats as [$label, $value, $tone])
                    <div class="bg-white px-3 py-2">
                        <dt class="text-[10.5px] font-medium uppercase tracking-[0.06em] text-slate-400">{{ $label }}</dt>
                        <dd class="mt-0.5 text-[13px] font-semibold num {{ $tone }}">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="flex lg:flex-col items-center lg:items-end gap-2">
                <a href="{{ route('admin.exams.questions.index', $exam) }}" class="btn-primary"><x-icon name="list-checks" class="w-3.5 h-3.5" />Manage questions</a>
                <div class="icon-group">
                    <x-icon-button :href="route('admin.exams.edit', $exam)" icon="pencil" label="Edit settings" tone="primary" />
                    <x-icon-button :href="route('admin.exams.report', $exam)" icon="chart-column" label="Audit report" tone="primary" />
                    <x-icon-button :href="route('student.entry', $exam)" target="_blank" rel="noopener" icon="external-link" label="Open candidate URL" />
                </div>
            </div>
        </article>
    @empty
        <div class="card py-16 text-center">
            <p class="text-sm font-semibold text-slate-900">No examinations found</p>
            <p class="text-xs text-slate-500 mt-1 mb-4">{{ $search ? 'Try a different search.' : 'Create your first exam to get started.' }}</p>
            <a href="{{ route('admin.exams.create') }}" class="btn-primary"><x-icon name="plus" class="w-3.5 h-3.5" />New exam</a>
        </div>
    @endforelse
</div>

{{ $exams->links() }}
@endsection
