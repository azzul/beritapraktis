<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $monthlyLimit = (int) config('services.beritapraktis.max_articles_per_month', 20);

        $users = User::withCount([
            'articles',
            'articles as monthly_articles_count' => function ($q) {
                $q->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
            },
        ])->latest()->paginate(25);

        return view('admin.users.index', compact('users', 'monthlyLimit'));
    }

    public function toggleBan(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id || $user->isAdmin()) {
            return back()->with('error', 'Tidak dapat memblokir akun Admin.');
        }

        $user->update([
            'is_banned' => !$user->is_banned,
            'ban_reason' => !$user->is_banned ? ($request->input('ban_reason') ?: 'Melanggar pedoman konten BeritaPraktis.') : null,
        ]);

        return back()->with('success', $user->is_banned ? "User {$user->email} diblokir." : "Blokir {$user->email} dicabut.");
    }

    public function toggleRole(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Tidak dapat mengubah role akun Anda sendiri.');
        }

        $user->update([
            'role' => $user->role === 'admin' ? 'user' : 'admin',
        ]);

        return back()->with('success', "Role {$user->email} diubah menjadi {$user->role}.");
    }
}
