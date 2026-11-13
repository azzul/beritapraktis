@extends('layouts.app')

@section('title', "Hasil Pencarian: {$q} | BeritaPraktis")

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6">
    <header class="border-b border-gray-900 pb-4">
        <h1 class="font-serif text-2xl font-bold text-gray-900">
            @if($q !== '')
                Hasil Pencarian: "{{ $q }}"
            @else
                Cari Berita &amp; Press Release
            @endif
        </h1>
        <form action="{{ route('search') }}" method="GET" class="mt-4 flex max-w-lg gap-2">
            <input type="search" name="q" value="{{ $q }}" placeholder="Ketik kata kunci..." class="flex-1 rounded-md border border-gray-300 bg-white px-3.5 py-2 text-sm focus:border-gray-900 focus:outline-none">
            <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Cari</button>
        </form>
    </header>

    @if($articles->count() > 0)
        <div class="mt-8 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($articles as $item)
                <article class="group">
                    <a href="{{ route('articles.show', $item->slug) }}" class="block">
                        <div class="aspect-[16/10] w-full overflow-hidden rounded-md border border-gray-200 bg-gray-100">
                            <img src="{{ $item->image_url }}" alt="{{ $item->image_alt }}" loading="lazy" class="h-full w-full object-cover">
                        </div>
                        <div class="mt-3 text-xs font-semibold uppercase tracking-wider text-red-700">{{ $item->category->name }}</div>
                        <h2 class="mt-1 font-serif text-lg font-bold text-gray-900 group-hover:text-red-700 line-clamp-2">{{ $item->title }}</h2>
                        <p class="mt-1.5 text-sm text-gray-600 line-clamp-2">{{ $item->excerpt }}</p>
                    </a>
                </article>
            @endforeach
        </div>
        <div class="mt-10">{{ $articles->links() }}</div>
    @else
        <p class="mt-8 text-sm text-gray-600">Tidak ditemukan artikel yang cocok dengan pencarian Anda.</p>
    @endif
</div>
@endsection
