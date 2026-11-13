<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('articles')->orderBy('sort_order')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:320'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $slug = Str::slug($validated['name']);
        Category::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => true,
            ]
        );

        ModerationController::clearSeoCaches();

        return back()->with('success', 'Kategori berhasil disimpan.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->articles()->exists()) {
            $category->update(['is_active' => !$category->is_active]);

            return back()->with('success', 'Status aktif kategori diperbarui (kategori masih memiliki artikel).');
        }

        $category->delete();
        ModerationController::clearSeoCaches();

        return back()->with('success', 'Kategori dihapus.');
    }
}
