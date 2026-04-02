<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('posts')->orderBy('sort_order')->orderBy('id')->get();
        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Categories/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'slug' => 'nullable|string|max:50|unique:categories,slug',
            'description' => 'nullable|string|max:255',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            // Ensure unique auto-slug
            $originalSlug = $validated['slug'];
            $count = 1;
            while (Category::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = "{$originalSlug}-" . $count++;
            }
        }

        Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'description' => $validated['description'],
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.categories.index')->with('success', '새 카테고리가 등록되었습니다.');
    }

    public function edit(Category $category)
    {
        return Inertia::render('Admin/Categories/Edit', [
            'category' => $category
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'slug' => "required|string|max:50|unique:categories,slug,{$category->id}",
            'description' => 'nullable|string|max:255',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'description' => $validated['description'],
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.categories.index')->with('success', '카테고리가 성공적으로 수정되었습니다.');
    }

    public function destroy(Category $category)
    {
        if ($category->posts()->count() > 0) {
            return back()->with('error', '이 카테고리를 사용하는 게시글이 1개 이상 존재하여 삭제할 수 없습니다. 먼저 게시글을 비워주세요.');
        }
        
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', '카테고리가 안전하게 삭제되었습니다.');
    }
}
