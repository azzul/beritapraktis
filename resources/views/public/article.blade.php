@extends('layouts.app')

@section('title', "{$article->title} | BeritaPraktis")
@section('meta_description', $article->excerpt)
@section('og_type', 'article')
@section('og_image', $article->image_url)

@section('content')
<article class="mx-auto max-w-4xl px-4 py-8 sm:px-6">
    <!-- Breadcrumb -->
    <nav class="text-xs text-gray-500 mb-4" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-red-700">Beranda</a>
        <span class="mx-1.5">/</span>
        <a href="{{ route('categories.show', $article->category->slug) }}" class="hover:text-red-700">{{ $article->category->name }}</a>
        <span class="mx-1.5">/</span>
        <span class="text-gray-800 line-clamp-1 inline">{{ $article->title }}</span>
    </nav>

    <!-- Article Header -->
    <header>
        <a href="{{ route('categories.show', $article->category->slug) }}" class="inline-block text-xs font-bold uppercase tracking-wider text-red-700 hover:underline">
            {{ $article->category->name }}
        </a>
        <h1 class="mt-2 font-serif text-3xl font-bold leading-tight text-gray-900 sm:text-4xl">
            {{ $article->title }}
        </h1>
        <p class="mt-3 text-base leading-relaxed text-gray-600">
            {{ $article->excerpt }}
        </p>

        <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-y border-gray-200 py-3 text-xs text-gray-600">
            <div class="flex items-center gap-2.5">
                @if($article->user?->avatar)
                    <img src="{{ $article->user->avatar }}" alt="{{ $article->user->name }}" class="h-7 w-7 rounded-full object-cover">
                @endif
                <span>Oleh <strong class="text-gray-900">{{ $article->user->name ?? 'Redaksi' }}</strong></span>
                <span class="text-gray-300">&bull;</span>
                <time datetime="{{ optional($article->published_at)->toIso8601String() }}">
                    {{ optional($article->published_at)->translatedFormat('d F Y, H:i') }} WIB
                </time>
            </div>
            <div>
                {{ $article->word_count }} kata &bull; {{ $article->reading_time_minutes }} menit baca
            </div>
        </div>
    </header>

    <!-- Featured Image -->
    <figure class="mt-6">
        <div class="overflow-hidden rounded-md border border-gray-200 bg-gray-100">
            <img
                src="{{ $article->image_url }}"
                alt="{{ $article->image_alt }}"
                fetchpriority="high"
                class="w-full max-h-[520px] object-cover"
            >
        </div>
        @if($article->image_caption)
            <figcaption class="mt-2 text-xs text-gray-500">{{ $article->image_caption }}</figcaption>
        @endif
    </figure>

    <!-- Editorial Body -->
    <div class="prose-editorial mx-auto mt-8 max-w-[72ch]">
        {!! $article->content !!}
    </div>

    <!-- Related Articles -->
    @if($relatedArticles->count() > 0)
        <section class="mt-14 border-t border-gray-900 pt-6">
            <h2 class="font-serif text-xl font-bold text-gray-900">Berita Terkait di {{ $article->category->name }}</h2>
            <div class="mt-5 grid grid-cols-1 gap-6 sm:grid-cols-2">
                @foreach($relatedArticles as $rel)
                    <a href="{{ route('articles.show', $rel->slug) }}" class="group flex gap-3.5 items-start">
                        <img src="{{ $rel->image_url }}" alt="{{ $rel->image_alt }}" loading="lazy" class="h-20 w-28 shrink-0 rounded border border-gray-200 object-cover">
                        <div>
                            <h3 class="font-serif text-sm font-bold leading-snug text-gray-900 group-hover:text-red-700 line-clamp-2">{{ $rel->title }}</h3>
                            <span class="mt-1 block text-xs text-gray-500">{{ optional($rel->published_at)->translatedFormat('d M Y') }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</article>
@endsection
