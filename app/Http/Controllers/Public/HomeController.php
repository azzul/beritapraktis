<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Services\SeoSchemaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(SeoSchemaService $seo): View
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        $headline = Article::with(['category', 'user'])
            ->published()
            ->latest('published_at')
            ->first();

        $latestArticles = Article::with(['category', 'user'])
            ->published()
            ->when($headline, fn ($q) => $q->where('id', '!=', $headline->id))
            ->latest('published_at')
            ->paginate(12);

        $popularArticles = Article::with('category')
            ->published()
            ->orderByDesc('views_count')
            ->limit(5)
            ->get();

        $schemas = [
            $seo->websiteSchema(),
            $seo->organizationSchema(),
        ];

        return view('public.home', compact('categories', 'headline', 'latestArticles', 'popularArticles', 'schemas'));
    }

    public function search(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        $articles = Article::with(['category', 'user'])
            ->published()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('excerpt', 'like', "%{$q}%")
                        ->orWhere('content', 'like', "%{$q}%");
                });
            })
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        return view('public.search', compact('q', 'articles', 'categories'));
    }
}
