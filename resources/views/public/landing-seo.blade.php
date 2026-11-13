@extends('layouts.app')

@section('title', $metaTitle)
@section('meta_description', $metaDescription)

@section('content')
<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
    <!-- Editorial Hero -->
    <section class="border-b border-gray-200 pb-10">
        <span class="inline-block rounded border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-red-700">
            {{ $badge }}
        </span>
        <h1 class="mt-4 font-serif text-3xl font-bold leading-tight text-gray-900 sm:text-5xl">
            {{ $h1 }}
        </h1>
        <p class="mt-4 max-w-3xl text-base leading-relaxed text-gray-600 sm:text-lg">
            {{ $subtitle }}
        </p>

        <div class="mt-7 flex flex-wrap items-center gap-3">
            <a href="{{ route('dashboard.articles.create') }}" class="rounded-md bg-red-700 px-6 py-3 text-sm font-semibold text-white hover:bg-red-800 transition">
                Mulai Kirim Sekarang (Gratis)
            </a>
            <a href="{{ route('rules') }}" class="rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-800 hover:bg-gray-50 transition">
                Lihat Syarat &amp; Ketentuan
            </a>
        </div>
    </section>

    <!-- 3 Key Rules Summary -->
    <section class="mt-10 grid grid-cols-1 gap-5 md:grid-cols-3">
        <div class="rounded-md border border-gray-200 bg-white p-5">
            <div class="text-xs font-bold uppercase tracking-wider text-red-700">Syarat 01</div>
            <h2 class="mt-1 font-serif text-xl font-bold text-gray-900">Minimal 900 Kata</h2>
            <p class="mt-2 text-sm leading-relaxed text-gray-600">
                Artikel mendalam minimal 900 kata agar memberikan nilai informasi nyata bagi pembaca serta kuat di indeks Google &amp; AI Search.
            </p>
        </div>
        <div class="rounded-md border border-gray-200 bg-white p-5">
            <div class="text-xs font-bold uppercase tracking-wider text-red-700">Syarat 02</div>
            <h2 class="mt-1 font-serif text-xl font-bold text-gray-900">Maksimal 2 Link</h2>
            <p class="mt-2 text-sm leading-relaxed text-gray-600">
                Setiap artikel diperbolehkan menyertakan hingga 2 tautan keluar (outbound link) yang relevan, bersih, dan legal.
            </p>
        </div>
        <div class="rounded-md border border-gray-200 bg-white p-5">
            <div class="text-xs font-bold uppercase tracking-wider text-red-700">Syarat 03</div>
            <h2 class="mt-1 font-serif text-xl font-bold text-gray-900">Wajib Foto &amp; Bersih</h2>
            <p class="mt-2 text-sm leading-relaxed text-gray-600">
                Wajib melampirkan foto utama yang jelas. Dilarang keras konten judi online, game online ilegal, dan pornografi.
            </p>
        </div>
    </section>

    <!-- How It Works -->
    <section class="mt-12 rounded-md border border-gray-200 bg-white p-6 sm:p-8">
        <h2 class="font-serif text-2xl font-bold text-gray-900">Alur {{ $targetKeyword }} di BeritaPraktis</h2>
        <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-4">
            <div>
                <span class="font-serif text-2xl font-bold text-red-700">1.</span>
                <h3 class="mt-1 text-sm font-bold text-gray-900">Login Google</h3>
                <p class="mt-1 text-xs leading-relaxed text-gray-600">Masuk instan menggunakan akun Google tanpa registrasi rumit.</p>
            </div>
            <div>
                <span class="font-serif text-2xl font-bold text-red-700">2.</span>
                <h3 class="mt-1 text-sm font-bold text-gray-900">Tulis &amp; Cek Validasi</h3>
                <p class="mt-1 text-xs leading-relaxed text-gray-600">Sistem otomatis mengecek 900+ kata, maks 2 link, foto, dan filter kata terlarang.</p>
            </div>
            <div>
                <span class="font-serif text-2xl font-bold text-red-700">3.</span>
                <h3 class="mt-1 text-sm font-bold text-gray-900">Moderasi Redaksi</h3>
                <p class="mt-1 text-xs leading-relaxed text-gray-600">Notifikasi langsung masuk ke Telegram redaksi untuk peninjauan cepat.</p>
            </div>
            <div>
                <span class="font-serif text-2xl font-bold text-red-700">4.</span>
                <h3 class="mt-1 text-sm font-bold text-gray-900">Terbit &amp; Terindeks</h3>
                <p class="mt-1 text-xs leading-relaxed text-gray-600">Artikel tayang lengkap dengan JSON-LD NewsArticle, Sitemap News, &amp; llms.txt.</p>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="mt-12">
        <h2 class="font-serif text-2xl font-bold text-gray-900">Pertanyaan Umum ({{ $targetKeyword }})</h2>
        <div class="mt-5 divide-y divide-gray-200 rounded-md border border-gray-200 bg-white">
            @foreach($faqs as $faq)
                <div class="p-5">
                    <h3 class="text-base font-bold text-gray-900">{{ $faq['question'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-gray-600">{{ $faq['answer'] }}</p>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
