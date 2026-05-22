<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Post $post): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     * 공지사항(notice)은 일반 Post 수정 라우트로 변경 불가 — 어드민 전용
     */
    public function update(User $user, Post $post): bool
    {
        if ($post->type === 'notice') return false;
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     * 공지사항(notice)은 일반 Post 삭제 라우트로 삭제 불가 — 어드민 전용
     */
    public function delete(User $user, Post $post): bool
    {
        if ($post->type === 'notice') return false;
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Post $post): bool
    {
        if ($post->type === 'notice') return false;
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        if ($post->type === 'notice') return false;
        return $user->id === $post->user_id;
    }
}
