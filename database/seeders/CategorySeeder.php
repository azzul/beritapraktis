<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Politik', 'slug' => 'politik', 'description' => 'Berita politik nasional, kebijakan publik, pemilu, dan dinamika pemerintahan terkini.', 'sort_order' => 1],
            ['name' => 'Bisnis & Ekonomi', 'slug' => 'bisnis-ekonomi', 'description' => 'Liputan bisnis, UMKM, investasi, startup, korporasi, dan perkembangan ekonomi Indonesia.', 'sort_order' => 2],
            ['name' => 'Teknologi', 'slug' => 'teknologi', 'description' => 'Inovasi teknologi, kecerdasan buatan, transformasi digital, dan rilis produk terbaru.', 'sort_order' => 3],
            ['name' => 'Pertanian', 'slug' => 'pertanian', 'description' => 'Berita agribisnis, ketahanan pangan, teknologi pertanian, dan pemberdayaan petani.', 'sort_order' => 4],
            ['name' => 'Pendidikan', 'slug' => 'pendidikan', 'description' => 'Informasi dunia pendidikan, riset kampus, beasiswa, dan kebijakan kurikulum.', 'sort_order' => 5],
            ['name' => 'Hukum & Kriminal', 'slug' => 'hukum-kriminal', 'description' => 'Berita hukum, regulasi terbaru, penegakan keadilan, dan peristiwa kriminal.', 'sort_order' => 6],
            ['name' => 'Gaya Hidup', 'slug' => 'gaya-hidup', 'description' => 'Artikel kesehatan, kuliner, wisata, budaya, dan tren gaya hidup masyarakat modern.', 'sort_order' => 7],
            ['name' => 'Press Release', 'slug' => 'press-release', 'description' => 'Siaran pers resmi perusahaan, instansi, komunitas, dan organisasi di Indonesia.', 'sort_order' => 8],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
