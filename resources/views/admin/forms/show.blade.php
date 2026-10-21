@extends('layouts.admin')

@section('title', 'Inquiry #' . $submission->id)
@section('page_title', 'Inquiry Details: ' . $submission->student_or_lead_name)
@section('page_subtitle', 'Submitted via ' . $submission->form_name . ' • ' . $submission->created_at->toDayDateTimeString())

@section('topbar_actions')
    <a href="{{ route('admin.forms.index') }}" class="px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold border border-slate-200 shadow-sm transition">
        &larr; Back to Inquiries
    </a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Detail Card -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-bold text-base text-slate-900">{{ $submission->subject ?? 'General Inquiry' }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">Category: <span class="font-mono text-indigo-700">{{ $submission->form_name }}</span></p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200">
                {{ strtoupper($submission->status) }}
            </span>
        </div>

        <!-- Sender info -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                <span class="text-slate-500 block mb-1 uppercase font-medium text-[10px]">Sender Name</span>
                <span class="font-bold text-slate-900">{{ $submission->student_or_lead_name }}</span>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                <span class="text-slate-500 block mb-1 uppercase font-medium text-[10px]">Email Address</span>
                <a href="mailto:{{ $submission->email }}" class="font-bold text-indigo-600 hover:text-indigo-700">{{ $submission->email }}</a>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                <span class="text-slate-500 block mb-1 uppercase font-medium text-[10px]">Phone Number</span>
                <span class="font-bold text-slate-900">{{ $submission->phone ?? 'Not provided' }}</span>
            </div>
        </div>

        <!-- Message Body -->
        <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Message Content</h4>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-xs text-slate-800 leading-relaxed whitespace-pre-line">
                {{ $submission->message }}
            </div>
        </div>

        <div class="text-[11px] text-slate-400 font-mono">
            IP Address: {{ $submission->ip_address ?? '127.0.0.1' }} • Submitted: {{ $submission->created_at->format('Y-m-d H:i:s') }}
        </div>
    </div>

    <!-- Status & Admin Notes -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm">
        <h3 class="font-bold text-sm text-slate-900 mb-4">Proctor Administration Action</h3>

        <form action="{{ route('admin.forms.status', $submission) }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Update Status</label>
                    <select name="status" class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                        <option value="new" {{ $submission->status === 'new' ? 'selected' : '' }}>New</option>
                        <option value="reviewed" {{ $submission->status === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="contacted" {{ $submission->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="closed" {{ $submission->status === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Internal Admin Notes</label>
                <textarea name="admin_notes" rows="3" placeholder="Notes for examination team..."
                    class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">{{ old('admin_notes', $submission->admin_notes) }}</textarea>
            </div>

            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl shadow-sm transition">
                Update Lead Record
            </button>
        </form>
    </div>

</div>
@endsection
