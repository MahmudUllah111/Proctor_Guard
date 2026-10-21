@extends('layouts.admin')

@section('title', 'Live Proctoring')
@section('page_title', 'Live Anti-Cheat Proctoring Ledger')
@section('page_subtitle', 'Real-time telemetry, infraction audits and proctor intervention')

@section('topbar_actions')
    <span class="btn-secondary pointer-events-none" aria-live="polite">
        <span class="relative flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
        </span>
        <span id="live-count" class="num">{{ $liveSessionCount }}</span> live
    </span>
@endsection

@section('content')
@php
    $statuses = ['in_progress' => 'Active test', 'waiting_room' => 'Waiting room', 'terminated_cheat' => 'Terminated (Cheat)', 'completed' => 'Submitted'];
@endphp

<section class="card overflow-hidden">
    <form action="{{ route('admin.proctoring.index') }}" method="GET" class="card-header flex-wrap">
        <div class="flex flex-wrap items-center gap-2">
            <x-select name="exam_id" onchange="this.form.submit()" aria-label="Filter by examination" wrapper-class="w-72">
                <option value="">All examinations</option>
                @foreach($exams as $ex)
                    <option value="{{ $ex->id }}" @selected($selectedExamId == $ex->id)>{{ $ex->course_code ?? 'EXAM' }} — {{ $ex->title }}</option>
                @endforeach
            </x-select>
            <x-select name="status" onchange="this.form.submit()" aria-label="Filter by status" wrapper-class="w-48">
                <option value="">All statuses</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" @selected($selectedStatus == $value)>{{ $label }}</option>
                @endforeach
            </x-select>
            @if($selectedExamId || $selectedStatus)
                <a href="{{ route('admin.proctoring.index') }}" class="btn-ghost">Clear</a>
            @endif
        </div>
        <span class="text-xs text-slate-500"><span class="num font-semibold text-slate-900">{{ number_format($sessions->total()) }}</span> attempts</span>
    </form>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Examination</th>
                    <th>Status</th>
                    <th class="text-right">Strikes</th>
                    <th>IP address</th>
                    <th class="text-right">Remaining</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $sess)
                    @php $strikeTone = $sess->strikes_count >= $sess->max_strikes ? 'text-rose-600 font-semibold' : ($sess->strikes_count > 0 ? 'text-amber-700 font-semibold' : 'text-slate-400'); @endphp
                    <tr id="session-row-{{ $sess->id }}">
                        <td>
                            <div class="flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full shrink-0 {{ $sess->isOnline() ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}" title="{{ $sess->isOnline() ? 'Heartbeat live' : 'No recent heartbeat' }}"></span>
                                <div class="min-w-0">
                                    <p class="font-medium text-slate-900 whitespace-nowrap">{{ $sess->student_name }}</p>
                                    <p class="font-mono text-[11px] text-slate-500">{{ $sess->student_id_code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="max-w-[280px]">
                            <p class="text-slate-900 truncate" title="{{ $sess->exam->title ?? '' }}">{{ $sess->exam->title ?? '—' }}</p>
                            <p class="font-mono text-[11px] text-slate-400">{{ $sess->exam->course_code ?? '' }}</p>
                        </td>
                        <td><span class="{{ $sess->status_pill }}">{{ $sess->status_label }}</span></td>
                        <td class="text-right num {{ $strikeTone }}">{{ $sess->strikes_count }}<span class="text-slate-300">/</span>{{ $sess->max_strikes }}</td>
                        <td class="num text-slate-500">{{ $sess->ip_address ?? '—' }}</td>
                        <td class="text-right num">
                            @if($sess->status === 'in_progress')
                                @php $rem = $sess->remainingSeconds(); @endphp
                                <span class="{{ $rem < 300 ? 'text-rose-600 font-semibold' : 'text-slate-700' }}">{{ gmdate('H:i:s', $rem) }}</span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="icon-group w-full">
                                @if($sess->strikes_count > 0)
                                    <x-icon-button icon="rotate-ccw" label="Reset strikes" tone="warning" onclick="resetStrikes({{ $sess->id }}, @js($sess->student_name))" />
                                @else
                                    <span class="w-8 h-8" aria-hidden="true"></span>
                                @endif
                                @if($sess->status === 'in_progress')
                                    <x-icon-button icon="shield-alert" label="Force terminate" tone="danger" onclick="terminateCandidate({{ $sess->id }}, @js($sess->student_name))" />
                                @else
                                    <span class="w-8 h-8" aria-hidden="true"></span>
                                @endif
                                <x-icon-button icon="eye" label="View audit log ({{ $sess->infractions->count() }})" tone="primary" onclick="openInfractionModal({{ $sess->id }})" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-12 text-center text-slate-400">No attempts match these filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($sessions->hasPages())
        <div class="px-5 py-3 border-t border-slate-100">{{ $sessions->links() }}</div>
    @endif
</section>

<!-- Infraction audit drawer -->
<div id="infraction-modal" class="fixed inset-0 z-50 hidden bg-slate-900/30 backdrop-blur-[2px] flex justify-end transition-opacity duration-300 opacity-0" onclick="if(event.target === this) closeInfractionModal()">
    <div id="infraction-drawer-panel" role="dialog" aria-modal="true" aria-labelledby="drawer-title" class="bg-white border-l border-slate-200 h-full max-w-md w-full shadow-elevation flex flex-col transform translate-x-full transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)]">
        <div class="h-16 px-5 border-b border-slate-100 flex items-center justify-between gap-3">
            <div class="min-w-0">
                <h3 id="drawer-title" class="card-title">Infraction audit log</h3>
                <p id="modal-candidate-subtitle" class="text-xs text-slate-500 truncate">Loading…</p>
            </div>
            <x-icon-button icon="x" label="Close" onclick="closeInfractionModal()" />
        </div>
        <ol id="modal-infractions-body" class="flex-1 overflow-y-auto px-5 py-5 space-y-4"></ol>
        <div class="px-5 py-3 border-t border-slate-100 text-[11px] text-slate-400">Timestamps are server-verified.</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    // Infraction details originate from the candidate's browser: always escape before rendering.
    const esc = (v) => String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

    async function openInfractionModal(sessionId) {
        const modal = document.getElementById('infraction-modal');
        const drawer = document.getElementById('infraction-drawer-panel');
        const body = document.getElementById('modal-infractions-body');
        const subtitle = document.getElementById('modal-candidate-subtitle');

        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            modal.classList.replace('opacity-0', 'opacity-100');
            drawer.classList.replace('translate-x-full', 'translate-x-0');
        });

        body.innerHTML = '<li class="text-xs text-slate-400 text-center py-8">Loading…</li>';

        try {
            const res = await fetch(`{{ url('admin/proctoring') }}/${sessionId}/infractions`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            const s = data.session;
            subtitle.textContent = `${s.student_name} · ${s.student_id} · ${s.strikes_count}/${s.max_strikes} strikes`;

            if (data.infractions.length === 0) {
                body.innerHTML = '<li class="py-12 text-center text-xs text-slate-500">Clean record — no infractions logged.</li>';
                return;
            }

            body.innerHTML = data.infractions.map(inf => {
                const severe = ['high', 'critical'].includes(String(inf.severity).toLowerCase());
                return `
                <li class="border-l-2 ${severe ? 'border-rose-400' : 'border-amber-300'} pl-3">
                    <div class="flex items-baseline justify-between gap-3">
                        <span class="text-xs font-semibold text-slate-900">${esc(inf.type)}</span>
                        <span class="text-[11px] text-slate-400 font-mono tabular-nums whitespace-nowrap">${esc(inf.timestamp)}</span>
                    </div>
                    <p class="text-xs text-slate-600 mt-0.5">${esc(inf.details || 'Integrity perimeter violated.')}</p>
                    <dl class="mt-1.5 flex items-center gap-3 text-[11px] text-slate-400 font-mono tabular-nums">
                        <div>Strike <span class="text-slate-700">${esc(inf.strike_number)}</span></div>
                        <div class="uppercase ${severe ? 'text-rose-600' : 'text-amber-700'}">${esc(inf.severity)}</div>
                        <div>${esc(inf.ip_address || '—')}</div>
                    </dl>
                </li>`;
            }).join('');
        } catch (e) {
            body.innerHTML = '<li class="text-xs text-rose-700 text-center py-8">Failed to load audit records.</li>';
        }
    }

    function closeInfractionModal() {
        const modal = document.getElementById('infraction-modal');
        const drawer = document.getElementById('infraction-drawer-panel');
        drawer.classList.replace('translate-x-0', 'translate-x-full');
        modal.classList.replace('opacity-100', 'opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeInfractionModal(); });

    async function postAction(sessionId, action, body = {}) {
        await fetch(`{{ url('admin/proctoring') }}/${sessionId}/${action}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(body)
        });
        window.location.reload();
    }

    function terminateCandidate(sessionId, name) {
        if (confirm(`Force terminate ${name}'s exam?\n\nThe attempt will be scored at 0% and an incident alert dispatched.`)) {
            postAction(sessionId, 'terminate', { reason: 'Disqualified manually by exam proctor administrator.' });
        }
    }

    function resetStrikes(sessionId, name) {
        if (confirm(`Reset all infraction strikes for ${name} back to zero?`)) {
            postAction(sessionId, 'reset-strikes');
        }
    }

    // Keep the live counter in sync with the telemetry feed.
    setInterval(async () => {
        try {
            const res = await fetch('{{ route('admin.proctoring.feed', ['exam_id' => $selectedExamId]) }}', { headers: { 'Accept': 'application/json' } });
            document.getElementById('live-count').textContent = (await res.json()).active_count;
        } catch (e) {}
    }, 4000);
</script>
@endpush
