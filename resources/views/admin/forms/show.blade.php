@extends('layouts.admin')

@section('title', 'Inquiry #' . $submission->id)
@section('page_title', 'Inquiry Details: ' . $submission->student_or_lead_name)
@section('page_subtitle', 'Submitted via ' . $submission->form_name . ' • ' . $submission->created_at->toDayDateTimeString())

@section('topbar_actions')
    <a href="{{ route('admin.forms.index') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 transition">
        &larr; Back to Inquiries
    </a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Detail Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-6">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h3 class="font-bold text-base text-white">{{ $submission->subject ?? 'General Inquiry' }}</h3>
                <p class="text-xs text-slate-400 mt-0.5">Category: <span class="font-mono text-indigo-300">{{ $submission->form_name }}</span></p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-slate-800 text-slate-300 font-bold text-xs border border-slate-700">
                {{ strtoupper($submission->status) }}
            </span>
        </div>

        <!-- Sender info -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                <span class="text-slate-500 block mb-1">Sender Name</span>
                <span class="font-bold text-white">{{ $submission->student_or_lead_name }}</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                <span class="text-slate-500 block mb-1">Email Address</span>
                <a href="mailto:{{ $submission->email }}" class="font-bold text-indigo-400 hover:text-indigo-300">{{ $submission->email }}</a>
            </div>
            <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                <span class="text-slate-500 block mb-1">Phone Number</span>
                <span class="font-bold text-white">{{ $submission->phone ?? 'Not provided' }}</span>
            </div>
        </div>

        <!-- Message Body -->
        <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Message Content</h4>
            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-200 leading-relaxed whitespace-pre-line">
                {{ $submission->message }}
            </div>
        </div>

        <div class="text-[11px] text-slate-500 font-mono">
            IP Address: {{ $submission->ip_address ?? '127.0.0.1' }} • Submitted: {{ $submission->created_at->format('Y-m-d H:i:s') }}
        </div>
    </div>

    <!-- Status & Admin Notes -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h3 class="font-bold text-sm text-white mb-4">Proctor Administration Action</h3>

        <form action="{{ route('admin.forms.status', $submission) }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-2">Update Status</label>
                    <select name="status" class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white">
                        <option value="new" {{ $submission->status === 'new' ? 'selected' : '' }}>New</option>
                        <option value="reviewed" {{ $submission->status === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="contacted" {{ $submission->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="closed" {{ $submission->status === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-2">Internal Admin Notes</label>
                <textarea name="admin_notes" rows="3" placeholder="Notes for examination team..."
                    class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('admin_notes', $submission->admin_notes) }}</textarea>
            </div>

            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow transition">
                Update Lead Record
            </button>
        </form>
    </div>

</div>
@endsection
