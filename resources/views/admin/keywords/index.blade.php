@extends('layouts.app')

@section('title', 'Kata Kunci Terlarang — Admin | BeritaPraktis')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
    @include('admin.partials.nav')

    <div class="flex items-center justify-between border-b border-gray-900 pb-4">
        <h1 class="font-serif text-2xl font-bold text-gray-900">Daftar Kata Kunci Terlarang (Judol / Game Online / Pornografi)</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold text-gray-600 hover:text-red-700">&larr; Dashboard</a>
    </div>

    <form action="{{ route('admin.keywords.store') }}" method="POST" class="mt-6 rounded-md border border-gray-200 bg-white p-5 grid grid-cols-1 gap-4 sm:grid-cols-4 items-end">
        @csrf
        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-gray-700">Kata Kunci / Frasa</label>
            <input type="text" name="keyword" required placeholder="Contoh: slot gacor" class="mt-1 w-full rounded border border-gray-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700">Kategori Pelanggaran</label>
            <select name="category" class="mt-1 w-full rounded border border-gray-300 bg-white px-3 py-2 text-sm">
                <option value="gambling">Judi Online</option>
                <option value="online_game">Game Online</option>
                <option value="pornography">Pornografi</option>
                <option value="other">Lainnya</option>
            </select>
        </div>
        <button type="submit" class="rounded bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800 cursor-pointer">+ Tambah</button>
    </form>

    <div class="mt-6 overflow-hidden rounded-md border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-600">
                <tr>
                    <th class="px-4 py-3">Kata Kunci</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($keywords as $kw)
                    <tr>
                        <td class="px-4 py-2.5 font-medium text-gray-900">{{ $kw->keyword }}</td>
                        <td class="px-4 py-2.5 text-xs text-gray-600">{{ $kw->category }}</td>
                        <td class="px-4 py-2.5 text-right">
                            <form action="{{ route('admin.keywords.destroy', $kw) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-red-700 hover:underline cursor-pointer">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="border-t border-gray-100 px-4 py-3">{{ $keywords->links() }}</div>
    </div>
</div>
@endsection
