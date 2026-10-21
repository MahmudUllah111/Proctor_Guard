@extends('layouts.admin')

@section('title', 'Inquiries & Leads')
@section('page_title', 'Inquiries & Form Submissions')
@section('page_subtitle', 'Manage candidate support inquiries, feedback, and website leads')

@section('topbar_actions')
    <a href="{{ route('admin.forms.export') }}" class="px-3.5 py-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 shadow-sm transition flex items-center gap-1.5">
        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Export CSV
    </a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Filters Bar -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-4 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto text-xs font-semibold">
            <a href="{{ route('admin.forms.index') }}" class="px-3 py-1.5 rounded-lg transition {{ empty($status) ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:text-slate-900 border border-slate-200/80' }}">
                All ({{ $counts['all'] }})
            </a>
            <a href="{{ route('admin.forms.index', ['status' => 'new']) }}" class="px-3 py-1.5 rounded-lg transition {{ $status === 'new' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:text-slate-900 border border-slate-200/80' }}">
                New ({{ $counts['new'] }})
            </a>
            <a href="{{ route('admin.forms.index', ['status' => 'reviewed']) }}" class="px-3 py-1.5 rounded-lg transition {{ $status === 'reviewed' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:text-slate-900 border border-slate-200/80' }}">
                Reviewed ({{ $counts['reviewed'] }})
            </a>
            <a href="{{ route('admin.forms.index', ['status' => 'contacted']) }}" class="px-3 py-1.5 rounded-lg transition {{ $status === 'contacted' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:text-slate-900 border border-slate-200/80' }}">
                Contacted ({{ $counts['contacted'] }})
            </a>
            <a href="{{ route('admin.forms.index', ['status' => 'closed']) }}" class="px-3 py-1.5 rounded-lg transition {{ $status === 'closed' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:text-slate-900 border border-slate-200/80' }}">
                Closed ({{ $counts['closed'] }})
            </a>
        </div>

        <form action="{{ route('admin.forms.index') }}" method="GET" class="w-full md:w-72">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search inquiries..."
                class="w-full px-3.5 py-1.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
        </form>
    </div>

    <!-- Leads Table -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <th class="py-3.5 px-6 font-semibold">Sender / Candidate</th>
                        <th class="py-3.5 px-4 font-semibold">Subject</th>
                        <th class="py-3.5 px-4 font-semibold">Form Category</th>
                        <th class="py-3.5 px-4 font-semibold">Status</th>
                        <th class="py-3.5 px-4 font-semibold">Date</th>
                        <th class="py-3.5 px-6 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($submissions as $lead)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6 font-semibold text-slate-900">
                                <div>{{ $lead->student_or_lead_name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $lead->email }}</div>
                            </td>
                            <td class="py-4 px-4 text-slate-600">
                                <span class="font-medium text-slate-900">{{ $lead->subject ?? 'General Inquiry' }}</span>
                                <p class="text-[11px] text-slate-500 truncate max-w-xs">{{ $lead->message }}</p>
                            </td>
                            <td class="py-4 px-4 font-mono text-[11px] text-indigo-700">
                                {{ $lead->form_name }}
                            </td>
                            <td class="py-4 px-4">
                                @if($lead->status === 'new')
                                    <span class="px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 font-bold text-[10px]">NEW</span>
                                @elseif($lead->status === 'reviewed')
                                    <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 font-bold text-[10px]">REVIEWED</span>
                                @elseif($lead->status === 'contacted')
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[10px]">CONTACTED</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 font-bold text-[10px]">CLOSED</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-slate-500 text-[11px]">
                                {{ $lead->created_at->diffForHumans() }}
                            </td>
                            <td class="py-4 px-6 text-right space-x-2 font-medium">
                                <a href="{{ route('admin.forms.show', $lead) }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">View</a>
                                <span class="text-slate-300">•</span>
                                <form action="{{ route('admin.forms.destroy', $lead) }}" method="POST" class="inline" onsubmit="return confirm('Delete this inquiry?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700">Delete</button>
                                </form>
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

        <div class="p-4 border-t border-slate-100">{{ $submissions->links() }}</div>
    </div>

</div>
@endsection
