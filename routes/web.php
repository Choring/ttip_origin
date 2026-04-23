<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SEOController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ImageController;

Route::middleware('throttle:global')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/sitemap.xml', [SEOController::class, 'sitemap']);
    Route::get('/popular', [HomeController::class, 'popular'])->name('popular');
    Route::get('/bookmarks', [HomeController::class, 'bookmarks'])->name('bookmarks');
    Route::get('/notices', [\App\Http\Controllers\NoticeController::class, 'index'])->name('notices.index');
    Route::inertia('/terms', 'Legal/Terms')->name('terms');
    Route::inertia('/privacy', 'Legal/Privacy')->name('privacy');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show')->whereNumber('post');
});

// 에디터 이미지 업로드
Route::post('/api/upload-image', [ImageController::class, 'upload'])->middleware(['auth', 'verified'])->name('image.upload');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 로그인만 필요한 라우트 (이메일 인증 불필요)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 이메일 인증 필요한 라우트
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::post('/posts', [PostController::class, 'store'])->middleware('throttle:write')->name('posts.store');
    Route::put('/posts/{post}', [PostController::class, 'update'])->middleware('throttle:write')->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::post('/posts/{post}/comments', [\App\Http\Controllers\CommentController::class, 'store'])->middleware('throttle:write')->name('comments.store');
    Route::put('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'update'])->middleware('throttle:write')->name('comments.update');
    Route::delete('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy');

    Route::post('/posts/{post}/like', [PostController::class, 'toggleLike'])->name('posts.like');
    Route::post('/posts/{post}/bookmark', [PostController::class, 'toggleBookmark'])->name('posts.bookmark');
    Route::post('/comments/{comment}/like', [\App\Http\Controllers\CommentController::class, 'toggleLike'])->name('comments.like');
});


require __DIR__.'/auth.php';
