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
