<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannedKeyword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class BannedKeywordController extends Controller
{
    public function index(): View
    {
        $keywords = BannedKeyword::orderBy('category')->orderBy('keyword')->paginate(50);

        return view('admin.keywords.index', compact('keywords'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'keyword' => ['required', 'string', 'min:2', 'max:80'],
            'category' => ['required', 'in:gambling,online_game,pornography,other'],
        ]);

        BannedKeyword::updateOrCreate(
            ['keyword' => mb_strtolower(trim($validated['keyword']), 'UTF-8')],
            ['category' => $validated['category'], 'is_active' => true]
        );

        Cache::forget('banned_keywords_active');

        return back()->with('success', 'Kata kunci terlarang ditambahkan.');
    }

    public function destroy(BannedKeyword $keyword): RedirectResponse
    {
        $keyword->delete();
        Cache::forget('banned_keywords_active');

        return back()->with('success', 'Kata kunci dihapus.');
    }
}
