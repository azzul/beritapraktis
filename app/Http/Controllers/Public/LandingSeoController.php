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
                'answer' => 'Kontributor reguler diperbolehkan menyertakan maksimal 2 tautan keluar (outbound link) dofollow yang relevan, legal, dan tidak mengarah ke situs judi online, game online ilegal, atau pornografi.',
            ],
            [
                'question' => 'Siapa saja yang boleh mengirim press release gratis di sini?',
                'answer' => 'Startup, UMKM, perusahaan korporasi, agensi PR/digital marketing, lembaga pendidikan, instansi pemerintah, komunitas, hingga penyelenggara acara di seluruh Indonesia.',
            ],
            [
                'question' => 'Apakah press release yang tayang langsung masuk Sitemap Google News dan AI Search?',
                'answer' => 'Ya. Begitu disetujui admin, artikel otomatis masuk ke sitemap-news.xml, sitemap-articles.xml, RSS feed, serta endpoint llms.txt dan llms-full.txt agar cepat dirayapi Google dan mesin pencari berbasis AI.',
            ],
        ];

        $benefits = [
            ['title' => '100% Gratis Tanpa Biaya Tayang', 'desc' => 'Tidak ada biaya langganan atau tarif publikasi per artikel bagi UMKM, startup, maupun humas instansi.'],
            ['title' => 'Struktur JSON-LD NewsArticle', 'desc' => 'Setiap siaran pers otomatis dibekali skema NewsArticle, BreadcrumbList, dan OpenGraph agar siap tampil di Google Search.'],
            ['title' => 'Sitemap News & RSS Auto-Update', 'desc' => 'Setiap kali artikel disetujui, cache sitemap dan feed RSS langsung diperbarui untuk mempercepat indeksasi.'],
            ['title' => 'Ramah AI Search (llms.txt)', 'desc' => 'Konten Anda juga terekspos ke ekosistem AI Crawler melalui /llms.txt dan /llms-full.txt untuk visibilitas masa depan.'],
            ['title' => 'Tautan Dofollow Relevan', 'desc' => 'Sertakan hingga 2 tautan resmi perusahaan atau halaman produk untuk mendukung otoritas digital brand Anda.'],
            ['title' => 'Moderasi Cepat via Telegram', 'desc' => 'Tim redaksi menerima notifikasi real-time sehingga proses review berjalan cepat dan transparan di dashboard Anda.'],
        ];

        $sections = [
            [
                'heading' => 'Mengapa Publikasi Press Release Gratis Penting bagi Bisnis & Startup?',
                'paragraphs' => [
                    'Di tengah persaingan digital yang semakin ketat, membangun kepercayaan publik (public trust) dan jejak digital yang kredibel di mesin pencari menjadi kebutuhan utama bagi setiap merek, startup, UMKM, maupun instansi. Namun, biaya publikasi siaran pers di media massa konvensional sering kali mahal dan tidak terjangkau oleh pelaku usaha rintisan atau bisnis lokal yang baru berkembang.',
                    'BeritaPraktis hadir menjawab tantangan tersebut dengan menyediakan platform press release gratis yang menerapkan standar kurasi editorial ketat. Setiap siaran pers yang masuk tidak sekadar ditayangkan mentah-mentah, melainkan melalui penyaringan kualitas minimal 900 kata, kelengkapan foto utama beresolusi baik, serta filter bebas konten terlarang seperti judi online, game online ilegal, dan pornografi. Dengan ekosistem yang bersih, reputasi brand Anda tetap terjaga saat tampil berdampingan dengan berita-berita nasional lainnya.',
                ],
                'bullets' => [],
            ],
            [
                'heading' => 'Jenis Siaran Pers yang Cocok Dipublikasikan di BeritaPraktis',
                'paragraphs' => [
                    'Anda dapat memanfaatkan layanan press release gratis ini untuk berbagai kebutuhan komunikasi korporasi maupun pemasaran produk secara legal dan profesional:',
                ],
                'bullets' => [
                    'Peluncuran produk baru, aplikasi digital, layanan SaaS, maupun pembukaan cabang usaha baru di berbagai kota.',
                    'Pengumuman pendanaan startup, ekspansi bisnis, kerja sama strategis B2B, atau akuisisi dan kemitraan.',
                    'Laporan kegiatan CSR (Corporate Social Responsibility), seminar nasional, pameran UMKM, dan webinar edukasi.',
                    'Rilis riset industri, survei pasar tahunan, penghargaan perusahaan, maupun klarifikasi resmi dari manajemen.',
                ],
            ],
            [
                'heading' => 'Tips Menulis Press Release 900+ Kata agar Cepat Disetujui Redaksi',
                'paragraphs' => [
                    'Agar siaran pers Anda langsung lolos moderasi tanpa revisi, terapkan kaidah piramida terbalik khas jurnalistik. Mulailah paragraf pembuka dengan unsur 5W+1H (Siapa, Apa, Kapan, Di mana, Mengapa, dan Bagaimana) secara ringkas dan padat.',
                    'Selanjutnya, lengkapi badan berita dengan latar belakang masalah yang diselesaikan oleh produk atau acara Anda, kutipan resmi dari pimpinan atau narasumber terkait, data pendukung yang valid, serta detail informasi akses layanan pada bagian penutup. Gunakan subjudul (H2/H3) agar pembaca nyaman memindai isi tulisan dan mesin pencari mudah memahami struktur topik artikel Anda.',
                    'Pastikan foto utama yang diunggah tajam, relevan, tidak mengandung watermark berlebih, serta dilengkapi Alt Text yang deskriptif. Untuk referensi lebih lengkap, Anda juga dapat membaca panduan lengkap di halaman Aturan Penulisan Press Release atau memanfaatkan layanan backlink berita gratis dan post berita gratis sesuai kebutuhan publikasi Anda.',
                ],
                'bullets' => [],
            ],
        ];

        return view('public.landing-seo', [
            'slug' => 'press-release-gratis',
            'metaTitle' => 'Press Release Gratis — Publikasi Siaran Pers Media Online | BeritaPraktis',
            'metaDescription' => 'Layanan press release gratis untuk perusahaan, startup, UMKM, dan komunitas. Terindeks cepat di Google News & AI Search, bebas biaya, moderasi cepat via redaksi.',
            'ogImage' => asset('images/og-press-release.jpg'),
            'h1' => 'Press Release Gratis untuk Brand, Startup, & UMKM Indonesia',
            'subtitle' => 'Publikasikan siaran pers resmi Anda di portal berita nasional secara gratis. Teroptimasi SEO News, JSON-LD, dan ramah crawler AI.',
            'badge' => 'Layanan Publikasi Gratis',
            'targetKeyword' => 'Press Release Gratis',
            'benefits' => $benefits,
            'sections' => $sections,
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
                'answer' => 'Bisa. Setiap kontributor yang mempublikasikan artikel berita atau press release berkualitas minimal 900 kata diperbolehkan menyertakan maksimal 2 tautan referensi dofollow yang relevan.',
            ],
            [
                'question' => 'Apakah link yang ditanam di artikel bersifat dofollow?',
                'answer' => 'Ya, secara default tautan keluar pada artikel menggunakan atribut rel="noopener" (dofollow) selama tautan tersebut relevan, alami, dan bukan spam.',
            ],
            [
                'question' => 'Apakah semua topik website boleh ditanam backlink?',
                'answer' => 'Tidak. Kami melarang keras tautan maupun konten terkait judi online, slot, taruhan, game online ilegal, dan pornografi. Pelanggaran akan langsung ditolak dan akun diblokir permanen.',
            ],
            [
                'question' => 'Mengapa syarat artikel harus minimal 900 kata?',
                'answer' => 'Artikel mendalam minimal 900 kata jauh lebih disukai mesin pencari Google dan LLM, sehingga nilai otoritas halaman berita dan tautan di dalamnya jauh lebih kuat dibandingkan artikel tipis 300 kata.',
            ],
            [
                'question' => 'Berapa banyak artikel yang boleh saya kirim dalam sebulan?',
                'answer' => 'Setiap akun kontributor mendapatkan kuota hingga 20 artikel per bulan secara gratis, cukup untuk mendukung strategi SEO dan branding bisnis Anda secara konsisten.',
            ],
        ];

        $benefits = [
            ['title' => 'Tautan Kontekstual Dofollow', 'desc' => 'Link ditempatkan secara alami di dalam paragraf artikel berita panjang sehingga bernilai tinggi bagi SEO off-page.'],
            ['title' => 'Bebas Lingkungan Spam & Judol', 'desc' => 'Filter otomatis dan moderasi manusia menjaga kebersihan domain BeritaPraktis dari tautan berisiko penalti Google.'],
            ['title' => 'Artikel Panjang 900+ Kata', 'desc' => 'Konten mendalam meningkatkan relevansi semantik topik antara artikel berita dan website tujuan Anda.'],
            ['title' => 'Optimasi Teknis SEO Lengkap', 'desc' => 'Dilengkapi canonical URL, meta tag otomatis, JSON-LD NewsArticle, dan sitemap XML yang selalu mutakhir.'],
            ['title' => 'Kuota 20 Artikel / Bulan', 'desc' => 'Kontributor aktif dapat membangun profil backlink media yang natural secara bertahap setiap bulan tanpa biaya.'],
            ['title' => 'Pantau Status di Dashboard', 'desc' => 'Cek kapan saja apakah artikel Anda sedang menunggu approval, sudah tayang, atau perlu perbaikan.'],
        ];

        $sections = [
            [
                'heading' => 'Strategi Backlink Berita Gratis yang Aman dari Algoritma Google',
                'paragraphs' => [
                    'Dalam praktik Search Engine Optimization (SEO) modern, kualitas backlink jauh lebih menentukan daripada kuantitas semata. Google Spam Update secara konsisten menargetkan tautan dari situs yang penuh spam, artikel pendek hasil generate massal, atau situs yang menerima tautan judi online. Karena itulah BeritaPraktis membangun ekosistem portal berita yang bersih dan terkurasi sebagai sumber backlink berita gratis yang aman bagi website bisnis Anda.',
                    'Dengan mewajibkan setiap artikel memiliki panjang minimal 900 kata, foto utama yang relevan, serta pembatasan maksimal 2 tautan keluar per artikel, setiap halaman berita memiliki bobot editorial yang kuat. Tautan yang disisipkan secara kontekstual di dalam pembahasan yang mendalam membantu mesin pencari memahami relevansi antara artikel berita di BeritaPraktis dengan layanan atau informasi pada situs web tujuan Anda.',
                ],
                'bullets' => [],
            ],
            [
                'heading' => 'Praktik Terbaik Menempatkan Anchor Text pada Artikel Berita',
                'paragraphs' => [
                    'Agar backlink berita Anda memberikan dampak optimal tanpa memicu sinyal over-optimasi, perhatikan beberapa panduan praktis berikut sebelum mengirim tulisan:',
                ],
                'bullets' => [
                    'Gunakan variasi anchor text yang natural seperti nama brand, URL resmi, atau frasa deskriptif yang menyatu dengan kalimat.',
                    'Tempatkan tautan pada bagian isi artikel yang benar-benar relevan dengan halaman tujuan, bukan dipaksakan di paragraf pembuka tanpa konteks.',
                    'Pastikan halaman tujuan (landing page) memiliki konten informatif, cepat diakses, menggunakan HTTPS, dan bebas dari materi terlarang.',
                    'Lengkapi artikel dengan subjudul H2/H3 dan data yang bermanfaat agar pembaca betah membaca hingga selesai (dwell time tinggi).',
                ],
            ],
            [
                'heading' => 'Mengapa Memilih BeritaPraktis Dibandingkan Direktori Artikel Biasa?',
                'paragraphs' => [
                    'Direktori artikel konvensional sering kali dipenuhi ribuan tautan keluar yang tidak terkontrol sehingga otoritasnya lemah. Di BeritaPraktis, seluruh artikel dimoderasi secara manual oleh admin melalui notifikasi Telegram real-time dan sistem penyaringan kata kunci terlarang otomatis.',
                    'Selain itu, setiap artikel yang disetujui langsung masuk ke sitemap berita, RSS feed, serta indeks llms.txt untuk AI Search. Anda juga dapat mengombinasikan strategi ini dengan fitur press release gratis untuk rilis resmi perusahaan maupun post berita gratis untuk liputan topik industri Anda.',
                ],
                'bullets' => [],
            ],
        ];

        return view('public.landing-seo', [
            'slug' => 'backlink-berita-gratis',
            'metaTitle' => 'Backlink Berita Gratis Berkualitas & Terindeks Google | BeritaPraktis',
            'metaDescription' => 'Dapatkan backlink berita gratis dofollow dari artikel editorial minimal 900 kata. Maksimal 2 link relevan per artikel, bersih dari spam, judi online, dan pornografi.',
            'ogImage' => asset('images/og-backlink-berita.jpg'),
            'h1' => 'Backlink Berita Gratis dari Artikel Editorial Berkualitas',
            'subtitle' => 'Tingkatkan otoritas domain dan visibilitas brand Anda melalui publikasi artikel informatif minimal 900 kata dengan maksimal 2 tautan referensi.',
            'badge' => 'SEO & Otoritas Media',
            'targetKeyword' => 'Backlink Berita Gratis',
            'benefits' => $benefits,
            'sections' => $sections,
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
            [
                'question' => 'Apakah penulis pemula atau jurnalis warga boleh ikut mengirim berita?',
                'answer' => 'Tentu saja. Platform ini terbuka bagi jurnalis warga, blogger, mahasiswa, pelaku UMKM, komunitas daerah, hingga praktisi industri yang ingin berbagi informasi positif dan faktual.',
            ],
            [
                'question' => 'Bagaimana cara mengetahui berita saya sudah disetujui atau ditolak?',
                'answer' => 'Anda dapat memantau status setiap tulisan secara langsung di menu Dashboard Kontributor > Artikel Saya (Tayang, Menunggu Approval, atau Ditolak beserta alasannya).',
            ],
        ];

        $benefits = [
            ['title' => 'Login Instan Pakai Google', 'desc' => 'Tidak perlu mengisi formulir pendaftaran panjang; masuk satu klik menggunakan akun Google Anda.'],
            ['title' => '8 Kanal Berita Lengkap', 'desc' => 'Pilih kategori Politik, Bisnis & Ekonomi, Teknologi, Pertanian, Pendidikan, Hukum, Gaya Hidup, atau Press Release.'],
            ['title' => 'Validator Real-Time Sebelum Kirim', 'desc' => 'Editor otomatis menghitung jumlah kata, jumlah tautan, kelengkapan foto, dan kata terlarang secara langsung.'],
            ['title' => 'Kompresi Gambar Otomatis WebP', 'desc' => 'Foto yang Anda unggah otomatis dioptimasi ke format WebP ringan agar halaman berita cepat dimuat di ponsel.'],
            ['title' => 'Terindeks Google & AI Crawler', 'desc' => 'Berita yang tayang otomatis masuk sitemap XML, RSS feed, dan llms.txt untuk menjangkau pembaca lebih luas.'],
            ['title' => 'Dashboard Pantau Status Tulisan', 'desc' => 'Lacak status moderasi, jumlah kata, hingga tautan langsung ke artikel yang sudah tayang dari satu halaman.'],
        ];

        $sections = [
            [
                'heading' => 'Platform Post Berita Gratis untuk Jurnalisme Warga & Kontributor Aktif',
                'paragraphs' => [
                    'Banyak informasi penting di daerah, inovasi pertanian di desa, kisah sukses UMKM lokal, kegiatan kampus, hingga perkembangan komunitas teknologi yang luput dari sorotan media arus utama. Melalui layanan post berita gratis di BeritaPraktis, siapa pun kini memiliki ruang publikasi yang rapi, modern, ringan, dan terstruktur layaknya portal berita profesional.',
                    'Kami merancang pengalaman menulis yang bebas ribet namun tetap menjaga standar kualitas jurnalistik. Anda cukup login dengan akun Google, menulis artikel langsung di editor kaya fitur (mendukung subjudul, daftar poin, kutipan, dan tautan), lalu memantau indikator kelayakan di sisi kanan layar sebelum menekan tombol kirim.',
                ],
                'bullets' => [],
            ],
            [
                'heading' => 'Topik Tulisan yang Sangat Diminati Pembaca BeritaPraktis',
                'paragraphs' => [
                    'Agar tulisan Anda dibaca banyak orang dan berpotensi tinggi muncul di halaman pertama pencarian, Anda dapat mengangkat topik-topik yang relevan dengan kebutuhan masyarakat Indonesia:',
                ],
                'bullets' => [
                    'Politik & Kebijakan Publik: rangkuman kebijakan pemerintah daerah, layanan publik terbaru, dan partisipasi masyarakat.',
                    'Bisnis, Ekonomi & UMKM: tips pengelolaan usaha, tren perdagangan digital, peluang ekspor, dan kisah inspiratif pengusaha lokal.',
                    'Teknologi & AI: ulasan teknologi tepat guna, keamanan siber untuk masyarakat, inovasi startup lokal, dan transformasi digital.',
                    'Pertanian & Ketahanan Pangan: teknik budidaya modern, agribisnis, inovasi pupuk organik, dan perkembangan harga komoditas.',
                    'Pendidikan, Hukum & Gaya Hidup: beasiswa, kegiatan kampus, literasi hukum sehari-hari, kesehatan preventif, dan wisata daerah.',
                ],
            ],
            [
                'heading' => 'Komitmen Etika Publikasi dan Perlindungan Pembaca',
                'paragraphs' => [
                    'BeritaPraktis berkomitmen menjadi ruang informasi yang sehat dan dapat dipercaya. Oleh karena itu, sistem kami dilengkapi filter otomatis maupun moderasi manual untuk menolak segala bentuk promosi judi online, game online ilegal, konten pornografi, ujaran kebencian, maupun berita bohong (hoaks).',
                    'Jika Anda juga ingin mempublikasikan siaran pers perusahaan atau membangun otoritas situs web, manfaatkan halaman press release gratis serta panduan backlink berita gratis yang tersedia di situs ini.',
                ],
                'bullets' => [],
            ],
        ];

        return view('public.landing-seo', [
            'slug' => 'post-berita-gratis',
            'metaTitle' => 'Post Berita Gratis — Kirim & Terbitkan Berita Online | BeritaPraktis',
            'metaDescription' => 'Platform post berita gratis untuk jurnalis warga, humas, dan penulis independen. Login mudah pakai Google, terbit cepat setelah kurasi redaksi.',
            'ogImage' => asset('images/og-post-berita.jpg'),
            'h1' => 'Post Berita Gratis & Bagikan Informasi ke Pembaca Luas',
            'subtitle' => 'Punya berita daerah, liputan teknologi, agribisnis pertanian, atau opini bisnis? Kirim tulisan Anda sekarang secara gratis.',
            'badge' => 'Jurnalisme Warga & Kontributor',
            'targetKeyword' => 'Post Berita Gratis',
            'benefits' => $benefits,
            'sections' => $sections,
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
