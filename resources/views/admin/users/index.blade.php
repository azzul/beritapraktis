@extends('layouts.app')

@section('title', 'Manage User & Kuota Bulanan — Admin | BeritaPraktis')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-900 pb-4">
        <div>
            <h1 class="font-serif text-2xl font-bold text-gray-900">Manage User &amp; Kuota Penulisan</h1>
            <p class="mt-1 text-xs text-gray-600">Batas penulisan kontributor saat ini: <strong>{{ $monthlyLimit }} artikel / bulan</strong> (diatur via <code>MAX_ARTICLES_PER_MONTH</code> di <code>.env</code>).</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold text-gray-600 hover:text-red-700">&larr; Dashboard Admin</a>
    </div>

    <div class="mt-6 overflow-x-auto rounded-md border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-600">
                <tr>
                    <th class="px-4 py-3">Nama &amp; Email</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Kuota Bulan Ini</th>
                    <th class="px-4 py-3">Total Artikel</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $u)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-gray-900">{{ $u->name }}</div>
                            <div class="text-xs text-gray-500">{{ $u->email }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded px-2 py-0.5 text-xs font-semibold {{ $u->isAdmin() ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700' }}">
                                {{ strtoupper($u->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs font-semibold {{ !$u->isAdmin() && $u->monthly_articles_count >= $monthlyLimit ? 'text-red-700' : 'text-gray-700' }}">
                            {{ $u->isAdmin() ? 'Unlimited' : "{$u->monthly_articles_count} / {$monthlyLimit}" }}
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $u->articles_count }}</td>
                        <td class="px-4 py-3 text-xs">
                            @if($u->is_banned)
                                <span class="rounded-full bg-red-100 px-2.5 py-0.5 font-semibold text-red-800">Diblokir</span>
                            @else
                                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 font-semibold text-emerald-800">Aktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            @if($u->id !== auth()->id())
                                <form action="{{ route('admin.users.role', $u) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs font-semibold text-gray-700 hover:underline cursor-pointer">
                                        {{ $u->isAdmin() ? 'Jadikan User' : 'Jadikan Admin' }}
                                    </button>
                                </form>
                                @if(!$u->isAdmin())
                                    <form action="{{ route('admin.users.ban', $u) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold {{ $u->is_banned ? 'text-emerald-700' : 'text-red-700' }} hover:underline cursor-pointer">
                                            {{ $u->is_banned ? 'Cabut Blokir' : 'Blokir' }}
                                        </button>
                                    </form>
                                @endif
                            @else
                                <span class="text-xs text-gray-400">Akun Anda</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="border-t border-gray-100 px-4 py-3">{{ $users->links() }}</div>
    </div>
</div>
@endsection
