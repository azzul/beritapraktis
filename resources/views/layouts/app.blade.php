<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'BeritaPraktis — Portal Berita & Press Release Gratis Indonesia')</title>
    <meta name="description" content="@yield('meta_description', 'Portal berita Indonesia dan platform press release gratis, post berita gratis, serta backlink berita berkualitas dengan kurasi redaksi ketat.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    <link rel="alternate" type="application/rss+xml" title="BeritaPraktis RSS Feed" href="{{ route('seo.feed') }}">

    <!-- OpenGraph & Twitter -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="BeritaPraktis">
    <meta property="og:title" content="@yield('title', 'BeritaPraktis — Portal Berita & Press Release Gratis')">
    <meta property="og:description" content="@yield('meta_description', 'Portal berita Indonesia dan platform press release gratis dengan kurasi redaksi.')">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
        <meta name="twitter:image" content="@yield('og_image')">
    @endif
    <meta name="twitter:card" content="summary_large_image">

    <!-- Editorial Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,600;0,6..72,700;1,6..72,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')

    @if(isset($schemas))
        @foreach($schemas as $schema)
            <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @endforeach
    @endif
    @stack('schemas')
</head>
<body class="min-h-screen flex flex-col bg-[#FAF9F6] text-gray-900">
    <!-- Top Editorial Bar -->
    <div class="border-b border-gray-200 bg-white text-xs text-gray-600">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-2 sm:px-6">
            <div class="flex items-center gap-4">
                <span class="font-medium text-gray-800">{{ now()->translatedFormat('l, d F Y') }}</span>
                <span class="hidden sm:inline text-gray-300">|</span>
                <a href="{{ route('seo.press-release') }}" class="hidden sm:inline hover:text-red-700 transition">Press Release Gratis</a>
                <a href="{{ route('seo.post-berita') }}" class="hidden md:inline hover:text-red-700 transition">Post Berita Gratis</a>
                <a href="{{ route('rules') }}" class="hidden md:inline hover:text-red-700 transition">Aturan Penulisan</a>
            </div>
            <div class="flex items-center gap-3">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="font-semibold text-red-700 hover:underline">Panel Redaksi</a>
                        <span class="text-gray-300">|</span>
                    @endif
                    <a href="{{ route('dashboard.articles.index') }}" class="font-medium text-gray-800 hover:text-red-700">Artikel Saya</a>
                @else
                    <a href="{{ route('login') }}" class="font-medium text-gray-700 hover:text-red-700">Masuk Kontributor</a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Masthead Header -->
    <header class="border-b border-gray-200 bg-white sticky top-0 z-30">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3.5 sm:px-6">
            <a href="{{ route('home') }}" class="shrink-0 flex flex-col" aria-label="Beranda BeritaPraktis">
                <img src="{{ asset('images/logo.svg') }}" alt="BeritaPraktis" class="h-8 sm:h-9 w-auto">
                <span class="mt-0.5 text-[10px] sm:text-[11px] font-medium tracking-wide text-gray-500">
                    Portal Berita &amp; Press Release Gratis Indonesia
                </span>
            </a>

            <form action="{{ route('search') }}" method="GET" class="hidden md:flex flex-1 max-w-md">
                <div class="relative w-full">
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari berita, topik, atau press release..."
                        class="w-full rounded-md border border-gray-300 bg-gray-50 px-3.5 py-1.5 pr-9 text-sm text-gray-900 placeholder-gray-500 focus:border-gray-900 focus:bg-white focus:outline-none"
                    >
                    <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-900" aria-label="Cari">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0Z"/></svg>
                    </button>
                </div>
            </form>

            <div class="flex items-center gap-2.5">
                @auth
                    <a href="{{ route('dashboard.articles.create') }}" class="inline-flex items-center gap-1.5 rounded-md bg-red-700 px-3.5 py-2 text-xs sm:text-sm font-semibold text-white hover:bg-red-800 transition">
                        + Kirim Berita
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="rounded-md border border-gray-300 px-3 py-2 text-xs sm:text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('dashboard.articles.create') }}" class="inline-flex items-center gap-1.5 rounded-md bg-gray-900 px-3.5 py-2 text-xs sm:text-sm font-semibold text-white hover:bg-red-700 transition">
                        Kirim Berita Gratis
                    </a>
                @endauth
            </div>
        </div>

        <!-- Category Navigation -->
        @php
            $navCategories = $categories ?? \App\Models\Category::where('is_active', true)->orderBy('sort_order')->get();
        @endphp
        <nav class="border-t border-gray-100 bg-white" aria-label="Kategori Berita">
            <div class="mx-auto flex max-w-7xl items-center gap-6 overflow-x-auto px-4 py-2.5 text-sm font-medium text-gray-700 sm:px-6 whitespace-nowrap">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-red-700 font-semibold' : 'hover:text-red-700' }}">Terkini</a>
                @foreach($navCategories as $navCat)
                    <a href="{{ route('categories.show', $navCat->slug) }}"
                       class="{{ request()->is('kategori/' . $navCat->slug) ? 'text-red-700 font-semibold' : 'hover:text-red-700' }}">
                        {{ $navCat->name }}
                    </a>
                @endforeach
            </div>
        </nav>
    </header>

    <!-- Flash Alerts -->
    @if(session('success') || session('error'))
        <div class="mx-auto w-full max-w-7xl px-4 pt-4 sm:px-6">
            @if(session('success'))
                <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Editorial Footer -->
    <footer class="mt-16 border-t border-gray-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                <div class="md:col-span-2">
                    <img src="{{ asset('images/logo.svg') }}" alt="BeritaPraktis" class="h-8 w-auto">
                    <p class="mt-3 max-w-md text-sm leading-relaxed text-gray-600">
                        BeritaPraktis adalah portal berita independen dan platform publikasi press release gratis Indonesia.
                        Mengutamakan jurnalisme informatif minimal 900 kata, bebas hoaks, judi online, dan pornografi.
                    </p>
                </div>

                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900">Layanan Publikasi</h3>
                    <ul class="mt-3 space-y-2 text-sm text-gray-600">
                        <li><a href="{{ route('seo.press-release') }}" class="hover:text-red-700">Press Release Gratis</a></li>
                        <li><a href="{{ route('seo.backlink-berita') }}" class="hover:text-red-700">Backlink Berita Gratis</a></li>
                        <li><a href="{{ route('seo.post-berita') }}" class="hover:text-red-700">Post Berita Gratis</a></li>
                        <li><a href="{{ route('rules') }}" class="hover:text-red-700">Aturan &amp; Pedoman Redaksi</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-900">Indeks &amp; Standar</h3>
                    <ul class="mt-3 space-y-2 text-sm text-gray-600">
                        <li><a href="{{ route('seo.sitemap') }}" class="hover:text-red-700">Sitemap XML</a></li>
                        <li><a href="{{ route('seo.sitemap-news') }}" class="hover:text-red-700">Google News Sitemap</a></li>
                        <li><a href="{{ route('seo.llms') }}" class="hover:text-red-700">llms.txt</a></li>
                        <li><a href="{{ route('seo.llms-full') }}" class="hover:text-red-700">llms-full.txt</a></li>
                        <li><a href="{{ route('seo.feed') }}" class="hover:text-red-700">RSS Feed</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-8 rounded-md border border-gray-200 bg-gray-50 p-4 text-xs leading-relaxed text-gray-600">
                <strong class="text-gray-900">Disclaimer &amp; Hak Cipta:</strong>
                BeritaPraktis adalah platform publikasi berita dan press release terbuka. Sebagian besar artikel, siaran pers, dan opini dikirimkan oleh kontributor maupun disadur dengan mencantumkan sumber resmi; seluruh isi tulisan menjadi tanggung jawab penuh masing-masing penulis/pengirim dan bukan merupakan pernyataan resmi pemilik situs.
                Apabila Anda menemukan konten, foto, atau materi yang melanggar hak cipta atau memerlukan penghapusan (takedown), silakan hubungi tim redaksi melalui email
                <a href="mailto:info@kabarpraktis.biz.id" class="font-semibold text-red-700 underline">info@kabarpraktis.biz.id</a>.
            </div>

            <div class="mt-6 flex flex-col items-center justify-between gap-4 border-t border-gray-100 pt-6 text-xs text-gray-500 sm:flex-row">
                <p>&copy; {{ date('Y') }} BeritaPraktis. Seluruh hak cipta dilindungi.</p>
                <p>Kontak Redaksi &amp; Takedown: <a href="mailto:info@kabarpraktis.biz.id" class="hover:text-red-700">info@kabarpraktis.biz.id</a></p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
