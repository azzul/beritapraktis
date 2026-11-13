<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Services\SeoSchemaService;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function category(string $slug, SeoSchemaService $seo): View
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        $articles = Article::with(['category', 'user'])
            ->published()
            ->where('category_id', $category->id)
            ->latest('published_at')
            ->paginate(12);

        $schemas = [
            $seo->breadcrumbSchema([
                ['name' => 'Beranda', 'url' => route('home')],
                ['name' => $category->name, 'url' => route('categories.show', $category->slug)],
            ]),
        ];

        return view('public.category', compact('category', 'categories', 'articles', 'schemas'));
    }

    public function show(string $slug, SeoSchemaService $seo): View
    {
        $article = Article::with(['category', 'user'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $article->increment('views_count');

        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        $relatedArticles = Article::with('category')
            ->published()
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->limit(4)
            ->get();

        $schemas = [
            $seo->newsArticleSchema($article),
            $seo->breadcrumbSchema([
                ['name' => 'Beranda', 'url' => route('home')],
                ['name' => $article->category->name, 'url' => route('categories.show', $article->category->slug)],
                ['name' => $article->title, 'url' => route('articles.show', $article->slug)],
            ]),
        ];

        return view('public.article', compact('article', 'categories', 'relatedArticles', 'schemas'));
    }
}
