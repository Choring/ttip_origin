<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Post;
use App\Models\PostBookmark;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'hasPassword' => !is_null($request->user()->password),
            'isKakaoLinked' => !is_null($request->user()->kakao_id),
        ]);
    }

    /**
     * Display the user's activity (posts & bookmarks).
     */
    public function activity(Request $request): Response
    {
        $user = $request->user();
        $tab  = $request->input('tab', 'posts');

        $formatPost = fn($post) => [
            'id'           => $post->id,
            'title'        => $post->title,
            'category'     => $post->category->name ?? '',
            'categorySlug' => $post->category->slug ?? '',
            'likes'        => $post->likes_count ?? 0,
            'comments'     => $post->comments_count ?? 0,
            'views'        => $post->view_count,
            'createdAt'    => $post->created_at->format('Y.m.d'),
            'isHidden'     => $post->is_hidden,
        ];

        $myPosts = $tab === 'posts'
            ? Post::where('user_id', $user->id)
                ->with('category')
                ->withCount(['comments', 'likes'])
                ->latest()
                ->paginate(15)
                ->through($formatPost)
            : null;

        $bookmarkedPosts = $tab === 'bookmarks'
            ? PostBookmark::where('user_id', $user->id)
                ->with(['post' => fn($q) => $q->withCount(['comments', 'likes'])->with('category')])
                ->latest()
                ->paginate(15)
                ->through(fn($b) => $b->post ? $formatPost($b->post) : null)
            : null;

        return Inertia::render('Profile/Activity', [
            'tab'             => $tab,
            'myPosts'         => $myPosts,
            'bookmarkedPosts' => $bookmarkedPosts,
            'stats'           => [
                'postCount'     => Post::where('user_id', $user->id)->count(),
                'bookmarkCount' => PostBookmark::where('user_id', $user->id)->count(),
            ],
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if ($request->user()->password) {
            $request->validate([
                'password' => ['required', 'current_password'],
            ]);
        }

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
