<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImageOptimizationService
{
    /**
     * Resolve the public uploads directory (supports cPanel separated webroot).
     */
    public function getUploadRoot(): string
    {
        $configured = trim((string) config('services.beritapraktis.public_upload_path'));
        if ($configured !== '') {
            return rtrim($configured, '/\\');
        }

        // Auto-detect common cPanel sibling webroot (e.g. /home/user/beritapraktis.biz.id)
        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        if ($appHost) {
            $siblingWebroot = dirname(base_path()) . DIRECTORY_SEPARATOR . $appHost;
            if (is_dir($siblingWebroot)) {
                return $siblingWebroot . DIRECTORY_SEPARATOR . 'uploads';
            }
        }

        return public_path('uploads');
    }

    /**
     * Store and optimize featured image directly into public uploads/articles/YYYY/MM/.
     */
    public function storeFeaturedImage(UploadedFile $file, string $slugHint): string
    {
        $subDir = 'articles/' . now()->format('Y/m');
        $baseName = Str::slug(Str::limit($slugHint, 50, '')) . '-' . Str::random(6);

        $uploadRoot = $this->getUploadRoot();
        $targetDir = $uploadRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $subDir);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: '');
        if ($ext === 'svg' || $file->getMimeType() === 'image/svg+xml') {
            $svg = file_get_contents($file->getRealPath()) ?: '';
            $svg = preg_replace('/<script\b[^>]*>.*?<\/script>/isu', '', $svg) ?? $svg;
            $svg = preg_replace('/<foreignObject\b[^>]*>.*?<\/foreignObject>/isu', '', $svg) ?? $svg;
            $svg = preg_replace('/\s+on[a-z]+\s*=\s*(["\']).*?\1/iu', '', $svg) ?? $svg;
            $svg = preg_replace('/javascript\s*:/iu', '', $svg) ?? $svg;

            $fileName = "{$baseName}.svg";
            file_put_contents($targetDir . DIRECTORY_SEPARATOR . $fileName, $svg);

            return "uploads/{$subDir}/{$fileName}";
        }

        $raw = file_get_contents($file->getRealPath());
        if ($raw !== false) {
            $webpPath = $this->saveAsWebp($raw, $targetDir, $subDir, $baseName);
            if ($webpPath !== null) {
                return $webpPath;
            }
        }

        // Fallback if GD/WebP is unavailable in current PHP environment
        $fallbackExt = $ext ?: 'jpg';
        $fileName = "{$baseName}.{$fallbackExt}";
        $file->move($targetDir, $fileName);

        return "uploads/{$subDir}/{$fileName}";
    }

    /**
     * Download an external image URL (such as temporary Pixabay links) and store it locally so it never expires.
     */
    public function storeFromUrl(string $imageUrl, string $slugHint): ?string
    {
        if (trim($imageUrl) === '') {
            return null;
        }

        try {
            $response = Http::connectTimeout(8)
                ->timeout(15)
                ->withHeaders(['User-Agent' => 'BeritaPraktisBot/1.0'])
                ->get($imageUrl);

            if (!$response->successful()) {
                return null;
            }

            $raw = $response->body();
            if (strlen($raw) < 1024) {
                return null;
            }

            $subDir = 'articles/' . now()->format('Y/m');
            $baseName = (Str::slug(Str::limit($slugHint, 50, '')) ?: 'artikel') . '-' . Str::random(6);

            $uploadRoot = $this->getUploadRoot();
            $targetDir = $uploadRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $subDir);
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $webpPath = $this->saveAsWebp($raw, $targetDir, $subDir, $baseName);
            if ($webpPath !== null) {
                return $webpPath;
            }

            // Fallback if GD/WebP is unavailable: verify it's an image MIME before saving raw
            $contentType = strtolower((string) $response->header('Content-Type'));
            $ext = match (true) {
                str_contains($contentType, 'png') => 'png',
                str_contains($contentType, 'webp') => 'webp',
                str_contains($contentType, 'jpeg'), str_contains($contentType, 'jpg') => 'jpg',
                default => null,
            };

            if ($ext === null) {
                return null;
            }

            $fileName = "{$baseName}.{$ext}";
            file_put_contents($targetDir . DIRECTORY_SEPARATOR . $fileName, $raw);

            return "uploads/{$subDir}/{$fileName}";
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Optimize and copy a local image file (e.g. from public/images/seed) into public uploads/articles/YYYY/MM/.
     */
    public function storeFromLocalFile(string $localFilePath, string $slugHint): ?string
    {
        if (!is_file($localFilePath)) {
            return null;
        }

        $raw = @file_get_contents($localFilePath);
        if ($raw === false || strlen($raw) < 1024) {
            return null;
        }

        $subDir = 'articles/' . now()->format('Y/m');
        $baseName = (Str::slug(Str::limit($slugHint, 50, '')) ?: 'artikel') . '-' . Str::random(6);

        $uploadRoot = $this->getUploadRoot();
        $targetDir = $uploadRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $subDir);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $webpPath = $this->saveAsWebp($raw, $targetDir, $subDir, $baseName);
        if ($webpPath !== null) {
            return $webpPath;
        }

        $ext = strtolower(pathinfo($localFilePath, PATHINFO_EXTENSION) ?: 'jpg');
        $fileName = "{$baseName}.{$ext}";
        if (@copy($localFilePath, $targetDir . DIRECTORY_SEPARATOR . $fileName)) {
            return "uploads/{$subDir}/{$fileName}";
        }

        return null;
    }

    private function saveAsWebp(string $raw, string $targetDir, string $subDir, string $baseName): ?string
    {
        if (!function_exists('imagewebp') || !function_exists('imagecreatefromstring')) {
            return null;
        }

        $src = @imagecreatefromstring($raw);
        if ($src === false) {
            return null;
        }

        $origW = imagesx($src);
        $origH = imagesy($src);
        $maxW = 1200;

        if ($origW > $maxW) {
            $newW = $maxW;
            $newH = (int) round(($origH / $origW) * $newW);
            $dst = imagecreatetruecolor($newW, $newH);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
            imagedestroy($src);
            $src = $dst;
        }

        $fileName = "{$baseName}.webp";
        $saved = imagewebp($src, $targetDir . DIRECTORY_SEPARATOR . $fileName, 82);
        imagedestroy($src);

        return $saved ? "uploads/{$subDir}/{$fileName}" : null;
    }
}
