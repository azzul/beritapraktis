<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SeoController extends Controller
{
    public function index(): View
    {
        $publishedCount = Article::published()->count();
        $news48hCount = Article::published()->where('published_at', '>=', now()->subHours(48))->count();

        $endpoints = [
            ['name' => 'Sitemap Index', 'url' => route('seo.sitemap'), 'desc' => 'Indeks utama seluruh sitemap XML'],
            ['name' => 'Google News Sitemap', 'url' => route('seo.sitemap-news'), 'desc' => "Berita terbit 48 jam terakhir ({$news48hCount} artikel)"],
            ['name' => 'Sitemap Artikel', 'url' => route('seo.sitemap-articles'), 'desc' => "Seluruh artikel terbit ({$publishedCount} artikel)"],
            ['name' => 'Sitemap Halaman & Kategori', 'url' => route('seo.sitemap-pages'), 'desc' => 'Homepage, landing SEO, dan kategori'],
            ['name' => 'llms.txt', 'url' => route('seo.llms'), 'desc' => 'Panduan struktur situs untuk AI Crawlers'],
            ['name' => 'llms-full.txt', 'url' => route('seo.llms-full'), 'desc' => 'Ringkasan 50 artikel terbaru untuk LLM'],
            ['name' => 'robots.txt', 'url' => route('seo.robots'), 'desc' => 'Aturan crawler Googlebot, Googlebot-News, GPTBot, ClaudeBot'],
            ['name' => 'RSS Feed', 'url' => route('seo.feed'), 'desc' => 'Feed RSS 2.0 terbaru'],
        ];

        return view('admin.seo.index', compact('endpoints', 'publishedCount', 'news48hCount'));
    }

    public function regenerate(): RedirectResponse
    {
        ModerationController::clearSeoCaches();

        return back()->with('success', 'Seluruh cache Sitemap, Google News Sitemap, llms.txt, llms-full.txt, dan RSS Feed berhasil diperbarui!');
    }
}
