@extends('layouts.app')

@section('title', 'Artikel Saya — Dashboard Kontributor | BeritaPraktis')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-900 pb-4">
        <div>
            <h1 class="font-serif text-2xl font-bold text-gray-900">Artikel &amp; Press Release Saya</h1>
            <p class="mt-1 text-sm text-gray-600">Pantau status moderasi artikel yang telah Anda kirimkan.</p>
        </div>
        <a href="{{ route('dashboard.articles.create') }}" class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800 transition">
            + Tulis Berita Baru
        </a>
    </div>

    <div class="mt-6 overflow-hidden rounded-md border border-gray-200 bg-white">
        @if($articles->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Judul</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Statistik</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($articles as $article)
                            <tr>
                                <td class="px-4 py-3.5">
                                    @if($article->status === 'published')
                                        <a href="{{ route('articles.show', $article->slug) }}" class="font-semibold text-gray-900 hover:text-red-700" target="_blank">
                                            {{ $article->title }}
                                        </a>
                                    @else
                                        <span class="font-semibold text-gray-900">{{ $article->title }}</span>
                                    @endif
                                    @if($article->status === 'rejected' && $article->rejection_reason)
                                        <p class="mt-1 text-xs text-red-700">Alasan ditolak: {{ $article->rejection_reason }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-gray-600">{{ $article->category->name ?? '-' }}</td>
                                <td class="px-4 py-3.5 text-xs text-gray-600">
                                    {{ $article->word_count }} kata &bull; {{ $article->outbound_links_count }} link
                                </td>
                                <td class="px-4 py-3.5">
                                    @if($article->status === 'published')
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">Tayang</span>
                                    @elseif($article->status === 'pending_review')
                                        <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800">Menunggu Approval</span>
                                    @elseif($article->status === 'rejected')
                                        <span class="inline-flex rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-800">Ditolak</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-700">Draft</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-xs text-gray-500">{{ $article->created_at->translatedFormat('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 px-4 py-3">{{ $articles->links() }}</div>
        @else
            <div class="p-10 text-center">
                <p class="text-sm text-gray-600">Anda belum pernah mengirimkan berita atau press release.</p>
                <a href="{{ route('dashboard.articles.create') }}" class="mt-4 inline-block rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                    Mulai Tulis Artikel Pertama
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
