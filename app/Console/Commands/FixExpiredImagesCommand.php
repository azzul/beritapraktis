<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Services\ImageOptimizationService;
use App\Services\RssSadurService;
use Illuminate\Console\Command;

class FixExpiredImagesCommand extends Command
{
    protected $signature = 'news:fix-expired-images';
    protected $description = 'Download and replace expiring Pixabay/external article images with permanent local WebP files';

    public function handle(ImageOptimizationService $imageService, RssSadurService $sadurService): int
    {
        $articles = Article::with('category')
            ->where('featured_image', 'like', '%pixabay.com%')
            ->get();

        if ($articles->isEmpty()) {
            $this->info('Tidak ada artikel dengan URL Pixabay sementara. Semua foto aman!');
            return self::SUCCESS;
        }

        $this->info("Menemukan {$articles->count()} artikel dengan URL Pixabay sementara. Memperbaiki...");
        $usedImages = Article::pluck('featured_image')->filter()->all();
        $fixed = 0;

        foreach ($articles as $article) {
            // Try downloading current URL first in case it hasn't expired yet
            $localPath = $imageService->storeFromUrl((string) $article->featured_image, $article->slug);

            if ($localPath !== null) {
                $article->update(['featured_image' => $localPath]);
                $usedImages[] = $localPath;
                $fixed++;
                $this->line("OK (saved current): {$article->title}");
                continue;
            }

            // Expired — fetch a fresh photo for this article and store locally
            $categorySlug = $article->category?->slug ?? 'bisnis-ekonomi';
            $photo = $sadurService->fetchUniquePhoto($article->title, $categorySlug, $usedImages, '', $article->slug);
            $usedImages[] = $photo['url'];

            $article->update([
                'featured_image' => $photo['url'],
                'image_caption' => $photo['credit'] . ' | BeritaPraktis',
            ]);
            $fixed++;
            $this->line("OK (replaced expired): {$article->title}");
        }

        $this->info("Selesai! {$fixed} artikel berhasil diperbarui ke foto lokal permanen.");

        return self::SUCCESS;
    }
}
