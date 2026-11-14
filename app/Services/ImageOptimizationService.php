<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
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

        if (function_exists('imagewebp') && function_exists('imagecreatefromstring')) {
            $raw = file_get_contents($file->getRealPath());
            $src = $raw !== false ? @imagecreatefromstring($raw) : false;

            if ($src !== false) {
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
                imagewebp($src, $targetDir . DIRECTORY_SEPARATOR . $fileName, 82);
                imagedestroy($src);

                return "uploads/{$subDir}/{$fileName}";
            }
        }

        // Fallback if GD/WebP is unavailable in current PHP environment
        $fallbackExt = $ext ?: 'jpg';
        $fileName = "{$baseName}.{$fallbackExt}";
        $file->move($targetDir, $fileName);

        return "uploads/{$subDir}/{$fileName}";
    }
}
