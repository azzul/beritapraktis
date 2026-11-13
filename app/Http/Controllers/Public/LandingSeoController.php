<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\SeoSchemaService;
use Illuminate\View\View;

class LandingSeoController extends Controller
{
    public function pressReleaseGratis(SeoSchemaService $seo): View
    {
        $faqs = [
            [
                'question' => 'Apakah kirim press release di BeritaPraktis benar-benar gratis?',
                'answer' => 'Ya, 100% gratis tanpa biaya sepeser pun. Anda cukup login menggunakan akun Google, menulis press release minimal 900 kata, melampirkan foto utama, dan mengirimkannya untuk moderasi redaksi.',
            ],
            [
                'question' => 'Berapa lama proses approval press release?',
                'answer' => 'Setiap artikel yang masuk langsung mengirimkan notifikasi ke tim redaksi melalui Telegram. Proses peninjauan umumnya berlangsung dalam hitungan jam hingga maksimal 1x24 jam.',
            ],
            [
                'question' => 'Berapa jumlah link yang diperbolehkan dalam satu press release?',
                'answer' => 'Maksimal 2 tautan keluar (outbound link) yang relevan, legal, dan tidak mengarah ke situs judi online, game online ilegal, atau pornografi.',
            ],
        ];

        return view('public.landing-seo', [
            'slug' => 'press-release-gratis',
            'metaTitle' => 'Press Release Gratis — Publikasi Siaran Pers Media Online | BeritaPraktis',
            'metaDescription' => 'Layanan press release gratis untuk perusahaan, startup, UMKM, dan komunitas. Terindeks cepat di Google News & AI Search, bebas biaya, moderasi cepat via redaksi.',
            'h1' => 'Press Release Gratis untuk Brand, Startup, & UMKM Indonesia',
            'subtitle' => 'Publikasikan siaran pers resmi Anda di portal berita nasional secara gratis. Teroptimasi SEO News, JSON-LD, dan ramah crawler AI.',
            'badge' => 'Layanan Publikasi Gratis',
            'targetKeyword' => 'Press Release Gratis',
            'faqs' => $faqs,
            'schemas' => [
                $seo->faqSchema($faqs),
                $seo->breadcrumbSchema([
                    ['name' => 'Beranda', 'url' => route('home')],
                    ['name' => 'Press Release Gratis', 'url' => route('seo.press-release')],
                ]),
            ],
        ]);
    }

    public function backlinkBeritaGratis(SeoSchemaService $seo): View
    {
        $faqs = [
            [
                'question' => 'Apakah bisa mendapatkan backlink berita gratis di BeritaPraktis?',
                'answer' => 'Bisa. Setiap kontributor yang mempublikasikan artikel berita atau press release berkualitas minimal 900 kata diperbolehkan menyertakan maksimal 2 tautan referensi yang relevan.',
            ],
            [
                'question' => 'Apakah semua topik website boleh ditanam backlink?',
                'answer' => 'Tidak. Kami melarang keras tautan maupun konten terkait judi online, game online, dan pornografi. Pelanggaran akan langsung ditolak dan akun diblokir permanen.',
            ],
            [
                'question' => 'Mengapa syarat artikel harus minimal 900 kata?',
                'answer' => 'Artikel mendalam minimal 900 kata jauh lebih disukai mesin pencari Google dan LLM, sehingga nilai otoritas halaman berita dan tautan di dalamnya jauh lebih kuat.',
            ],
        ];

        return view('public.landing-seo', [
            'slug' => 'backlink-berita-gratis',
            'metaTitle' => 'Backlink Berita Gratis Berkualitas & Terindeks Google | BeritaPraktis',
            'metaDescription' => 'Dapatkan backlink berita gratis dari artikel editorial minimal 900 kata. Maksimal 2 link relevan per artikel, bersih dari spam, judi online, dan pornografi.',
            'h1' => 'Backlink Berita Gratis dari Artikel Editorial Berkualitas',
            'subtitle' => 'Tingkatkan otoritas domain dan visibilitas brand Anda melalui publikasi artikel informatif minimal 900 kata dengan maksimal 2 tautan referensi.',
            'badge' => 'SEO & Otoritas Media',
            'targetKeyword' => 'Backlink Berita Gratis',
            'faqs' => $faqs,
            'schemas' => [
                $seo->faqSchema($faqs),
                $seo->breadcrumbSchema([
                    ['name' => 'Beranda', 'url' => route('home')],
                    ['name' => 'Backlink Berita Gratis', 'url' => route('seo.backlink-berita')],
                ]),
            ],
        ]);
    }

    public function postBeritaGratis(SeoSchemaService $seo): View
    {
        $faqs = [
            [
                'question' => 'Bagaimana cara post berita gratis di BeritaPraktis?',
                'answer' => 'Klik tombol Masuk dengan Google, pilih kategori berita (Politik, Bisnis, Teknologi, Pertanian, dll.), tulis artikel minimal 900 kata dengan foto utama, lalu klik Kirim untuk direview redaksi.',
            ],
            [
                'question' => 'Kategori berita apa saja yang tersedia?',
                'answer' => 'Tersedia kategori Politik, Bisnis & Ekonomi, Teknologi, Pertanian, Pendidikan, Hukum & Kriminal, Gaya Hidup, dan Press Release.',
            ],
            [
                'question' => 'Apakah tulisan saya langsung terbit?',
                'answer' => 'Semua kiriman berita akan melalui tahap moderasi redaksi terlebih dahulu untuk memastikan kualitas, keaslian foto, dan bebas dari konten terlarang.',
            ],
        ];

        return view('public.landing-seo', [
            'slug' => 'post-berita-gratis',
            'metaTitle' => 'Post Berita Gratis — Kirim & Terbitkan Berita Online | BeritaPraktis',
            'metaDescription' => 'Platform post berita gratis untuk jurnalis warga, humas, dan penulis independen. Login mudah pakai Google, terbit cepat setelah kurasi redaksi.',
            'h1' => 'Post Berita Gratis & Bagikan Informasi ke Pembaca Luas',
            'subtitle' => 'Punya berita daerah, liputan teknologi, agribisnis pertanian, atau opini bisnis? Kirim tulisan Anda sekarang secara gratis.',
            'badge' => 'Jurnalisme Warga & Kontributor',
            'targetKeyword' => 'Post Berita Gratis',
            'faqs' => $faqs,
            'schemas' => [
                $seo->faqSchema($faqs),
                $seo->breadcrumbSchema([
                    ['name' => 'Beranda', 'url' => route('home')],
                    ['name' => 'Post Berita Gratis', 'url' => route('seo.post-berita')],
                ]),
            ],
        ]);
    }

    public function rules(): View
    {
        return view('public.rules');
    }
}
