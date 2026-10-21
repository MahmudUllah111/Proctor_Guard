/**
 * WordPress-Style Drag-and-Drop Media Library Modal
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
        <div id="wp-media-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 w-full max-w-5xl h-[85vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Media Library & Asset Manager
                        </h3>
                        <!-- Tabs -->
                        <div class="flex bg-slate-100 dark:bg-slate-800 p-1 rounded-lg text-xs font-semibold">
                            <button id="media-tab-upload-btn" type="button" class="px-3 py-1.5 rounded-md transition-colors text-slate-600 dark:text-slate-400 hover:text-slate-900">Upload Files</button>
                            <button id="media-tab-browse-btn" type="button" class="px-3 py-1.5 rounded-md bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm">Media Library</button>
                        </div>
                    </div>
                    <button id="media-modal-close-btn" type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="flex-1 overflow-hidden flex flex-col md:flex-row">
                    <!-- Main Content Area -->
                    <div class="flex-1 flex flex-col overflow-hidden">
                        <!-- Upload View -->
                        <div id="media-view-upload" class="hidden flex-1 p-8 flex flex-col items-center justify-center">
                            <div id="media-dropzone" class="border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-indigo-500 dark:hover:border-indigo-400 rounded-2xl p-12 text-center max-w-xl w-full cursor-pointer bg-slate-50 dark:bg-slate-800/50 transition-colors">
                                <svg class="w-16 h-16 text-indigo-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                <p class="text-base font-semibold text-slate-800 dark:text-slate-200 mb-1">Drag and drop files here to upload</p>
                                <p class="text-xs text-slate-500 mb-4">PNG, JPG, SVG, WEBP, GIF, PDF up to 20MB</p>
                                <input type="file" id="media-file-input" class="hidden" multiple accept="image/*,application/pdf">
                                <button type="button" onclick="document.getElementById('media-file-input').click()" class="px-5 py-2.5 bg-indigo-600 text-white font-medium text-xs rounded-xl hover:bg-indigo-700 shadow-sm transition">Select Files</button>
                                <div id="media-upload-status" class="mt-4 text-xs font-semibold text-indigo-600 hidden">Uploading...</div>
                            </div>
                        </div>

                        <!-- Browse View -->
                        <div id="media-view-browse" class="flex-1 flex flex-col overflow-hidden">
                            <!-- Search & Filter bar -->
                            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between gap-4">
                                <input type="text" id="media-search-input" placeholder="Search media assets..." class="w-72 px-3.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <span id="media-count-label" class="text-xs text-slate-500">Loading assets...</span>
                            </div>

                            <!-- Assets Grid -->
                            <div id="media-assets-grid" class="flex-1 overflow-y-auto p-4 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                                <!-- Populated dynamically -->
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Preview -->
                    <div id="media-asset-sidebar" class="w-full md:w-72 border-t md:border-t-0 md:border-l border-slate-200 dark:border-slate-800 p-4 bg-slate-50/50 dark:bg-slate-800/30 flex flex-col justify-between hidden">
                        <div>
                            <h4 class="text-xs font-bold uppercase text-slate-400 tracking-wider mb-3">Attachment Details</h4>
                            <div id="media-sidebar-preview" class="w-full h-40 rounded-xl bg-slate-200 dark:bg-slate-700 overflow-hidden flex items-center justify-center mb-3">
                                <img id="media-preview-img" src="" class="w-full h-full object-cover">
                            </div>
                            <div class="space-y-1 text-xs">
                                <p id="media-sidebar-name" class="font-bold text-slate-900 dark:text-white truncate">image.png</p>
                                <p id="media-sidebar-size" class="text-slate-500">450 KB</p>
                                <p id="media-sidebar-dims" class="text-slate-500">1200 x 800</p>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700 flex flex-col gap-2">
                            <button id="media-choose-asset-btn" type="button" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow transition">Insert Selected Asset</button>
                            <button id="media-delete-asset-btn" type="button" class="w-full py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-xl text-xs font-medium transition">Delete Permanently</button>
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
            uploadTabBtn.className = 'px-3 py-1.5 rounded-md bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm';
            browseTabBtn.className = 'px-3 py-1.5 rounded-md transition-colors text-slate-600 dark:text-slate-400 hover:text-slate-900';
            uploadView.classList.remove('hidden');
            browseView.classList.add('hidden');
        };

        browseTabBtn.onclick = () => {
            browseTabBtn.className = 'px-3 py-1.5 rounded-md bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm';
            uploadTabBtn.className = 'px-3 py-1.5 rounded-md transition-colors text-slate-600 dark:text-slate-400 hover:text-slate-900';
            browseView.classList.remove('hidden');
            uploadView.classList.add('hidden');
            this.loadAssets();
        };

        // Drag and Drop
        ['dragenter', 'dragover'].forEach(name => {
            dropzone.addEventListener(name, (e) => {
                e.preventDefault();
                dropzone.classList.add('border-indigo-600');
            });
        });
        ['dragleave', 'drop'].forEach(name => {
            dropzone.addEventListener(name, (e) => {
                e.preventDefault();
                dropzone.classList.remove('border-indigo-600');
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
                item.className = 'group relative aspect-square rounded-xl overflow-hidden border-2 border-slate-200 dark:border-slate-800 hover:border-indigo-500 cursor-pointer bg-slate-100 dark:bg-slate-800 transition-all';

                if (asset.is_image) {
                    item.innerHTML = `<img src="${asset.url}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">`;
                } else {
                    item.innerHTML = `
                    <div class="w-full h-full flex flex-col items-center justify-center p-2 text-center">
                        <svg class="w-8 h-8 text-slate-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span class="text-[10px] font-medium text-slate-600 dark:text-slate-300 truncate w-full">${asset.original_name}</span>
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
            grid.innerHTML = '<div class="col-span-full py-8 text-center text-xs text-red-500">Failed to load media assets.</div>';
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
        modal.classList.remove('hidden');
        document.getElementById('media-tab-browse-btn').click();
    }

    close() {
        const modal = document.getElementById('wp-media-modal');
        modal.classList.add('hidden');
    }
}

window.MediaModal = new MediaLibraryModal();
window.openMediaModal = (callback) => window.MediaModal.open(callback);
