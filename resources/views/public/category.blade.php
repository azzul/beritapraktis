@extends('layouts.app')

@section('title', "Berita {$category->name} Terkini | BeritaPraktis")
@section('meta_description', $category->description ?: "Kumpulan berita {$category->name} terbaru dan terpercaya di BeritaPraktis.")

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6">
    <header class="border-b border-gray-900 pb-4">
        <nav class="text-xs text-gray-500 mb-2">
            <a href="{{ route('home') }}" class="hover:text-red-700">Beranda</a> / <span class="text-gray-900 font-medium">{{ $category->name }}</span>
        </nav>
        <h1 class="font-serif text-3xl font-bold text-gray-900">{{ $category->name }}</h1>
        @if($category->description)
            <p class="mt-1.5 text-sm text-gray-600 max-w-2xl">{{ $category->description }}</p>
        @endif
    </header>

    @if($articles->count() > 0)
        <div class="mt-8 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($articles as $item)
                <article class="group">
                    <a href="{{ route('articles.show', $item->slug) }}" class="block">
                        <div class="aspect-[16/10] w-full overflow-hidden rounded-md border border-gray-200 bg-gray-100">
                            <img src="{{ $item->image_url }}" alt="{{ $item->image_alt }}" loading="lazy" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]">
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                            {{ optional($item->published_at)->translatedFormat('d M Y') }} &bull; {{ $item->reading_time_minutes }} mnt baca
                        </div>
                        <h2 class="mt-1 font-serif text-lg font-bold leading-snug text-gray-900 group-hover:text-red-700 line-clamp-2">
                            {{ $item->title }}
                        </h2>
                        <p class="mt-2 text-sm text-gray-600 line-clamp-2">{{ $item->excerpt }}</p>
                    </a>
                </article>
            @endforeach
        </div>

        <div class="mt-10">{{ $articles->links() }}</div>
    @else
        <div class="mt-8 rounded-md border border-gray-200 bg-white p-8 text-center text-sm text-gray-600">
            Belum ada berita di kategori {{ $category->name }}.
            <a href="{{ route('dashboard.articles.create') }}" class="font-semibold text-red-700 hover:underline">Kirim berita pertama &rarr;</a>
        </div>
    @endif
</div>
@endsection
