@extends('layouts.admin')

@section('title', 'Leads & Inquiries')
@section('page_title', 'Inquiries & Form Submissions')
@section('page_subtitle', 'Manage candidate support inquiries, feedback, and website leads')

@section('topbar_actions')
    <a href="{{ route('admin.forms.export') }}" class="btn-secondary">
        <x-icon name="download" class="w-3.5 h-3.5 text-emerald-600" />
        <span>Export CSV</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Filters & Search Bar -->
    <div class="card p-3 flex flex-col md:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto text-xs font-semibold">
            @php
                $statusTabs = [
                    '' => ['label' => 'All', 'count' => $counts['all']],
                    'new' => ['label' => 'New', 'count' => $counts['new']],
                    'reviewed' => ['label' => 'Reviewed', 'count' => $counts['reviewed']],
                    'contacted' => ['label' => 'Contacted', 'count' => $counts['contacted']],
                    'closed' => ['label' => 'Closed', 'count' => $counts['closed']],
                ];
            @endphp
            @foreach($statusTabs as $tabKey => $tabData)
                @php
                    $isActive = ($status === $tabKey) || (empty($status) && $tabKey === '');
                @endphp
                <a href="{{ empty($tabKey) ? route('admin.forms.index') : route('admin.forms.index', ['status' => $tabKey]) }}"
                   class="inline-flex items-center gap-1.5 h-7 px-2.5 rounded-lg text-xs font-semibold transition {{ $isActive ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <span>{{ $tabData['label'] }}</span>
                    <span class="num text-[10.5px] opacity-80">({{ $tabData['count'] }})</span>
                </a>
            @endforeach
        </div>

        <form action="{{ route('admin.forms.index') }}" method="GET" class="w-full md:w-72">
            @if(!empty($status))
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search inquiries..." class="input pr-8">
                <x-icon name="search" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
            </div>
        </form>
    </div>

    <!-- Leads Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Sender / Candidate</th>
                        <th>Subject & Message</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $lead)
                        <tr>
                            <td>
                                <div class="font-semibold text-slate-900">{{ $lead->student_or_lead_name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $lead->email }}</div>
                            </td>
                            <td>
                                <div class="font-medium text-slate-900">{{ $lead->subject ?? 'General Inquiry' }}</div>
                                <p class="text-[11px] text-slate-500 truncate max-w-sm">{{ $lead->message }}</p>
                            </td>
                            <td>
                                <span class="token-chip">
                                    {{ $lead->form_name }}
                                </span>
                            </td>
                            <td>
                                @if($lead->status === 'new')
                                    <span class="pill-indigo">New</span>
                                @elseif($lead->status === 'reviewed')
                                    <span class="pill-amber">Reviewed</span>
                                @elseif($lead->status === 'contacted')
                                    <span class="pill-emerald">Contacted</span>
                                @else
                                    <span class="pill-slate">Closed</span>
                                @endif
                            </td>
                            <td class="num text-slate-500 whitespace-nowrap">
                                {{ $lead->created_at->diffForHumans() }}
                            </td>
                            <td class="text-right">
                                <div class="icon-group">
                                    <x-icon-button :href="route('admin.forms.show', $lead)" icon="eye" label="View inquiry" tone="primary" />
                                    <form action="{{ route('admin.forms.destroy', $lead) }}" method="POST" class="inline" onsubmit="return confirm('Delete this inquiry?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-icon-button type="submit" icon="trash-2" label="Delete inquiry" tone="danger" />
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400">No submissions recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($submissions->hasPages())
            <div class="px-5 py-3 border-t border-slate-100">{{ $submissions->links() }}</div>
        @endif
    </div>

</div>
@endsection
