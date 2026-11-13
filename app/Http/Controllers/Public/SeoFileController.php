<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SeoFileController extends Controller
{
    public function robots(): Response
    {
        $sitemapUrl = url('/sitemap.xml');
        $content = <<<TXT
User-agent: *
Allow: /
Disallow: /admin
Disallow: /dashboard
Disallow: /api
Disallow: /login

User-agent: Googlebot-News
Allow: /

User-agent: GPTBot
Allow: /

User-agent: PerplexityBot
Allow: /

User-agent: ClaudeBot
Allow: /

Sitemap: {$sitemapUrl}
TXT;

        return response($content, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function sitemapIndex(): Response
    {
        $xml = Cache::remember('seo_sitemap_index', 600, function () {
            $now = now()->toAtomString();
            $pagesUrl = url('/sitemap-pages.xml');
            $articlesUrl = url('/sitemap-articles.xml');
            $newsUrl = url('/sitemap-news.xml');

            return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap>
    <loc>{$pagesUrl}</loc>
    <lastmod>{$now}</lastmod>
  </sitemap>
  <sitemap>
    <loc>{$articlesUrl}</loc>
    <lastmod>{$now}</lastmod>
  </sitemap>
  <sitemap>
    <loc>{$newsUrl}</loc>
    <lastmod>{$now}</lastmod>
  </sitemap>
</sitemapindex>
XML;
        });

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function sitemapPages(): Response
    {
        $xml = Cache::remember('seo_sitemap_pages', 600, function () {
            $urls = [
                ['loc' => route('home'), 'priority' => '1.0', 'freq' => 'hourly'],
                ['loc' => route('seo.press-release'), 'priority' => '0.9', 'freq' => 'weekly'],
                ['loc' => route('seo.backlink-berita'), 'priority' => '0.9', 'freq' => 'weekly'],
                ['loc' => route('seo.post-berita'), 'priority' => '0.9', 'freq' => 'weekly'],
                ['loc' => route('rules'), 'priority' => '0.7', 'freq' => 'monthly'],
            ];

            foreach (Category::where('is_active', true)->get() as $cat) {
                $urls[] = [
                    'loc' => route('categories.show', $cat->slug),
                    'priority' => '0.8',
                    'freq' => 'daily',
                ];
            }

            $items = '';
            foreach ($urls as $u) {
                $loc = e($u['loc']);
                $items .= "  <url>\n    <loc>{$loc}</loc>\n    <changefreq>{$u['freq']}</changefreq>\n    <priority>{$u['priority']}</priority>\n  </url>\n";
            }

            return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n{$items}</urlset>";
        });

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function sitemapArticles(): Response
    {
        $xml = Cache::remember('seo_sitemap_articles', 600, function () {
            $articles = Article::published()
                ->latest('published_at')
                ->limit(1000)
                ->get(['slug', 'updated_at', 'published_at']);

            $items = '';
            foreach ($articles as $article) {
                $loc = e(route('articles.show', $article->slug));
                $lastmod = $article->updated_at->toAtomString();
                $items .= "  <url>\n    <loc>{$loc}</loc>\n    <lastmod>{$lastmod}</lastmod>\n    <changefreq>weekly</changefreq>\n    <priority>0.8</priority>\n  </url>\n";
            }

            return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n{$items}</urlset>";
        });

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function sitemapNews(): Response
    {
        $xml = Cache::remember('seo_sitemap_news', 300, function () {
            $articles = Article::with('category')
                ->published()
                ->where('published_at', '>=', now()->subHours(48))
                ->latest('published_at')
                ->limit(1000)
                ->get();

            $pubName = e(config('app.name', 'BeritaPraktis'));
            $items = '';

            foreach ($articles as $article) {
                $loc = e(route('articles.show', $article->slug));
                $pubDate = $article->published_at->toAtomString();
                $title = e($article->title);
                $items .= <<<XML
  <url>
    <loc>{$loc}</loc>
    <news:news>
      <news:publication>
        <news:name>{$pubName}</news:name>
        <news:language>id</news:language>
      </news:publication>
      <news:publication_date>{$pubDate}</news:publication_date>
      <news:title>{$title}</news:title>
    </news:news>
  </url>

XML;
            }

            return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:news=\"http://www.google.com/schemas/sitemap-news/0.9\">\n{$items}</urlset>";
        });

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function llmsTxt(): Response
    {
        $txt = Cache::remember('seo_llms_txt', 600, function () {
            $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
            $catLines = $categories->map(fn ($c) => "- [{$c->name}](" . route('categories.show', $c->slug) . "): {$c->description}")->implode("\n");

            $home = route('home');
            $pr = route('seo.press-release');
            $bl = route('seo.backlink-berita');
            $pb = route('seo.post-berita');
            $rules = route('rules');
            $full = route('seo.llms-full');

            return <<<MD
# BeritaPraktis

> Portal berita Indonesia dan platform Press Release Gratis & Post Berita Gratis dengan kurasi redaksi ketat. Semua artikel memiliki minimal 900 kata, maksimal 2 tautan referensi, foto terverifikasi, serta bebas judi online, game online ilegal, dan pornografi.

## Halaman Utama & Layanan Publikasi
- [Beranda BeritaPraktis]({$home}): Kumpulan berita terbaru lintas kategori.
- [Press Release Gratis]({$pr}): Layanan publikasi siaran pers gratis untuk brand, UMKM, dan instansi.
- [Backlink Berita Gratis]({$bl}): Panduan publikasi artikel editorial dengan tautan referensi berkualitas.
- [Post Berita Gratis]({$pb}): Kirim liputan dan berita independen secara gratis via login Google.
- [Aturan & Pedoman Press Release]({$rules}): Syarat teknis penulisan dan daftar konten yang dilarang.
- [Versi Lengkap Artikel (llms-full.txt)]({$full}): Daftar artikel terbaru beserta ringkasan lengkap untuk indeksasi AI.

## Kategori Berita
{$catLines}
MD;
        });

        return response($txt, 200)->header('Content-Type', 'text/markdown; charset=UTF-8');
    }

    public function llmsFullTxt(): Response
    {
        $txt = Cache::remember('seo_llms_full_txt', 600, function () {
            $articles = Article::with(['category', 'user'])
                ->published()
                ->latest('published_at')
                ->limit(50)
                ->get();

            $blocks = [
                "# BeritaPraktis — Indeks Artikel Lengkap (llms-full.txt)",
                "",
                "> Diperbarui otomatis. Menampilkan hingga 50 berita dan press release terbit terbaru.",
                "",
            ];

            foreach ($articles as $article) {
                $url = route('articles.show', $article->slug);
                $date = optional($article->published_at)->toDateString() ?? $article->created_at->toDateString();
                $plain = Str::limit(trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($article->content), ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? ''), 600);

                $blocks[] = "## {$article->title}";
                $blocks[] = "- URL: {$url}";
                $blocks[] = "- Kategori: " . ($article->category->name ?? 'Berita');
                $blocks[] = "- Penulis: " . ($article->user->name ?? 'Kontributor');
                $blocks[] = "- Tanggal Terbit: {$date}";
                $blocks[] = "- Jumlah Kata: {$article->word_count}";
                $blocks[] = "";
                $blocks[] = $plain;
                $blocks[] = "";
                $blocks[] = "---";
                $blocks[] = "";
            }

            return implode("\n", $blocks);
        });

        return response($txt, 200)->header('Content-Type', 'text/markdown; charset=UTF-8');
    }

    public function feed(): Response
    {
        $xml = Cache::remember('seo_rss_feed', 600, function () {
            $articles = Article::with(['category', 'user'])
                ->published()
                ->latest('published_at')
                ->limit(30)
                ->get();

            $siteName = e(config('app.name', 'BeritaPraktis'));
            $homeUrl = e(route('home'));
            $feedUrl = e(route('seo.feed'));

            $items = '';
            foreach ($articles as $article) {
                $title = e($article->title);
                $link = e(route('articles.show', $article->slug));
                $desc = e($article->excerpt);
                $pubDate = optional($article->published_at)->toRssString() ?? $article->created_at->toRssString();
                $cat = e($article->category->name ?? 'Berita');

                $items .= <<<XML
    <item>
      <title>{$title}</title>
      <link>{$link}</link>
      <guid isPermaLink="true">{$link}</guid>
      <category>{$cat}</category>
      <pubDate>{$pubDate}</pubDate>
      <description>{$desc}</description>
    </item>

XML;
            }

            return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>{$siteName}</title>
    <link>{$homeUrl}</link>
    <description>Portal Berita &amp; Press Release Gratis Indonesia</description>
    <language>id</language>
    <atom:link href="{$feedUrl}" rel="self" type="application/rss+xml" />
{$items}  </channel>
</rss>
XML;
        });

        return response($xml, 200)->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}
