@extends('layouts.admin')

@section('title', 'Media Library')
@section('page_title', 'WordPress-Style Media Library')
@section('page_subtitle', 'Drag-and-drop asset management, automatic thumbnails, and URL copy')

@section('topbar_actions')
    <button onclick="document.getElementById('media-upload-area').scrollIntoView({behavior: 'smooth'})" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/20 transition flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
        Upload New Media
    </button>
@endsection

@section('content')
<div class="space-y-8">

    <!-- Drag-and-Drop Dropzone -->
    <div id="media-upload-area" class="bg-slate-900 border-2 border-dashed border-slate-700 hover:border-indigo-500 rounded-3xl p-10 text-center shadow-xl transition cursor-pointer">
        <div class="max-w-md mx-auto">
            <div class="w-16 h-16 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center mx-auto mb-4 border border-indigo-500/20">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            </div>
            <h3 class="font-bold text-base text-white mb-1">Drag and drop assets here</h3>
            <p class="text-xs text-slate-400 mb-5">Upload images, diagrams, certificates, or question PDFs up to 20MB</p>
            <input type="file" id="page-media-file-input" class="hidden" multiple accept="image/*,application/pdf">
            <button type="button" onclick="document.getElementById('page-media-file-input').click()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-lg transition">
                Select Files to Upload
            </button>
            <p id="page-upload-progress" class="text-xs font-semibold text-indigo-400 mt-4 hidden">Uploading files...</p>
        </div>
    </div>

    <!-- Media Library Grid -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl space-y-6">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <form action="{{ route('admin.media.index') }}" method="GET" class="w-full sm:w-80">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search assets by name or alt text..."
                    class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </form>
            <span class="text-xs text-slate-400">Total Assets: <strong class="text-white">{{ $assets->total() }}</strong></span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @forelse($assets as $asset)
                <div class="group relative rounded-2xl bg-slate-950 border border-slate-800 overflow-hidden hover:border-slate-700 shadow transition flex flex-col justify-between">
                    <div class="aspect-square bg-slate-900 overflow-hidden flex items-center justify-center relative">
                        @if($asset->is_image)
                            <img src="{{ $asset->url }}" alt="{{ $asset->alt_text }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                        @else
                            <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        @endif

                        <!-- Hover overlay action -->
                        <div class="absolute inset-0 bg-slate-950/80 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 p-2">
                            <button onclick="navigator.clipboard.writeText('{{ $asset->url }}'); alert('Direct URL copied to clipboard!');" class="p-2 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-500 transition" title="Copy URL">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                            </button>
                            <form action="{{ route('admin.media.destroy', $asset) }}" method="POST" onsubmit="return confirm('Delete this asset?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg bg-rose-600 text-white text-xs font-semibold hover:bg-rose-500 transition" title="Delete Asset">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="p-3 text-[11px]">
                        <p class="font-bold text-white truncate" title="{{ $asset->original_name }}">{{ $asset->original_name }}</p>
                        <p class="text-slate-500 mt-0.5">{{ $asset->formatted_size }}</p>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-slate-500 text-xs">
                    No media assets found. Upload files using the dropzone above!
                </div>
            @endforelse
        </div>

        <div class="pt-4 border-t border-slate-800">{{ $assets->links() }}</div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const dropzone = document.getElementById('media-upload-area');
    const fileInput = document.getElementById('page-media-file-input');
    const progressText = document.getElementById('page-upload-progress');

    ['dragenter', 'dragover'].forEach(name => {
        dropzone.addEventListener(name, (e) => {
            e.preventDefault();
            dropzone.classList.add('border-indigo-500', 'bg-indigo-950/10');
        });
    });

    ['dragleave', 'drop'].forEach(name => {
        dropzone.addEventListener(name, (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-indigo-500', 'bg-indigo-950/10');
        });
    });

    dropzone.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        if (files.length) uploadMediaFiles(files);
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length) uploadMediaFiles(e.target.files);
    });

    async function uploadMediaFiles(files) {
        progressText.classList.remove('hidden');
        progressText.innerText = `Uploading ${files.length} asset(s)...`;

        for (const file of files) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('alt_text', file.name);

            try {
                await fetch('{{ route("admin.media.upload") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData,
                });
            } catch (err) {
                console.error(err);
            }
        }

        window.location.reload();
    }
</script>
@endpush
