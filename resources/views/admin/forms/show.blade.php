@extends('layouts.admin')

@section('title', 'Inquiry #' . $submission->id)
@section('page_title', 'Inquiry Details: ' . $submission->student_or_lead_name)
@section('page_subtitle', 'Submitted via ' . $submission->form_name . ' • ' . $submission->created_at->toDayDateTimeString())

@section('topbar_actions')
    <a href="{{ route('admin.forms.index') }}" class="btn-secondary">
        <x-icon name="arrow-left" class="w-3.5 h-3.5" />
        <span>Back to Inquiries</span>
    </a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Detail Card -->
    <div class="card p-6 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-bold text-base text-slate-900">{{ $submission->subject ?? 'General Inquiry' }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">Category: <span class="token-chip text-indigo-700">{{ $submission->form_name }}</span></p>
            </div>
            @php
                $statusPills = [
                    'new' => 'pill-indigo',
                    'reviewed' => 'pill-amber',
                    'contacted' => 'pill-emerald',
                    'closed' => 'pill-slate',
                ];
            @endphp
            <span class="{{ $statusPills[$submission->status] ?? 'pill-slate' }}">
                {{ ucfirst($submission->status) }}
            </span>
        </div>

        <!-- Sender info -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200/70">
                <span class="text-slate-500 block mb-1 uppercase font-semibold text-[10px] tracking-wider">Sender Name</span>
                <span class="font-bold text-slate-900">{{ $submission->student_or_lead_name }}</span>
            </div>
            <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200/70">
                <span class="text-slate-500 block mb-1 uppercase font-semibold text-[10px] tracking-wider">Email Address</span>
                <a href="mailto:{{ $submission->email }}" class="font-bold text-indigo-600 hover:text-indigo-700">{{ $submission->email }}</a>
            </div>
            <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-200/70">
                <span class="text-slate-500 block mb-1 uppercase font-semibold text-[10px] tracking-wider">Phone Number</span>
                <span class="font-bold text-slate-900 num">{{ $submission->phone ?? 'Not provided' }}</span>
            </div>
        </div>

        <!-- Message Body -->
        <div>
            <h4 class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-2">Message Content</h4>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-xs text-slate-800 leading-relaxed whitespace-pre-line">
                {{ $submission->message }}
            </div>
        </div>

        <div class="text-[11px] text-slate-500 flex items-center gap-2 num">
            <span>IP: <strong class="text-slate-700">{{ $submission->ip_address ?? '—' }}</strong></span>
            <span>•</span>
            <span>Submitted: <strong class="text-slate-700">{{ $submission->created_at->format('Y-m-d H:i:s') }}</strong></span>
        </div>
    </div>

    <!-- Status & Admin Notes -->
    <div class="card p-6">
        <h3 class="font-semibold text-sm text-slate-900 mb-4">Proctor Administration Action</h3>

        <form action="{{ route('admin.forms.status', $submission) }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Update Status</label>
                    <x-select name="status">
                        <option value="new" @selected($submission->status === 'new')>New</option>
                        <option value="reviewed" @selected($submission->status === 'reviewed')>Reviewed</option>
                        <option value="contacted" @selected($submission->status === 'contacted')>Contacted</option>
                        <option value="closed" @selected($submission->status === 'closed')>Closed</option>
                    </x-select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Internal Admin Notes</label>
                <textarea name="admin_notes" rows="3" placeholder="Notes for examination team..."
                    class="block w-full p-3 bg-white border border-slate-200 rounded-lg text-xs text-slate-900 placeholder-slate-400 shadow-sm transition focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">{{ old('admin_notes', $submission->admin_notes) }}</textarea>
            </div>

            <button type="submit" class="btn-primary">
                Update Lead Record
            </button>
        </form>
    </div>

</div>
@endsection
