<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\RssSadurService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutoSadurController extends Controller
{
    public function index(): View
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $presets = RssSadurService::presets();

        return view('admin.sadur.index', compact('categories', 'presets'));
    }

    public function import(Request $request, RssSadurService $sadur): RedirectResponse
    {
        $validated = $request->validate([
            'feed_url' => ['required', 'url'],
            'source_name' => ['required', 'string', 'max:100'],
            'category_id' => ['required', 'exists:categories,id'],
            'limit' => ['required', 'integer', 'min:1', 'max:15'],
            'publish_now' => ['nullable', 'boolean'],
        ]);

        $category = Category::findOrFail($validated['category_id']);
        $result = $sadur->importFromFeed(
            $validated['feed_url'],
            $validated['source_name'],
            $category,
            $request->user(),
            (int) $validated['limit'],
            $request->boolean('publish_now')
        );

        if (!empty($result['errors'])) {
            return back()->with('error', implode(' ', $result['errors']));
        }

        $statusLabel = $request->boolean('publish_now') ? 'langsung tayang & masuk sitemap' : 'masuk antrean review';

        return back()->with('success', "Auto-Sadur selesai: {$result['imported']} artikel baru berhasil disadur ({$statusLabel}), {$result['skipped']} dilewati (duplikat/terfilter).");
    }
}
