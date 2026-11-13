<?php

namespace App\Services;

use App\Models\BannedKeyword;
use Illuminate\Support\Facades\Cache;

class ArticleValidationService
{
    public function countWords(string $html): int
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? '');

        if ($text === '') {
            return 0;
        }

        preg_match_all('/[\p{L}\p{N}]+(?:[\'-][\p{L}\p{N}]+)*/u', $text, $matches);

        return count($matches[0] ?? []);
    }

    /**
     * Extract all external/outbound links from <a href="..."> and raw URLs.
     *
     * @return list<string>
     */
    public function extractOutboundLinks(string $html): array
    {
        $links = [];
        $appHost = parse_url(config('app.url'), PHP_URL_HOST) ?: '';

        // 1. Extract href from <a> tags
        if (preg_match_all('/<a\s+[^>]*href\s*=\s*(["\'])(.*?)\1/iu', $html, $matches)) {
            foreach ($matches[2] as $url) {
                $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                if ($this->isExternalUrl($url, $appHost)) {
                    $links[] = $url;
                }
            }
        }

        // 2. Extract raw URLs from plain text (outside HTML attributes)
        $plainText = strip_tags(preg_replace('/<a\b[^>]*>.*?<\/a>/isu', ' ', $html) ?? $html);
        if (preg_match_all('/\b((?:https?:\/\/|www\.)[^\s<>"\'()]+)/iu', $plainText, $rawMatches)) {
            foreach ($rawMatches[1] as $rawUrl) {
                $rawUrl = rtrim($rawUrl, '.,;:!?');
                $normalized = str_starts_with(strtolower($rawUrl), 'www.') ? 'https://' . $rawUrl : $rawUrl;
                if ($this->isExternalUrl($normalized, $appHost)) {
                    $links[] = $normalized;
                }
            }
        }

        return array_values(array_unique($links));
    }

    private function isExternalUrl(string $url, string $appHost): bool
    {
        if ($url === '' || str_starts_with($url, '#') || str_starts_with($url, '/') || str_starts_with(strtolower($url), 'mailto:') || str_starts_with(strtolower($url), 'tel:')) {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) {
            return false;
        }

        if ($appHost !== '' && strcasecmp($host, $appHost) === 0) {
            return false;
        }

        return true;
    }

    /**
     * Check title, content, and outbound URLs against banned keywords.
     *
     * @param  list<string>  $links
     * @return list<array{keyword: string, category: string}>
     */
    public function findBannedKeywords(string $title, string $content, array $links = []): array
    {
        $keywords = Cache::remember('banned_keywords_active', 300, function () {
            return BannedKeyword::where('is_active', true)->get(['keyword', 'category'])->toArray();
        });

        if (empty($keywords)) {
            return [];
        }

        $plainContent = html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $haystack = mb_strtolower($title . ' ' . $plainContent . ' ' . implode(' ', $links), 'UTF-8');
        // Normalized version without punctuation between letters to catch s.l.o.t / j*d*o*l
        $collapsed = preg_replace('/[^\p{L}\p{N}\s]/u', '', $haystack) ?? $haystack;

        $found = [];
        foreach ($keywords as $item) {
            $kw = mb_strtolower(trim($item['keyword']), 'UTF-8');
            if ($kw === '') {
                continue;
            }

            $pattern = '/\b' . preg_quote($kw, '/') . '\b/u';
            if (preg_match($pattern, $haystack) || preg_match($pattern, $collapsed)) {
                $found[] = [
                    'keyword' => $item['keyword'],
                    'category' => $item['category'],
                ];
            }
        }

        return $found;
    }

    /**
     * Sanitize HTML content and enforce rel & target attributes on outbound links.
     */
    public function sanitizeContent(string $html): string
    {
        // Strip dangerous tags
        $html = preg_replace('/<(script|style|iframe|object|embed|form|input|button)[^>]*>.*?<\/\1>/isu', '', $html) ?? $html;
        $html = preg_replace('/<(script|style|iframe|object|embed|form|input|button)[^>]*\/?>/isu', '', $html) ?? $html;

        // Strip inline event handlers & javascript: URIs
        $html = preg_replace('/\s+on[a-z]+\s*=\s*(["\']).*?\1/iu', '', $html) ?? $html;
        $html = preg_replace('/href\s*=\s*(["\'])\s*javascript:.*?\1/iu', 'href="#"', $html) ?? $html;

        // Allow only safe editorial HTML tags
        $allowedTags = '<p><br><h2><h3><h4><h5><ul><ol><li><blockquote><strong><b><em><i><u><a><figure><figcaption><hr>';
        $html = strip_tags($html, $allowedTags);

        // Enforce rel and target="_blank" on external <a> tags
        $rel = config('services.beritapraktis.outbound_link_rel', 'noopener');
        $html = preg_replace_callback('/<a\s+([^>]*href\s*=\s*(["\'])(.*?)\2[^>]*)>/iu', function ($m) use ($rel) {
            $attrs = $m[1];
            // Remove existing rel and target to normalize
            $attrs = preg_replace('/\s*(rel|target)\s*=\s*(["\']).*?\2/iu', '', $attrs) ?? $attrs;
            $attrs = trim($attrs);

            return '<a ' . $attrs . ' target="_blank" rel="' . e($rel) . '">';
        }, $html) ?? $html;

        return trim($html);
    }
}
