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

// Notice Management
Route::get('/notices', [\App\Http\Controllers\Admin\NoticeController::class, 'index'])->name('notices.index');
Route::get('/notices/create', [\App\Http\Controllers\Admin\NoticeController::class, 'create'])->name('notices.create');
Route::post('/notices', [\App\Http\Controllers\Admin\NoticeController::class, 'store'])->name('notices.store');
Route::patch('/notices/{post}/toggle-pin', [\App\Http\Controllers\Admin\NoticeController::class, 'togglePin'])->name('notices.togglePin');

