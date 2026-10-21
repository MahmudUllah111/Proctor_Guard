<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaLibraryController extends Controller
{
    /**
     * Dedicated Media Library management view.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $assets = MediaAsset::when($search, function ($q, $search) {
                $q->where('original_name', 'like', "%{$search}%")
                  ->orWhere('alt_text', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(24)
            ->withQueryString();

        return view('admin.media.index', compact('assets', 'search'));
    }

    /**
     * JSON feed for WordPress-style Media Picker Modal.
     */
    public function items(Request $request): JsonResponse
    {
        $search = $request->input('search');

        $assets = MediaAsset::when($search, function ($q, $search) {
                $q->where('original_name', 'like', "%{$search}%")
                  ->orWhere('alt_text', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(24);

        return response()->json([
            'data' => $assets->items(),
            'current_page' => $assets->currentPage(),
            'last_page' => $assets->lastPage(),
            'total' => $assets->total(),
        ]);
    }

    /**
     * Handle drag-and-drop upload.
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:20480', // 20MB max
            'alt_text' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mime = $file->getMimeType();
        $size = $file->getSize();

        $filename = Str::random(24) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('media', $filename, 'public');

        $width = null;
        $height = null;

        if (str_starts_with($mime ?? '', 'image/') && function_exists('getimagesize')) {
            $imageInfo = @getimagesize($file->getRealPath());
            if ($imageInfo) {
                $width = $imageInfo[0];
                $height = $imageInfo[1];
            }
        }

        $asset = MediaAsset::create([
            'original_name' => $originalName,
            'file_name' => $filename,
            'file_path' => $path,
            'disk' => 'public',
            'mime_type' => $mime,
            'file_size' => $size,
            'width' => $width,
            'height' => $height,
            'alt_text' => $request->input('alt_text', pathinfo($originalName, PATHINFO_FILENAME)),
            'caption' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Asset uploaded successfully.',
            'asset' => $asset,
        ]);
    }

    /**
     * Delete an asset from disk and database.
     */
    public function destroy(MediaAsset $media)
    {
        if (Storage::disk($media->disk)->exists($media->file_path)) {
            Storage::disk($media->disk)->delete($media->file_path);
        }

        $media->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Media asset removed.']);
        }

        return back()->with('success', 'Media asset removed.');
    }
}
