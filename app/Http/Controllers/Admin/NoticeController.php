<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoticeController extends Controller
{
    /**
     * Display a listing of notices for admin.
     */
    public function index(Request $request)
    {
        $query = Post::with(['user', 'category'])
            ->notice()
            ->latest();

        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $notices = $query->paginate(15)->appends($request->query());

        return Inertia::render('Admin/Notice/Index', [
            'notices' => $notices,
            'filters' => $request->only('search'),
        ]);
    }

    /**
     * Show the form for creating a new notice.
     */
    public function create()
    {
        $categories = \App\Models\Category::where('is_active', true)->orderBy('sort_order')->get();
        return Inertia::render('Admin/Notice/Create', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created notice in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'nullable|array',
            'is_pinned' => 'boolean',
            'image' => 'nullable|image|max:5120',
        ]);

        $post = new Post();
        $post->user_id = auth()->id();
        $post->category_id = $validated['category_id'];
        $post->title = $validated['title'];
        $post->content = $validated['content'];
        $post->tags = $validated['tags'] ?? [];
        $post->type = 'notice'; // 강제 공지사항 타입
        $post->is_pinned = $validated['is_pinned'] ?? false;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $post->card_image_path = $path;
        }

        $post->save();

        return redirect()->route('admin.notices.index')
            ->with('success', '공지사항이 성공적으로 등록되었습니다.');
    }

    /**
     * Toggle the pinned status of a notice.
     */
    public function togglePin(Post $post)

    {
        if ($post->type !== 'notice') {
            return back()->with('error', '공지사항이 아닌 게시글은 고정 설정을 할 수 없습니다.');
        }

        $post->update([
            'is_pinned' => !$post->is_pinned
        ]);

        $status = $post->is_pinned ? '고정' : '고정 해제';
        return back()->with('success', "공지사항이 성공적으로 {$status} 처리되었습니다.");
    }
}
