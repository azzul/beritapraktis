@extends('layouts.app')

@section('title', 'Kelola Artikel — Admin Redaksi | BeritaPraktis')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6">
    @include('admin.partials.nav')

    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-900 pb-4">
        <h1 class="font-serif text-2xl font-bold text-gray-900">Daftar Semua Artikel</h1>
        <div class="flex flex-wrap gap-2 text-xs font-semibold">
            @foreach(['pending_review' => 'Pending', 'published' => 'Tayang', 'rejected' => 'Ditolak', 'all' => 'Semua'] as $key => $label)
                <a href="{{ route('admin.articles.index', ['status' => $key]) }}"
                   class="rounded px-3 py-1.5 {{ $status === $key ? 'bg-gray-900 text-white' : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="mt-6 overflow-x-auto rounded-md border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-600">
                <tr>
                    <th class="px-4 py-3">Judul</th>
                    <th class="px-4 py-3">Penulis</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($articles as $article)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $article->title }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $article->user->email }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $article->category->name }}</td>
                        <td class="px-4 py-3 text-xs font-semibold">{{ $article->status }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.articles.show', $article) }}" class="text-xs font-semibold text-red-700 hover:underline">Detail &amp; Moderasi</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="border-t border-gray-100 px-4 py-3">{{ $articles->links() }}</div>
    </div>
</div>
@endsection
