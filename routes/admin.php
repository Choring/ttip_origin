<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
Route::patch('/users/{user}/toggle-ban', [\App\Http\Controllers\Admin\UserController::class, 'toggleBan'])->name('users.toggleBan');

// Categories
Route::get('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/create', [\App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('categories.create');
Route::post('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
Route::get('/categories/{category}/edit', [\App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('categories.edit');
Route::put('/categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

// Posts Management
Route::get('/posts', [\App\Http\Controllers\Admin\PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}/edit', [\App\Http\Controllers\Admin\PostController::class, 'edit'])->name('posts.edit');
Route::put('/posts/{post}', [\App\Http\Controllers\Admin\PostController::class, 'update'])->name('posts.update');
Route::delete('/posts/{post}', [\App\Http\Controllers\Admin\PostController::class, 'destroy'])->name('posts.destroy');

// Tourist Spot Management
Route::get('/tourist-spots', [\App\Http\Controllers\Admin\TouristSpotController::class, 'index'])->name('tourist-spots.index');
Route::get('/tourist-spots/create', [\App\Http\Controllers\Admin\TouristSpotController::class, 'create'])->name('tourist-spots.create');
Route::post('/tourist-spots', [\App\Http\Controllers\Admin\TouristSpotController::class, 'store'])->name('tourist-spots.store');
Route::get('/tourist-spots/{touristSpot}/edit', [\App\Http\Controllers\Admin\TouristSpotController::class, 'edit'])->name('tourist-spots.edit');
Route::put('/tourist-spots/{touristSpot}', [\App\Http\Controllers\Admin\TouristSpotController::class, 'update'])->name('tourist-spots.update');
Route::delete('/tourist-spots/{touristSpot}', [\App\Http\Controllers\Admin\TouristSpotController::class, 'destroy'])->name('tourist-spots.destroy');

// Restaurant Management
Route::get('/restaurants', [\App\Http\Controllers\Admin\RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurants/create', [\App\Http\Controllers\Admin\RestaurantController::class, 'create'])->name('restaurants.create');
Route::post('/restaurants', [\App\Http\Controllers\Admin\RestaurantController::class, 'store'])->name('restaurants.store');
Route::get('/restaurants/{restaurant}/edit', [\App\Http\Controllers\Admin\RestaurantController::class, 'edit'])->name('restaurants.edit');
Route::put('/restaurants/{restaurant}', [\App\Http\Controllers\Admin\RestaurantController::class, 'update'])->name('restaurants.update');
Route::delete('/restaurants/{restaurant}', [\App\Http\Controllers\Admin\RestaurantController::class, 'destroy'])->name('restaurants.destroy');

// Inquiry Management
Route::get('/inquiries', [\App\Http\Controllers\Admin\InquiryController::class, 'index'])->name('inquiries.index');
Route::get('/inquiries/{inquiry}', [\App\Http\Controllers\Admin\InquiryController::class, 'show'])->name('inquiries.show');
Route::post('/inquiries/{inquiry}/answer', [\App\Http\Controllers\Admin\InquiryController::class, 'answer'])->name('inquiries.answer');
Route::delete('/inquiries/{inquiry}', [\App\Http\Controllers\Admin\InquiryController::class, 'destroy'])->name('inquiries.destroy');

// Tier Management
Route::get('/tiers', [\App\Http\Controllers\Admin\TierController::class, 'index'])->name('tiers.index');
Route::post('/tiers', [\App\Http\Controllers\Admin\TierController::class, 'store'])->name('tiers.store');
Route::put('/tiers/{tier}', [\App\Http\Controllers\Admin\TierController::class, 'update'])->name('tiers.update');
Route::delete('/tiers/{tier}', [\App\Http\Controllers\Admin\TierController::class, 'destroy'])->name('tiers.destroy');

// Reports Management
Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
Route::patch('/reports/{report}/resolve', [\App\Http\Controllers\Admin\ReportController::class, 'resolve'])->name('reports.resolve');
Route::patch('/reports/{report}/dismiss', [\App\Http\Controllers\Admin\ReportController::class, 'dismiss'])->name('reports.dismiss');

// Quiz Management
Route::get('/quiz', [\App\Http\Controllers\Admin\QuizController::class, 'index'])->name('quiz.index');
Route::get('/quiz/create', [\App\Http\Controllers\Admin\QuizController::class, 'create'])->name('quiz.create');
Route::post('/quiz', [\App\Http\Controllers\Admin\QuizController::class, 'store'])->name('quiz.store');
Route::get('/quiz/{quiz}/edit', [\App\Http\Controllers\Admin\QuizController::class, 'edit'])->name('quiz.edit');
Route::put('/quiz/{quiz}', [\App\Http\Controllers\Admin\QuizController::class, 'update'])->name('quiz.update');
Route::delete('/quiz/{quiz}', [\App\Http\Controllers\Admin\QuizController::class, 'destroy'])->name('quiz.destroy');
Route::patch('/quiz/{quiz}/toggle-active', [\App\Http\Controllers\Admin\QuizController::class, 'toggleActive'])->name('quiz.toggleActive');

// Banner Management
Route::get('/banners', [\App\Http\Controllers\Admin\BannerController::class, 'index'])->name('banners.index');
Route::get('/banners/create', [\App\Http\Controllers\Admin\BannerController::class, 'create'])->name('banners.create');
Route::post('/banners', [\App\Http\Controllers\Admin\BannerController::class, 'store'])->name('banners.store');
Route::get('/banners/{banner}/edit', [\App\Http\Controllers\Admin\BannerController::class, 'edit'])->name('banners.edit');
Route::put('/banners/{banner}', [\App\Http\Controllers\Admin\BannerController::class, 'update'])->name('banners.update');
Route::delete('/banners/{banner}', [\App\Http\Controllers\Admin\BannerController::class, 'destroy'])->name('banners.destroy');
Route::patch('/banners/{banner}/toggle-active', [\App\Http\Controllers\Admin\BannerController::class, 'toggleActive'])->name('banners.toggleActive');

// Notice Management
Route::get('/notices', [\App\Http\Controllers\Admin\NoticeController::class, 'index'])->name('notices.index');
Route::get('/notices/create', [\App\Http\Controllers\Admin\NoticeController::class, 'create'])->name('notices.create');
Route::post('/notices', [\App\Http\Controllers\Admin\NoticeController::class, 'store'])->name('notices.store');
Route::get('/notices/{post}/edit', [\App\Http\Controllers\Admin\NoticeController::class, 'edit'])->name('notices.edit');
Route::put('/notices/{post}', [\App\Http\Controllers\Admin\NoticeController::class, 'update'])->name('notices.update');
Route::delete('/notices/{post}', [\App\Http\Controllers\Admin\NoticeController::class, 'destroy'])->name('notices.destroy');
Route::patch('/notices/{post}/toggle-pin', [\App\Http\Controllers\Admin\NoticeController::class, 'togglePin'])->name('notices.togglePin');

