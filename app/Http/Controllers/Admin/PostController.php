<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'category'])->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhereHas('user', function ($uq) use ($request) {
                      $uq->where('name', 'like', "%{$request->search}%");
                  });
            });
        }

        $posts = $query->paginate(15)->appends($request->query());

        return Inertia::render('Admin/Post/Index', [
            'posts' => $posts,
            'filters' => $request->only('search'),
        ]);
    }

    public function edit(Post $post)
    {
        return Inertia::render('Admin/Post/Edit', [
            'post' => $post->load(['user', 'category']),
            'categories' => Category::orderBy('sort_order')->get()
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'content'     => 'required|string',
            'tags'        => 'nullable|array|max:3',
            'tags.*'      => 'string|max:15',
            'type'        => 'required|in:general,notice,pinned',
            'is_pinned'   => 'boolean',
        ]);

        // pinned 타입이면 is_pinned 강제 true
        if ($validated['type'] === 'pinned') {
            $validated['is_pinned'] = true;
        }

        $post->update($validated);

        return redirect()->route('admin.posts.index')->with('success', '해당 게시글이 관리자에 의해 수정되었습니다.');
    }

    public function destroy(Post $post)
    {
        // Delete cascading associations conceptually, real physical delete handles via DB standard rules or observers
        $post->delete();
        
        return redirect()->route('admin.posts.index')->with('success', '해당 게시글이 강제로 삭제(블라인드) 처리되었습니다.');
    }
}
