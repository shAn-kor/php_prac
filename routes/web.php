<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'index'])->name('posts.index');
Route::resource('posts', PostController::class);