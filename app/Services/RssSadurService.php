<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RssSadurService
{
    public function __construct(
        private ArticleValidationService $validator
    ) {}

    /**
     * Preset official / reputable Indonesian RSS feeds for ethical auto-sadur.
     *
     * @return list<array{name: string, url: string, category_slug: string}>
     */
    public static function presets(): array
    {
        return [
            ['name' => 'ANTARA News - Terkini', 'url' => 'https://www.antaranews.com/rss/terkini.xml', 'category_slug' => 'politik'],
            ['name' => 'ANTARA News - Ekonomi', 'url' => 'https://www.antaranews.com/rss/ekonomi.xml', 'category_slug' => 'bisnis-ekonomi'],
            ['name' => 'ANTARA News - Tekno', 'url' => 'https://www.antaranews.com/rss/tekno.xml', 'category_slug' => 'teknologi'],
            ['name' => 'ANTARA News - Hukum', 'url' => 'https://www.antaranews.com/rss/hukum.xml', 'category_slug' => 'hukum-kriminal'],
            ['name' => 'ANTARA News - Gaya Hidup', 'url' => 'https://www.antaranews.com/rss/lifestyle.xml', 'category_slug' => 'gaya-hidup'],
        ];
    }

    /**
     * Fetch RSS feed, filter banned keywords, and create ethical saduran articles.
     *
     * @return array{imported: int, skipped: int, errors: list<string>}
     */
    public function importFromFeed(
        string $feedUrl,
        string $sourceName,
        Category $category,
        User $author,
        int $limit = 5,
        bool $publishImmediately = false
    ): array {
        $imported = 0;
        $skipped = 0;
        $errors = [];

        try {
            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'BeritaPraktisBot/1.0 (+https://beritapraktis.com)'])
                ->get($feedUrl);

            if (!$response->successful()) {
                return ['imported' => 0, 'skipped' => 0, 'errors' => ["Gagal mengambil RSS (HTTP {$response->status()})."]];
            }

            $items = $this->parseRssItems($response->body(), $limit);
        } catch (\Throwable $e) {
            return ['imported' => 0, 'skipped' => 0, 'errors' => ['Error koneksi RSS: ' . $e->getMessage()]];
        }

        if (empty($items)) {
            return ['imported' => 0, 'skipped' => 0, 'errors' => ['Tidak ada item berita ditemukan pada feed tersebut.']];
        }

        $photoMap = [
            'politik' => 'images/seed/politik.jpg',
            'bisnis-ekonomi' => 'images/seed/bisnis-ekonomi.jpg',
            'teknologi' => 'images/seed/teknologi.jpg',
            'pertanian' => 'images/seed/pertanian.jpg',
            'pendidikan' => 'images/seed/pendidikan.jpg',
            'hukum-kriminal' => 'images/seed/hukum-kriminal.jpg',
            'gaya-hidup' => 'images/seed/gaya-hidup.jpg',
            'press-release' => 'images/seed/press-release.jpg',
        ];
        $featuredImage = $photoMap[$category->slug] ?? 'images/seed/press-release.jpg';

        foreach ($items as $item) {
            $title = trim(html_entity_decode(strip_tags($item['title']), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $link = trim($item['link']);
            $desc = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($item['description']), ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? '');

            if ($title === '' || $link === '') {
                $skipped++;
                continue;
            }

            $slug = Str::slug($title);
            if (Article::withTrashed()->where('slug', $slug)->exists()) {
                $skipped++;
                continue;
            }

            // Safety check against banned keywords (Judol, Game Online, Pornografi)
            if (!empty($this->validator->findBannedKeywords($title, $desc, [$link]))) {
                $skipped++;
                continue;
            }

            $summary = $desc !== '' ? Str::limit($desc, 420) : "Ringkasan informasi terkini mengenai {$title}.";
            $content = $this->buildSaduranContent($title, $summary, $sourceName, $link, $category->name);
            $wordCount = $this->validator->countWords($content);
            $outboundLinks = $this->validator->extractOutboundLinks($content);

            Article::create([
                'user_id' => $author->id,
                'category_id' => $category->id,
                'title' => Str::limit($title, 175, ''),
                'slug' => $slug,
                'excerpt' => Str::limit($summary, 160),
                'content' => $content,
                'featured_image' => $featuredImage,
                'image_alt' => 'Foto editorial: ' . Str::limit($title, 120, ''),
                'image_caption' => "Ilustrasi BeritaPraktis | Sumber saduran: {$sourceName}",
                'word_count' => $wordCount,
                'outbound_links_count' => count($outboundLinks),
                'outbound_links' => $outboundLinks,
                'status' => $publishImmediately ? 'published' : 'pending_review',
                'published_at' => $publishImmediately ? now() : null,
                'reviewed_by' => $publishImmediately ? $author->id : null,
                'reviewed_at' => $publishImmediately ? now() : null,
            ]);

            $imported++;
        }

        if ($imported > 0 && $publishImmediately) {
            \App\Http\Controllers\Admin\ModerationController::clearSeoCaches();
        }

        return ['imported' => $imported, 'skipped' => $skipped, 'errors' => $errors];
    }

    /**
     * Parse RSS XML items safely with or without ext-xml.
     *
     * @return list<array{title: string, link: string, description: string}>
     */
    private function parseRssItems(string $xmlString, int $limit): array
    {
        $items = [];

        if (function_exists('simplexml_load_string')) {
            libxml_use_internal_errors(true);
            $xml = @simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);
            if ($xml && isset($xml->channel->item)) {
                foreach ($xml->channel->item as $node) {
                    $items[] = [
                        'title' => (string) ($node->title ?? ''),
                        'link' => (string) ($node->link ?? ''),
                        'description' => (string) ($node->description ?? ''),
                    ];
                    if (count($items) >= $limit) {
                        break;
                    }
                }
                return $items;
            }
        }

        // Regex fallback if ext-xml / simplexml is unavailable
        if (preg_match_all('/<item\b[^>]*>(.*?)<\/item>/isu', $xmlString, $matches)) {
            foreach (array_slice($matches[1], 0, $limit) as $block) {
                $title = $this->extractTag($block, 'title');
                $link = $this->extractTag($block, 'link');
                $desc = $this->extractTag($block, 'description');
                $items[] = [
                    'title' => $title,
                    'link' => $link,
                    'description' => $desc,
                ];
            }
        }

        return $items;
    }

    private function extractTag(string $block, string $tag): string
    {
        if (preg_match("/<{$tag}[^>]*>(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?<\/{$tag}>/isu", $block, $m)) {
            return trim($m[1]);
        }
        return '';
    }

    private function buildSaduranContent(string $title, string $summary, string $sourceName, string $sourceUrl, string $categoryName): string
    {
        return <<<HTML
<p><strong>JAKARTA, BeritaPraktis</strong> — {$summary}</p>

<h2>Sorotan Utama &amp; Konteks {$categoryName}</h2>
<p>Perkembangan terbaru terkait <em>{$title}</em> menjadi topik yang mendapat perhatian luas di kanal {$categoryName}. Informasi awal yang dihimpun menunjukkan adanya dinamika penting yang relevan bagi pembaca maupun pelaku industri di Indonesia.</p>
<p>Dalam penyampaian informasi publik, ketepatan fakta dan rujukan sumber resmi menjadi prinsip utama agar masyarakat memperoleh gambaran yang berimbang dan terhindar dari disinformasi.</p>

<h2>Implikasi bagi Pembaca</h2>
<p>Ringkasan ini disusun untuk memberikan pemahaman cepat mengenai pokok persoalan, latar belakang kebijakan, serta potensi dampaknya bagi aktivitas masyarakat sehari-hari. Pembaca disarankan untuk selalu mengikuti pembaruan lanjutan dari sumber-sumber tepercaya.</p>

<blockquote>
<p><strong>Sumber Saduran Resmi:</strong> Disadur secara etis dari <a href="{$sourceUrl}" target="_blank" rel="noopener">{$sourceName}</a>. Klik tautan sumber untuk membaca laporan lengkap aslinya.</p>
</blockquote>
HTML;
    }
}
