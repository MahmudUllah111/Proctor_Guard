@extends('layouts.admin')

@section('title', 'Live Proctoring Ledger')
@section('page_title', 'Live Anti-Cheat Proctoring Ledger')
@section('page_subtitle', 'Real-time telemetry stream, infraction audits, and remote proctor intervention')

@section('topbar_actions')
    <div class="flex items-center gap-3">
        <span class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-800 border border-slate-700 text-xs text-slate-300">
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Telemetry Stream Active (3s)
        </span>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Filters & Stats Bar -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 flex flex-col md:flex-row items-center justify-between gap-4 shadow-xl">
        <form action="{{ route('admin.proctoring.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <select name="exam_id" onchange="this.form.submit()" class="px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Examinations</option>
                @foreach($exams as $ex)
                    <option value="{{ $ex->id }}" {{ $selectedExamId == $ex->id ? 'selected' : '' }}>{{ $ex->course_code ?? 'EXAM' }} - {{ $ex->title }}</option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()" class="px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Statuses</option>
                <option value="in_progress" {{ $selectedStatus == 'in_progress' ? 'selected' : '' }}>Active In Exam</option>
                <option value="waiting_room" {{ $selectedStatus == 'waiting_room' ? 'selected' : '' }}>Waiting Room</option>
                <option value="terminated_cheat" {{ $selectedStatus == 'terminated_cheat' ? 'selected' : '' }}>Terminated (Cheat)</option>
                <option value="completed" {{ $selectedStatus == 'completed' ? 'selected' : '' }}>Completed / Submitted</option>
            </select>
        </form>

        <div class="flex items-center gap-4 text-xs font-semibold">
            <span class="text-slate-400">Showing <span class="text-white">{{ $sessions->total() }}</span> student candidate attempts</span>
        </div>
    </div>

    <!-- Live Telemetry Ledger Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-950/60 text-slate-400 border-b border-slate-800">
                        <th class="py-4 px-6 font-semibold">Candidate</th>
                        <th class="py-4 px-4 font-semibold">Examination</th>
                        <th class="py-4 px-4 font-semibold">Live Status</th>
                        <th class="py-4 px-4 font-semibold">Infraction Strikes</th>
                        <th class="py-4 px-4 font-semibold">IP Address</th>
                        <th class="py-4 px-4 font-semibold">Time Remaining</th>
                        <th class="py-4 px-6 font-semibold text-right">Proctor Actions</th>
                    </tr>
                </thead>
                <tbody id="proctor-ledger-tbody" class="divide-y divide-slate-800/60">
                    @forelse($sessions as $sess)
                        <tr id="session-row-{{ $sess->id }}" class="hover:bg-slate-800/30 transition">
                            <td class="py-4 px-6 font-semibold text-white">
                                <div class="flex items-center gap-3">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $sess->last_heartbeat_at && \Carbon\Carbon::parse($sess->last_heartbeat_at)->diffInSeconds(now()) < 30 ? 'bg-emerald-400 animate-pulse' : 'bg-slate-600' }}" title="Live Heartbeat Status"></span>
                                    <div>
                                        <div class="text-white font-bold">{{ $sess->student_name }}</div>
                                        <div class="text-[11px] font-mono text-indigo-300">{{ $sess->student_id_code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-slate-300">
                                <div class="font-medium text-white">{{ $sess->exam->title ?? 'N/A' }}</div>
                                <div class="text-[10px] text-slate-500 font-mono">{{ $sess->exam->course_code ?? 'EXAM' }}</div>
                            </td>
                            <td class="py-4 px-4">
                                @if($sess->status === 'terminated_cheat')
                                    <span class="px-2.5 py-1 rounded-full bg-rose-500/20 text-rose-400 font-bold text-[10px] border border-rose-500/30">
                                        Terminated (Cheat)
                                    </span>
                                @elseif($sess->status === 'completed')
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400 font-bold text-[10px] border border-emerald-500/30">
                                        Submitted
                                    </span>
                                @elseif($sess->status === 'in_progress')
                                    <span class="px-2.5 py-1 rounded-full bg-indigo-500/20 text-indigo-300 font-bold text-[10px] border border-indigo-500/30 flex items-center gap-1.5 w-max">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                                        Active Test
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-300 font-bold text-[10px] border border-amber-500/30">
                                        Waiting Room
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 font-mono">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $sess->strikes_count >= $sess->max_strikes ? 'bg-rose-600 text-white animate-pulse' : ($sess->strikes_count > 0 ? 'bg-amber-500 text-white' : 'bg-slate-800 text-slate-400') }}">
                                        {{ $sess->strikes_count }} / {{ $sess->max_strikes }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-4 font-mono text-slate-400 text-xs">
                                {{ $sess->ip_address ?? '127.0.0.1' }}
                            </td>
                            <td class="py-4 px-4 font-mono text-xs text-white">
                                @if($sess->status === 'in_progress')
                                    @php
                                        $rem = $sess->remainingSeconds();
                                        $h = floor($rem / 3600);
                                        $m = floor(($rem % 3600) / 60);
                                        $s = $rem % 60;
                                    @endphp
                                    <span class="{{ $rem < 300 ? 'text-rose-400 font-bold animate-pulse' : 'text-slate-300' }}">
                                        {{ sprintf('%02d:%02d:%02d', $h, $m, $s) }}
                                    </span>
                                @else
                                    <span class="text-slate-500">--:--:--</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <button onclick="openInfractionModal({{ $sess->id }})" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-semibold text-xs border border-slate-700 transition">
                                    Audit Logs ({{ $sess->infractions->count() }})
                                </button>

                                @if($sess->status === 'in_progress')
                                    <button onclick="terminateCandidate({{ $sess->id }}, '{{ addslashes($sess->student_name) }}')" class="px-3 py-1.5 rounded-lg bg-rose-600/20 hover:bg-rose-600 text-rose-300 hover:text-white font-semibold text-xs border border-rose-500/30 transition">
                                        Force Terminate
                                    </button>
                                @endif

                                @if($sess->strikes_count > 0)
                                    <button onclick="resetStrikes({{ $sess->id }}, '{{ addslashes($sess->student_name) }}')" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-400 hover:text-amber-300 font-semibold text-xs border border-slate-700 transition" title="Clear strikes for genuine hardware accident">
                                        Reset Strikes
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                No candidate examination attempts currently matching filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">{{ $sessions->links() }}</div>
    </div>

</div>

<!-- Infractions Audit Drawer / Modal -->
<div id="infraction-modal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-2xl w-full shadow-2xl flex flex-col max-h-[85vh] animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Proctoring Infraction Audit Ledger
                </h3>
                <p id="modal-candidate-subtitle" class="text-xs text-slate-400 mt-0.5">Candidate: Loading...</p>
            </div>
            <button onclick="closeInfractionModal()" class="text-slate-400 hover:text-white text-lg">&times;</button>
        </div>

        <!-- Infraction list -->
        <div id="modal-infractions-body" class="flex-1 overflow-y-auto py-4 space-y-3">
            <p class="text-xs text-slate-500 text-center py-8">Fetching forensic logs...</p>
        </div>

        <div class="pt-4 border-t border-slate-800 flex justify-end">
            <button onclick="closeInfractionModal()" class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-semibold">Close Audit</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    async function openInfractionModal(sessionId) {
        const modal = document.getElementById('infraction-modal');
        const body = document.getElementById('modal-infractions-body');
        const subtitle = document.getElementById('modal-candidate-subtitle');

        modal.classList.remove('hidden');
        body.innerHTML = '<p class="text-xs text-slate-400 text-center py-8">Loading infractions ledger...</p>';

        try {
            const res = await fetch(`/admin/proctoring/${sessionId}/infractions`);
            const data = await res.json();

            subtitle.innerText = `${data.session.student_name} (${data.session.student_id}) • Exam: ${data.session.exam_title} • Total Strikes: ${data.session.strikes_count}/${data.session.max_strikes}`;

            if (data.infractions.length === 0) {
                body.innerHTML = `
                <div class="py-12 text-center text-slate-500">
                    <svg class="w-10 h-10 text-emerald-500/50 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-xs font-semibold text-slate-300">Clean Session Record</p>
                    <p class="text-[11px] text-slate-500">No security infractions or focus violations recorded.</p>
                </div>`;
                return;
            }

            body.innerHTML = data.infractions.map(inf => `
                <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 flex items-start gap-3">
                    <span class="w-7 h-7 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center font-bold text-xs flex-shrink-0">
                        ${inf.strike_number}
                    </span>
                    <div class="flex-1 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white">${inf.type}</span>
                            <span class="text-[10px] text-slate-400 font-mono">${inf.timestamp}</span>
                        </div>
                        <p class="text-slate-300 text-[11px] mt-1">${inf.details || 'Integrity perimeter violated.'}</p>
                        <div class="mt-2 flex items-center gap-3 text-[10px] text-slate-500 font-mono">
                            <span>Severity: <strong class="text-rose-400">${inf.severity.toUpperCase()}</strong></span>
                            <span>•</span>
                            <span>IP: ${inf.ip_address || '127.0.0.1'}</span>
                        </div>
                    </div>
                </div>
            `).join('');
        } catch (e) {
            body.innerHTML = '<p class="text-xs text-rose-400 text-center py-8">Failed to load infraction audit records.</p>';
        }
    }

    function closeInfractionModal() {
        document.getElementById('infraction-modal').classList.add('hidden');
    }

    async function terminateCandidate(sessionId, name) {
        if (confirm(`Are you sure you want to FORCE TERMINATE ${name}'s exam for cheating?\n\nTheir exam will be scored at 0% and an incident alert will be dispatched.`)) {
            const res = await fetch(`/admin/proctoring/${sessionId}/terminate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reason: 'Disqualified manually by exam proctor administrator.' })
            });
            window.location.reload();
        }
    }

    async function resetStrikes(sessionId, name) {
        if (confirm(`Reset all infraction strikes for ${name} back to zero?`)) {
            await fetch(`/admin/proctoring/${sessionId}/reset-strikes`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            window.location.reload();
        }
    }

    // Auto-refresh telemetry feed every 4 seconds
    setInterval(async () => {
        try {
            const res = await fetch('{{ route("admin.proctoring.feed") }}?exam_id={{ $selectedExamId }}');
            const data = await res.json();
            // Telemetry heartbeat poll is working in background
        } catch (e) {}
    }, 4000);
</script>
@endpush
