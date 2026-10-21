@extends('layouts.admin')

@section('title', 'Media Library')
@section('page_title', 'Media Library')
@section('page_subtitle', 'Drag-and-drop asset management, automatic thumbnails, and URL copy')

@section('topbar_actions')
    <button type="button" onclick="document.getElementById('media-upload-area').scrollIntoView({behavior: 'smooth'})" class="btn-primary">
        <x-icon name="upload" class="w-3.5 h-3.5" />
        <span>Upload media</span>
    </button>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Refined Drag-and-Drop Dropzone -->
    <div id="media-upload-area" class="card border-dashed border-2 border-slate-200 hover:border-indigo-400 p-8 text-center transition cursor-pointer bg-slate-50/50 hover:bg-indigo-50/30">
        <div class="max-w-md mx-auto">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-3 border border-indigo-100/80 shadow-xs">
                <x-icon name="upload" class="w-6 h-6" />
            </div>
            <h3 class="font-semibold text-sm text-slate-900 mb-1">Drag and drop assets here</h3>
            <p class="text-xs text-slate-500 mb-4">Upload images, diagrams, certificates, or question PDFs up to 20MB</p>
            <input type="file" id="page-media-file-input" class="hidden" multiple accept="image/*,application/pdf">
            <button type="button" onclick="document.getElementById('page-media-file-input').click()" class="btn-primary">
                Select Files to Upload
            </button>
            <p id="page-upload-progress" class="text-xs font-semibold text-indigo-600 mt-3 hidden">Uploading files...</p>
        </div>
    </div>

    <!-- Media Library Grid -->
    <div class="card p-5 space-y-5">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <form action="{{ route('admin.media.index') }}" method="GET" class="w-full sm:w-80">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search assets by name or alt text..." class="input pr-8">
                    <x-icon name="search" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                </div>
            </form>
            <span class="text-xs text-slate-500 font-medium">Total Assets: <strong class="text-slate-900 num font-semibold">{{ $assets->total() }}</strong></span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5">
            @forelse($assets as $asset)
                <div class="group relative rounded-lg bg-white border border-slate-200 overflow-hidden hover:border-slate-300 shadow-xs transition flex flex-col justify-between">
                    <div class="aspect-square bg-slate-50 overflow-hidden flex items-center justify-center relative">
                        @if($asset->is_image)
                            <img src="{{ $asset->url }}" alt="{{ $asset->alt_text }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                        @else
                            <x-icon name="file" class="w-8 h-8 text-slate-400" />
                        @endif

                        <!-- Hover overlay action -->
                        <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1.5 p-2">
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $asset->url }}'); alert('Direct URL copied to clipboard!');"
                                    class="w-7 h-7 rounded-md bg-white/90 hover:bg-white text-slate-700 flex items-center justify-center shadow-xs transition" title="Copy URL">
                                <x-icon name="copy" class="w-3.5 h-3.5" />
                            </button>
                            <form action="{{ route('admin.media.destroy', $asset) }}" method="POST" onsubmit="return confirm('Delete this asset?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-7 h-7 rounded-md bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center shadow-xs transition" title="Delete Asset">
                                    <x-icon name="trash-2" class="w-3.5 h-3.5" />
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="p-2.5 text-[11px] border-t border-slate-100">
                        <p class="font-semibold text-slate-900 truncate" title="{{ $asset->original_name }}">{{ $asset->original_name }}</p>
                        <p class="text-slate-500 mt-0.5 num">{{ $asset->formatted_size }}</p>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-slate-400 text-xs">
                    No media assets found. Upload files using the dropzone above.
                </div>
            @endforelse
        </div>

        @if($assets->hasPages())
            <div class="pt-3 border-t border-slate-100">{{ $assets->links() }}</div>
        @endif
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
            dropzone.classList.add('border-indigo-500', 'bg-indigo-50/50');
        });
    });

    ['dragleave', 'drop'].forEach(name => {
        dropzone.addEventListener(name, (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-indigo-500', 'bg-indigo-50/50');
        });
    });

    dropzone.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        if (files.length > 0) uploadFiles(files);
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) uploadFiles(fileInput.files);
    });

    async function uploadFiles(files) {
        progressText.classList.remove('hidden');
        progressText.innerText = `Uploading ${files.length} file(s)...`;

        for (let file of files) {
            const formData = new FormData();
            formData.append('file', file);

            try {
                const res = await fetch('{{ route("admin.media.upload") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
            } catch (e) {
                console.error('Upload error:', e);
            }
        }

        window.location.reload();
    }
</script>
@endpush
