<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsPageController extends Controller
{
    public function index()
    {
        $pages = CmsPage::orderBy('is_home', 'desc')->orderBy('title')->get();

        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_pages,slug',
            'status' => 'required|in:published,draft',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'seo_keywords' => 'nullable|string|max:255',
            'og_image' => 'nullable|string|max:500',
            'is_home' => 'boolean',
            'widgets_json' => 'nullable|string',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $isHome = $request->boolean('is_home');

        if ($isHome) {
            CmsPage::where('is_home', true)->update(['is_home' => false]);
            $slug = 'home';
        }

        $widgets = [];
        if (!empty($validated['widgets_json'])) {
            $widgets = json_decode($validated['widgets_json'], true) ?? [];
        }

        $page = CmsPage::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'content_widgets' => $widgets,
            'status' => $validated['status'],
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
            'seo_keywords' => $validated['seo_keywords'] ?? null,
            'og_image' => $validated['og_image'] ?? null,
            'is_home' => $isHome,
        ]);

        return redirect()->route('admin.pages.edit', $page)
            ->with('success', 'Page created successfully! Configure your widgets below.');
    }

    public function edit(CmsPage $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, CmsPage $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:cms_pages,slug,' . $page->id,
            'status' => 'required|in:published,draft',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'seo_keywords' => 'nullable|string|max:255',
            'og_image' => 'nullable|string|max:500',
            'is_home' => 'boolean',
            'widgets_json' => 'nullable|string',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $isHome = $request->boolean('is_home');

        if ($isHome) {
            CmsPage::where('id', '!=', $page->id)->where('is_home', true)->update(['is_home' => false]);
            $slug = 'home';
        }

        $widgets = [];
        if (!empty($validated['widgets_json'])) {
            $widgets = json_decode($validated['widgets_json'], true) ?? [];
        }

        $page->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'content_widgets' => $widgets,
            'status' => $validated['status'],
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
            'seo_keywords' => $validated['seo_keywords'] ?? null,
            'og_image' => $validated['og_image'] ?? null,
            'is_home' => $isHome,
        ]);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page and widgets updated successfully.');
    }

    public function destroy(CmsPage $page)
    {
        if ($page->is_home) {
            return back()->withErrors(['error' => 'Cannot delete the designated Homepage. Assign another page first.']);
        }

        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }
}
