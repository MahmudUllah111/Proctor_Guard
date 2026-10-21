@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Integrity & Proctoring Overview')
@section('page_subtitle', 'Live sessions, security strikes and examination outcomes')

@section('topbar_actions')
    <a href="{{ route('admin.proctoring.index') }}" class="btn-primary">
        <span class="relative flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
        </span>
        Live Proctoring
    </a>
@endsection

@section('content')
@php
    $kpis = [
        ['label' => 'Active candidates', 'value' => $activeCandidates, 'meta' => 'In waiting room or exam', 'icon' => 'users', 'tone' => 'text-slate-900'],
        ['label' => 'Security terminations', 'value' => $cheatTerminated, 'meta' => number_format($totalInfractions) . ' strikes recorded', 'icon' => 'shield-alert', 'tone' => $cheatTerminated ? 'text-rose-600' : 'text-slate-900'],
        ['label' => 'Pass rate', 'value' => $passRate . '%', 'meta' => number_format($completedExams) . ' graded submissions', 'icon' => 'circle-check', 'tone' => 'text-slate-900'],
        ['label' => 'Examinations', 'value' => $totalExams, 'meta' => number_format($pendingLeads) . ' new leads awaiting review', 'icon' => 'clipboard-list', 'tone' => 'text-slate-900'],
    ];
    $strictness = \App\Models\SystemSetting::get('default_strictness', 'strict');
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    @foreach($kpis as $kpi)
        <div class="card p-5">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-xs font-medium">{{ $kpi['label'] }}</span>
                <x-icon :name="$kpi['icon']" class="w-4 h-4 text-slate-400" />
            </div>
            <p class="mt-3 text-[28px] leading-none font-semibold tracking-tight num {{ $kpi['tone'] }}">{{ $kpi['value'] }}</p>
            <p class="mt-2 text-xs text-slate-500">{{ $kpi['meta'] }}</p>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
    <section class="card xl:col-span-2 overflow-hidden">
        <div class="card-header">
            <div>
                <h2 class="card-title">Release control</h2>
                <p class="card-subtitle">Releasing an exam opens questions to everyone in its waiting room</p>
            </div>
            <a href="{{ route('admin.exams.create') }}" class="btn-secondary"><x-icon name="plus" class="w-3.5 h-3.5" />New exam</a>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table table-fixed min-w-[680px]">
                <colgroup>
                    <col><col class="w-24"><col class="w-24"><col class="w-36"><col class="w-[120px]">
                </colgroup>
                <thead>
                    <tr>
                        <th>Examination</th>
                        <th class="text-right">Duration</th>
                        <th class="text-right">Questions</th>
                        <th>Release</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activeExams as $exam)
                        <tr>
                            <td>
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="pill-slate font-mono">{{ $exam->course_code ?? 'EXAM' }}</span>
                                    <a href="{{ route('admin.exams.edit', $exam) }}" class="font-medium text-slate-900 hover:text-indigo-600 truncate" title="{{ $exam->title }}">{{ $exam->title }}</a>
                                </div>
                            </td>
                            <td class="text-right num text-slate-700">{{ $exam->duration_minutes }}<span class="text-slate-400"> min</span></td>
                            <td class="text-right num text-slate-700">{{ $exam->questions_count }}</td>
                            <td>
                                <form action="{{ route('admin.exams.toggle-release', $exam) }}" method="POST">
                                    @csrf
                                    <button type="submit" title="{{ $exam->is_released ? 'Click to lock the waiting room' : 'Click to release questions' }}"
                                        class="{{ $exam->is_released ? 'pill-emerald hover:bg-emerald-100' : 'pill-amber hover:bg-amber-100' }} transition">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $exam->is_released ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' }}"></span>
                                        {{ $exam->is_released ? 'Released' : 'Locked' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="icon-group w-full">
                                    <x-icon-button :href="route('admin.exams.report', $exam)" icon="chart-column" label="Audit report" tone="primary" />
                                    <x-icon-button :href="route('admin.exams.edit', $exam)" icon="pencil" label="Edit exam" tone="primary" />
                                    <x-icon-button :href="route('student.entry', $exam)" target="_blank" rel="noopener" icon="external-link" label="Open candidate URL" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-10 text-center text-slate-400">No exams configured yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Default proctoring engine</span>
            <span class="pill-indigo font-mono">{{ ucfirst($strictness) }}</span>
        </div>
    </section>

    <section class="card flex flex-col">
        <div class="card-header">
            <h2 class="card-title">Recent infractions</h2>
            <a href="{{ route('admin.proctoring.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-900 inline-flex items-center gap-1">View all <x-icon name="arrow-right" class="w-3.5 h-3.5" /></a>
        </div>
        <ol class="flex-1 px-5 py-4 space-y-3">
            @forelse($recentInfractions as $inf)
                <li class="border-l-2 pl-3 {{ in_array($inf->severity, ['high', 'critical']) ? 'border-rose-400' : 'border-amber-300' }}">
                    <div class="flex items-baseline justify-between gap-3">
                        <p class="text-xs font-semibold text-slate-900 truncate">{{ $inf->examSession->student_name ?? 'Candidate' }}</p>
                        <span class="text-[11px] text-slate-400 whitespace-nowrap">{{ $inf->created_at->diffForHumans(short: true) }}</span>
                    </div>
                    <p class="text-xs text-slate-600 mt-0.5">{{ $inf->formatted_type }}</p>
                    <p class="mt-1 flex items-center gap-2 text-[11px] text-slate-400 num">
                        <span>Strike {{ $inf->strike_number }}</span>
                        <span aria-hidden="true">·</span>
                        <span>{{ $inf->ip_address ?? '—' }}</span>
                    </p>
                </li>
            @empty
                <li class="py-8 text-center text-xs text-slate-400">No infractions recorded.</li>
            @endforelse
        </ol>
    </section>
</div>

<section class="card overflow-hidden">
    <div class="card-header">
        <div>
            <h2 class="card-title">Candidate telemetry</h2>
            <p class="card-subtitle">Most recent sessions across all exams</p>
        </div>
        <a href="{{ route('admin.proctoring.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-900 inline-flex items-center gap-1">Open ledger <x-icon name="arrow-right" class="w-3.5 h-3.5" /></a>
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Student ID</th>
                    <th>Exam</th>
                    <th>Status</th>
                    <th class="text-right">Strikes</th>
                    <th class="text-right">Score</th>
                    <th class="text-right">Heartbeat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentSessions as $session)
                    <tr>
                        <td class="font-medium text-slate-900">{{ $session->student_name }}</td>
                        <td class="font-mono text-slate-600">{{ $session->student_id_code }}</td>
                        <td class="max-w-[260px] truncate">{{ $session->exam->title ?? '—' }}</td>
                        <td><span class="{{ $session->status_pill }}">{{ $session->status_label }}</span></td>
                        <td class="text-right num {{ $session->strikes_count > 0 ? 'text-rose-600 font-semibold' : 'text-slate-500' }}">{{ $session->strikes_count }}/{{ $session->max_strikes }}</td>
                        <td class="text-right num text-slate-900">{{ $session->status === 'completed' ? number_format($session->percentage, 1) . '%' : '—' }}</td>
                        <td class="text-right text-slate-500 whitespace-nowrap">{{ $session->last_heartbeat_at?->diffForHumans(short: true) ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-10 text-center text-slate-400">No candidate sessions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
