<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'pending' => Article::where('status', 'pending_review')->count(),
            'published' => Article::where('status', 'published')->count(),
            'rejected' => Article::where('status', 'rejected')->count(),
            'users' => User::where('role', 'user')->count(),
        ];

        $pendingArticles = Article::with(['user', 'category'])
            ->where('status', 'pending_review')
            ->latest()
            ->paginate(15);

        return view('admin.dashboard', compact('stats', 'pendingArticles'));
    }
}
