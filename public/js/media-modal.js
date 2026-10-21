/**
 * WordPress-Style Drag-and-Drop Media Library Modal (Clean Light Mode SaaS)
 */
class MediaLibraryModal {
    constructor() {
        this.selectedCallback = null;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        this.initModal();
    }

    initModal() {
        if (document.getElementById('wp-media-modal')) return;

        const modalHtml = `
        <div id="wp-media-modal" class="fixed inset-0 z-50 hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity duration-200 opacity-0">
            <div id="wp-media-modal-dialog" class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-5xl h-[85vh] flex flex-col overflow-hidden transform scale-95 opacity-0 transition-all duration-200 ease-out">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-white">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </span>
                            <h3 class="font-bold text-base text-slate-900">Media Library & Asset Manager</h3>
                        </div>
                        <!-- Tabs -->
                        <div class="flex bg-slate-100 p-1 rounded-lg text-xs font-semibold">
                            <button id="media-tab-upload-btn" type="button" class="px-3.5 py-1.5 rounded-md transition text-slate-500 hover:text-slate-900">Upload Files</button>
                            <button id="media-tab-browse-btn" type="button" class="px-3.5 py-1.5 rounded-md bg-white text-slate-900 shadow-sm">Browse Media</button>
                        </div>
                    </div>
                    <button id="media-modal-close-btn" type="button" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="flex-1 overflow-hidden flex flex-col md:flex-row bg-slate-50/50">
                    <!-- Main Content Area -->
                    <div class="flex-1 flex flex-col overflow-hidden bg-white">
                        <!-- Upload View -->
                        <div id="media-view-upload" class="hidden flex-1 p-8 flex flex-col items-center justify-center">
                            <div id="media-dropzone" class="border-2 border-dashed border-slate-200 hover:border-indigo-500 rounded-2xl p-12 text-center max-w-xl w-full cursor-pointer bg-slate-50/50 hover:bg-indigo-50/20 transition">
                                <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-4 border border-indigo-100">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                </div>
                                <p class="text-sm font-semibold text-slate-900 mb-1">Drag and drop assets here to upload</p>
                                <p class="text-xs text-slate-500 mb-5">PNG, JPG, SVG, WEBP, GIF, PDF up to 20MB</p>
                                <input type="file" id="media-file-input" class="hidden" multiple accept="image/*,application/pdf">
                                <button type="button" onclick="document.getElementById('media-file-input').click()" class="px-5 py-2.5 bg-indigo-600 text-white font-semibold text-xs rounded-xl hover:bg-indigo-700 shadow-sm transition">Select Local Files</button>
                                <div id="media-upload-status" class="mt-4 text-xs font-semibold text-indigo-600 hidden">Uploading assets...</div>
                            </div>
                        </div>

                        <!-- Browse View -->
                        <div id="media-view-browse" class="flex-1 flex flex-col overflow-hidden">
                            <!-- Search & Filter bar -->
                            <div class="p-4 border-b border-slate-200 flex items-center justify-between gap-4 bg-white">
                                <input type="text" id="media-search-input" placeholder="Search media assets..." class="w-72 px-3.5 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                <span id="media-count-label" class="text-xs text-slate-500">Loading assets...</span>
                            </div>

                            <!-- Assets Grid -->
                            <div id="media-assets-grid" class="flex-1 overflow-y-auto p-4 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3 bg-slate-50/50">
                                <!-- Populated dynamically -->
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Metadata Inspector -->
                    <div id="media-asset-sidebar" class="w-full md:w-72 border-t md:border-t-0 md:border-l border-slate-200 p-5 bg-white flex flex-col justify-between hidden">
                        <div>
                            <h4 class="text-xs font-bold uppercase text-slate-500 tracking-wider mb-3">Attachment Details</h4>
                            <div id="media-sidebar-preview" class="w-full h-40 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex items-center justify-center mb-3">
                                <img id="media-preview-img" src="" class="w-full h-full object-cover">
                            </div>
                            <div class="space-y-1.5 text-xs">
                                <p id="media-sidebar-name" class="font-bold text-slate-900 truncate">image.png</p>
                                <p id="media-sidebar-size" class="text-slate-500">450 KB</p>
                                <p id="media-sidebar-dims" class="text-slate-500">1200 x 800</p>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-200 flex flex-col gap-2">
                            <button id="media-choose-asset-btn" type="button" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-sm transition">Insert Selected Asset</button>
                            <button id="media-delete-asset-btn" type="button" class="w-full py-2 text-rose-600 hover:bg-rose-50 rounded-xl text-xs font-semibold transition">Delete Permanently</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        this.bindEvents();
    }

    bindEvents() {
        const modal = document.getElementById('wp-media-modal');
        const closeBtn = document.getElementById('media-modal-close-btn');
        const uploadTabBtn = document.getElementById('media-tab-upload-btn');
        const browseTabBtn = document.getElementById('media-tab-browse-btn');
        const uploadView = document.getElementById('media-view-upload');
        const browseView = document.getElementById('media-view-browse');
        const dropzone = document.getElementById('media-dropzone');
        const fileInput = document.getElementById('media-file-input');
        const searchInput = document.getElementById('media-search-input');
        const chooseBtn = document.getElementById('media-choose-asset-btn');
        const deleteBtn = document.getElementById('media-delete-asset-btn');

        closeBtn.onclick = () => this.close();

        uploadTabBtn.onclick = () => {
            uploadTabBtn.className = 'px-3.5 py-1.5 rounded-md bg-white text-slate-900 shadow-sm';
            browseTabBtn.className = 'px-3.5 py-1.5 rounded-md transition text-slate-500 hover:text-slate-900';
            uploadView.classList.remove('hidden');
            browseView.classList.add('hidden');
        };

        browseTabBtn.onclick = () => {
            browseTabBtn.className = 'px-3.5 py-1.5 rounded-md bg-white text-slate-900 shadow-sm';
            uploadTabBtn.className = 'px-3.5 py-1.5 rounded-md transition text-slate-500 hover:text-slate-900';
            browseView.classList.remove('hidden');
            uploadView.classList.add('hidden');
            this.loadAssets();
        };

        ['dragenter', 'dragover'].forEach(name => {
            dropzone.addEventListener(name, (e) => {
                e.preventDefault();
                dropzone.classList.add('border-indigo-600', 'bg-indigo-50/30');
            });
        });
        ['dragleave', 'drop'].forEach(name => {
            dropzone.addEventListener(name, (e) => {
                e.preventDefault();
                dropzone.classList.remove('border-indigo-600', 'bg-indigo-50/30');
            });
        });

        dropzone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length) this.uploadFiles(files);
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length) this.uploadFiles(e.target.files);
        });

        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => this.loadAssets(e.target.value), 300);
        });

        chooseBtn.onclick = () => {
            if (this.selectedAsset && this.selectedCallback) {
                this.selectedCallback(this.selectedAsset);
                this.close();
            }
        };

        deleteBtn.onclick = async () => {
            if (!this.selectedAsset) return;
            if (confirm(`Permanently delete "${this.selectedAsset.original_name}"?`)) {
                await fetch(`/admin/media/${this.selectedAsset.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': this.csrfToken || '' }
                });
                this.selectedAsset = null;
                document.getElementById('media-asset-sidebar').classList.add('hidden');
                this.loadAssets();
            }
        };
    }

    async uploadFiles(files) {
        const status = document.getElementById('media-upload-status');
        status.classList.remove('hidden');

        for (const file of files) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('alt_text', file.name);

            try {
                await fetch('/admin/media/upload', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': this.csrfToken || '' },
                    body: formData,
                });
            } catch (err) {
                console.error('Upload failed:', err);
            }
        }

        status.classList.add('hidden');
        document.getElementById('media-tab-browse-btn').click();
    }

    async loadAssets(query = '') {
        const grid = document.getElementById('media-assets-grid');
        grid.innerHTML = '<div class="col-span-full py-8 text-center text-xs text-slate-400">Loading assets...</div>';

        try {
            const res = await fetch(`/admin/media/items?search=${encodeURIComponent(query)}`);
            const json = await res.json();
            const assets = json.data || [];

            document.getElementById('media-count-label').innerText = `${json.total || assets.length} item(s)`;

            if (assets.length === 0) {
                grid.innerHTML = '<div class="col-span-full py-12 text-center text-xs text-slate-400">No media assets found. Upload some!</div>';
                return;
            }

            grid.innerHTML = '';
            assets.forEach(asset => {
                const item = document.createElement('div');
                item.className = 'group relative aspect-square rounded-xl overflow-hidden border border-slate-200 hover:border-indigo-500 cursor-pointer bg-white transition shadow-sm';

                if (asset.is_image) {
                    item.innerHTML = `<img src="${asset.url}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">`;
                } else {
                    item.innerHTML = `
                    <div class="w-full h-full flex flex-col items-center justify-center p-2 text-center">
                        <svg class="w-8 h-8 text-slate-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span class="text-[10px] font-medium text-slate-600 truncate w-full">${asset.original_name}</span>
                    </div>`;
                }

                item.onclick = () => {
                    grid.querySelectorAll('div').forEach(el => el.classList.remove('border-indigo-600', 'ring-2', 'ring-indigo-600'));
                    item.classList.add('border-indigo-600', 'ring-2', 'ring-indigo-600');
                    this.selectAsset(asset);
                };

                grid.appendChild(item);
            });
        } catch (e) {
            grid.innerHTML = '<div class="col-span-full py-8 text-center text-xs text-rose-500">Failed to load media assets.</div>';
        }
    }

    selectAsset(asset) {
        this.selectedAsset = asset;
        const sidebar = document.getElementById('media-asset-sidebar');
        const img = document.getElementById('media-preview-img');
        const name = document.getElementById('media-sidebar-name');
        const size = document.getElementById('media-sidebar-size');
        const dims = document.getElementById('media-sidebar-dims');

        sidebar.classList.remove('hidden');
        if (asset.is_image) {
            img.src = asset.url;
            img.classList.remove('hidden');
        } else {
            img.classList.add('hidden');
        }

        name.innerText = asset.original_name;
        size.innerText = asset.formatted_size;
        dims.innerText = asset.width ? `${asset.width} × ${asset.height} px` : asset.mime_type;
    }

    open(callback) {
        this.selectedCallback = callback;
        const modal = document.getElementById('wp-media-modal');
        const dialog = document.getElementById('wp-media-modal-dialog');
        if (!modal) return;
        modal.classList.remove('hidden');
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
            modal.classList.add('opacity-100');
            if (dialog) {
                dialog.classList.remove('scale-95', 'opacity-0');
                dialog.classList.add('scale-100', 'opacity-100');
            }
        });
        document.getElementById('media-tab-browse-btn')?.click();
    }

    close() {
        const modal = document.getElementById('wp-media-modal');
        const dialog = document.getElementById('wp-media-modal-dialog');
        if (!modal) return;
        if (dialog) {
            dialog.classList.remove('scale-100', 'opacity-100');
            dialog.classList.add('scale-95', 'opacity-0');
        }
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }
}

window.MediaModal = new MediaLibraryModal();
window.openMediaModal = (callback) => window.MediaModal.open(callback);
