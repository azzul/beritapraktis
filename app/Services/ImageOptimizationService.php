<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizationService
{
    /**
     * Store and optimize featured image to WebP (when GD is available) with max width 1200px.
     */
    public function storeFeaturedImage(UploadedFile $file, string $slugHint): string
    {
        $dir = 'articles/' . now()->format('Y/m');
        $baseName = Str::slug(Str::limit($slugHint, 50, '')) . '-' . Str::random(6);

        // Ensure directory exists on public disk
        Storage::disk('public')->makeDirectory($dir);

        $ext = strtolower($file->getClientOriginalExtension() ?: '');
        if ($ext === 'svg' || $file->getMimeType() === 'image/svg+xml') {
            $svg = file_get_contents($file->getRealPath()) ?: '';
            $svg = preg_replace('/<script\b[^>]*>.*?<\/script>/isu', '', $svg) ?? $svg;
            $svg = preg_replace('/<foreignObject\b[^>]*>.*?<\/foreignObject>/isu', '', $svg) ?? $svg;
            $svg = preg_replace('/\s+on[a-z]+\s*=\s*(["\']).*?\1/iu', '', $svg) ?? $svg;
            $svg = preg_replace('/javascript\s*:/iu', '', $svg) ?? $svg;

            $relativePath = "{$dir}/{$baseName}.svg";
            Storage::disk('public')->put($relativePath, $svg);

            return $relativePath;
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

                $relativePath = "{$dir}/{$baseName}.webp";
                $fullPath = Storage::disk('public')->path($relativePath);
                imagewebp($src, $fullPath, 82);
                imagedestroy($src);

                return $relativePath;
            }
        }

        // Fallback if GD/WebP is unavailable in current PHP environment
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $relativePath = "{$dir}/{$baseName}.{$ext}";
        $file->storeAs($dir, "{$baseName}.{$ext}", 'public');

        return $relativePath;
    }
}
