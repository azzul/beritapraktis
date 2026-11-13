@extends('layouts.app')

@section('title', 'Panel Redaksi Admin | BeritaPraktis')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6">
    @include('admin.partials.nav')

    <div class="border-b border-gray-900 pb-4">
        <span class="text-xs font-bold uppercase tracking-wider text-red-700">Admin Redaksi</span>
        <h1 class="font-serif text-2xl font-bold text-gray-900">Dashboard Moderasi Berita</h1>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-md border border-amber-200 bg-amber-50 p-4">
            <div class="text-xs font-semibold text-amber-800">Menunggu Approval</div>
            <div class="mt-1 font-serif text-3xl font-bold text-amber-900">{{ $stats['pending'] }}</div>
        </div>
        <div class="rounded-md border border-emerald-200 bg-emerald-50 p-4">
            <div class="text-xs font-semibold text-emerald-800">Sudah Tayang</div>
            <div class="mt-1 font-serif text-3xl font-bold text-emerald-900">{{ $stats['published'] }}</div>
        </div>
        <div class="rounded-md border border-red-200 bg-red-50 p-4">
            <div class="text-xs font-semibold text-red-800">Ditolak</div>
            <div class="mt-1 font-serif text-3xl font-bold text-red-900">{{ $stats['rejected'] }}</div>
        </div>
        <div class="rounded-md border border-gray-200 bg-white p-4">
            <div class="text-xs font-semibold text-gray-600">Total Kontributor</div>
            <div class="mt-1 font-serif text-3xl font-bold text-gray-900">{{ $stats['users'] }}</div>
        </div>
    </div>

    <div class="mt-8 rounded-md border border-gray-200 bg-white">
        <div class="border-b border-gray-200 px-5 py-3.5">
            <h2 class="font-serif text-lg font-bold text-gray-900">Antrean Menunggu Persetujuan</h2>
        </div>

        @if($pendingArticles->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Judul &amp; Penulis</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Kata / Link</th>
                            <th class="px-4 py-3">Dikirim</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pendingArticles as $article)
                            <tr>
                                <td class="px-4 py-3.5">
                                    <div class="font-semibold text-gray-900">{{ $article->title }}</div>
                                    <div class="text-xs text-gray-500">{{ $article->user->name }} ({{ $article->user->email }})</div>
                                </td>
                                <td class="px-4 py-3.5 text-gray-700">{{ $article->category->name }}</td>
                                <td class="px-4 py-3.5 text-xs text-gray-600">{{ $article->word_count }} kata &bull; {{ $article->outbound_links_count }} link</td>
                                <td class="px-4 py-3.5 text-xs text-gray-500">{{ $article->created_at->diffForHumans() }}</td>
                                <td class="px-4 py-3.5 text-right">
                                    <a href="{{ route('admin.articles.show', $article) }}" class="rounded bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700">
                                        Review
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-100 px-4 py-3">{{ $pendingArticles->links() }}</div>
        @else
            <p class="p-8 text-center text-sm text-gray-500">Tidak ada artikel yang menunggu persetujuan.</p>
        @endif
    </div>
</div>
@endsection
