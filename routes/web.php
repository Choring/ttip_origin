<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SEOController;
use App\Http\Controllers\NoticeController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [SEOController::class, 'sitemap']);
Route::get('/popular', [HomeController::class, 'popular'])->name('popular');
Route::get('/bookmarks', [HomeController::class, 'bookmarks'])->name('bookmarks');
Route::get('/notices', [\App\Http\Controllers\NoticeController::class, 'index'])->name('notices.index');
Route::inertia('/terms', 'Legal/Terms')->name('terms');
Route::inertia('/privacy', 'Legal/Privacy')->name('privacy');

// {post} 라우트가 'create' 문자열을 삼켜서 404 에러가 나지 않도록 숫자(ID)만 받게 제한합니다.
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show')->whereNumber('post');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::post('/posts/{post}/comments', [\App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy');

    Route::post('/posts/{post}/like', [PostController::class, 'toggleLike'])->name('posts.like');
});

require __DIR__.'/auth.php';
