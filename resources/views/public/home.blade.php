@extends('layouts.app')

@section('title', 'BeritaPraktis — Berita Terkini & Platform Press Release Gratis Indonesia')
@section('meta_description', 'Baca berita terkini politik, bisnis, teknologi, pertanian, dan kirim press release gratis atau post berita gratis di BeritaPraktis.')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6">
    @if($headline)
        <!-- Top Headline + Popular Sidebar -->
        <section class="grid grid-cols-1 gap-8 border-b border-gray-200 pb-10 lg:grid-cols-12">
            <article class="lg:col-span-8">
                <a href="{{ route('articles.show', $headline->slug) }}" class="group block">
                    <div class="aspect-[16/9] w-full overflow-hidden rounded-md bg-gray-100 border border-gray-200">
                        <img
                            src="{{ $headline->image_url }}"
                            alt="{{ $headline->image_alt }}"
                            fetchpriority="high"
                            class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.02]"
                        >
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-red-700">
                            <span>{{ $headline->category->name }}</span>
                            <span class="text-gray-300">&bull;</span>
                            <span class="text-gray-500 font-normal normal-case">{{ optional($headline->published_at)->diffForHumans() }}</span>
                        </div>
                        <h1 class="mt-2 font-serif text-2xl font-bold leading-tight text-gray-900 group-hover:text-red-700 sm:text-4xl">
                            {{ $headline->title }}
                        </h1>
                        <p class="mt-3 text-base leading-relaxed text-gray-600 line-clamp-3">
                            {{ $headline->excerpt }}
                        </p>
                    </div>
                </a>
            </article>

            <!-- Sidebar: Terpopuler + CTA Box -->
            <aside class="lg:col-span-4 flex flex-col justify-between gap-6">
                <div class="rounded-md border border-gray-200 bg-white p-5">
                    <h2 class="border-b border-gray-200 pb-2.5 text-xs font-bold uppercase tracking-wider text-gray-900">
                        Berita Terpopuler
                    </h2>
                    <div class="divide-y divide-gray-100">
                        @forelse($popularArticles as $idx => $pop)
                            <a href="{{ route('articles.show', $pop->slug) }}" class="group flex items-start gap-3 py-3.5">
                                <span class="font-serif text-xl font-bold text-gray-300 group-hover:text-red-700">0{{ $idx + 1 }}</span>
                                <div>
                                    <span class="text-[11px] font-semibold uppercase tracking-wide text-red-700">{{ $pop->category->name }}</span>
                                    <h3 class="mt-0.5 text-sm font-semibold leading-snug text-gray-900 group-hover:text-red-700 line-clamp-2">
                                        {{ $pop->title }}
                                    </h3>
                                </div>
                            </a>
                        @empty
                            <p class="py-4 text-sm text-gray-500">Belum ada berita populer.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-md border border-gray-900 bg-gray-900 p-5 text-white">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-red-400">Publikasi Terbuka</span>
                    <h2 class="mt-1 font-serif text-xl font-bold leading-snug">Ingin Kirim Press Release Gratis?</h2>
                    <p class="mt-2 text-xs leading-relaxed text-gray-300">
                        Login pakai Google, tulis minimal 900 kata, lampirkan foto, dan terbitkan berita Anda setelah kurasi redaksi.
                    </p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('dashboard.articles.create') }}" class="rounded bg-red-700 px-3.5 py-2 text-xs font-semibold text-white hover:bg-red-600 transition">
                            Kirim Artikel Sekarang
                        </a>
                        <a href="{{ route('rules') }}" class="rounded border border-gray-600 px-3.5 py-2 text-xs font-medium text-gray-200 hover:bg-gray-800 transition">
                            Baca Aturan
                        </a>
                    </div>
                </div>
            </aside>
        </section>
    @else
        <!-- Empty State when no articles published yet -->
        <section class="rounded-md border border-gray-200 bg-white p-10 text-center">
            <h1 class="font-serif text-3xl font-bold text-gray-900">Selamat Datang di BeritaPraktis</h1>
            <p class="mx-auto mt-3 max-w-xl text-sm text-gray-600">
                Portal berita dan platform press release gratis Indonesia. Jadilah kontributor pertama yang menerbitkan berita atau siaran pers berkualitas!
            </p>
            <div class="mt-6 flex justify-center gap-3">
                <a href="{{ route('dashboard.articles.create') }}" class="rounded-md bg-red-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-800">
                    Post Berita Gratis
                </a>
                <a href="{{ route('seo.press-release') }}" class="rounded-md border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-800 hover:bg-gray-50">
                    Tentang Press Release Gratis
                </a>
            </div>
        </section>
    @endif

    <!-- Latest News Grid -->
    @if($latestArticles->count() > 0)
        <section class="mt-10">
            <div class="flex items-center justify-between border-b border-gray-900 pb-2">
                <h2 class="font-serif text-xl font-bold text-gray-900">Berita &amp; Press Release Terbaru</h2>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($latestArticles as $item)
                    <article class="group flex flex-col border-b border-gray-200 pb-6 sm:border-b-0 sm:pb-0">
                        <a href="{{ route('articles.show', $item->slug) }}" class="block">
                            <div class="aspect-[16/10] w-full overflow-hidden rounded-md border border-gray-200 bg-gray-100">
                                <img
                                    src="{{ $item->image_url }}"
                                    alt="{{ $item->image_alt }}"
                                    loading="lazy"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                                >
                            </div>
                            <div class="mt-3 flex items-center gap-2 text-xs">
                                <span class="font-semibold uppercase tracking-wider text-red-700">{{ $item->category->name }}</span>
                                <span class="text-gray-300">&bull;</span>
                                <span class="text-gray-500">{{ optional($item->published_at)->diffForHumans() }}</span>
                            </div>
                            <h3 class="mt-1.5 font-serif text-lg font-bold leading-snug text-gray-900 group-hover:text-red-700 line-clamp-2">
                                {{ $item->title }}
                            </h3>
                            <p class="mt-2 text-sm leading-relaxed text-gray-600 line-clamp-2">
                                {{ $item->excerpt }}
                            </p>
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $latestArticles->links() }}
            </div>
        </section>
    @endif
</div>
@endsection
