@extends('layouts.app')

@section('title', 'Kelola Kategori — Admin | BeritaPraktis')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
    @include('admin.partials.nav')

    <div class="flex items-center justify-between border-b border-gray-900 pb-4">
        <h1 class="font-serif text-2xl font-bold text-gray-900">Kelola Kategori Berita</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold text-gray-600 hover:text-red-700">&larr; Dashboard</a>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST" class="mt-6 rounded-md border border-gray-200 bg-white p-5 grid grid-cols-1 gap-4 sm:grid-cols-4 items-end">
        @csrf
        <div>
            <label class="block text-xs font-semibold text-gray-700">Nama Kategori</label>
            <input type="text" name="name" required class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-gray-700">Deskripsi SEO</label>
            <input type="text" name="description" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm">
        </div>
        <button type="submit" class="rounded bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 cursor-pointer">Simpan Kategori</button>
    </form>

    <div class="mt-6 overflow-hidden rounded-md border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-600">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Slug</th>
                    <th class="px-4 py-3">Artikel</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($categories as $cat)
                    <tr>
                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $cat->name }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $cat->slug }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $cat->articles_count }}</td>
                        <td class="px-4 py-3 text-xs">{{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                        <td class="px-4 py-3 text-right">
                            <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-red-700 hover:underline cursor-pointer">Toggle / Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
