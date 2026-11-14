<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiNewsWriterService
{
    public function __construct(
        private ArticleValidationService $validator
    ) {}

    public function isConfigured(): bool
    {
        return trim((string) config('services.gemini.key')) !== '';
    }

    /**
     * Rewrite and expand an RSS news summary into a 900+ word ethical Indonesian news article.
     *
     * @return array{title: string, excerpt: string, image_keywords: string, content_html: string}|null
     */
    public function rewriteAndExpand(
        string $originalTitle,
        string $rawSummary,
        string $sourceName,
        string $sourceUrl,
        string $categoryName
    ): ?array {
        $apiKey = trim((string) config('services.gemini.key'));
        if ($apiKey === '') {
            return null;
        }

        $model = trim((string) config('services.gemini.model', 'gemini-2.5-flash')) ?: 'gemini-2.5-flash';
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $prompt = <<<PROMPT
Anda adalah editor senior dan jurnalis profesional media online Indonesia yang menulis berita mendalam, objektif, dan ramah SEO.

Tugas Anda: sadur dan kembangkan berita berikut menjadi artikel berita berbahasa Indonesia yang segar, informatif, dan layak tayang.

DATA SUMBER:
- Kategori: {$categoryName}
- Judul Asli: {$originalTitle}
- Ringkasan Sumber: {$rawSummary}
- Media Sumber: {$sourceName}
- URL Sumber: {$sourceUrl}

ATURAN WAJIB:
1. Panjang isi artikel (`content_html`) WAJIB antara 900 sampai 1100 kata.
2. Gunakan gaya jurnalistik Indonesia yang natural, lugas, dan profesional (hindari kata-kata klise khas AI seperti "di era digital yang serba cepat", "tak dapat dipungkiri", "mari kita telusuri").
3. JAGA ETIKA JURNALISTIK: Jangan mengarang fakta palsu, angka fiktif, atau kutipan wawancara fiktif yang tidak ada di sumber. Kembangkan artikel dengan menjelaskan konteks topik di Indonesia, latar belakang kebijakan/industri, implikasi praktis bagi masyarakat/pelaku usaha, serta hal yang perlu dicermati ke depan.
4. Format `content_html` menggunakan HTML bersih: hanya tag `<p>`, `<h2>`, `<h3>`, `<ul>`, `<li>`, `<strong>`, `<em>`. JANGAN gunakan tag `<h1>`, `<img>`, atau `<a>` di dalam `content_html`.
5. Sertakan minimal 4 subjudul `<h2>` yang relevan dengan isi berita.
6. Dilarang keras mengandung konten judi online, slot, game online ilegal, atau pornografi.
7. Berikan output HANYA dalam format JSON valid dengan field berikut:
   - `title`: Judul berita baru yang informatif, natural, bukan clickbait (maksimal 135 karakter).
   - `excerpt`: Ringkasan meta description SEO yang padat (120-155 karakter).
   - `image_keywords`: 2-3 kata kunci bahasa Inggris paling akurat untuk mencari foto berita ini di Pixabay (contoh: "indonesia parliament meeting" atau "digital finance chart").
   - `content_html`: Isi artikel lengkap dalam HTML bersih (900-1100 kata).
PROMPT;

        try {
            $response = Http::timeout(45)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($endpoint, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.5,
                        'responseMimeType' => 'application/json',
                    ],
                ]);

            if (!$response->successful()) {
                Log::warning('Gemini AI sadur gagal HTTP ' . $response->status(), [
                    'body' => Str::limit($response->body(), 300),
                ]);
                return null;
            }

            $rawText = (string) data_get($response->json(), 'candidates.0.content.parts.0.text', '');
            $rawText = trim($rawText);
            if ($rawText === '') {
                return null;
            }

            // Strip markdown code fences if present
            $rawText = preg_replace('/^```(?:json)?\s*|\s*```$/iu', '', $rawText) ?? $rawText;
            $decoded = json_decode($rawText, true);

            if (!is_array($decoded) || empty($decoded['content_html'])) {
                return null;
            }

            $cleanHtml = $this->validator->sanitizeContent((string) $decoded['content_html']);
            if ($this->validator->countWords($cleanHtml) < 350) {
                return null;
            }

            $title = trim(strip_tags((string) ($decoded['title'] ?? $originalTitle)));
            $excerpt = trim(strip_tags((string) ($decoded['excerpt'] ?? '')));
            $imageKeywords = trim(strip_tags((string) ($decoded['image_keywords'] ?? '')));

            return [
                'title' => Str::limit($title !== '' ? $title : $originalTitle, 175, ''),
                'excerpt' => Str::limit($excerpt !== '' ? $excerpt : $rawSummary, 160),
                'image_keywords' => $imageKeywords,
                'content_html' => $cleanHtml,
            ];
        } catch (\Throwable $e) {
            Log::warning('Gemini AI sadur exception: ' . $e->getMessage());
            return null;
        }
    }
}
