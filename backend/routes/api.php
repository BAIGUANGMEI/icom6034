<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\TagController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Public routes (no authentication required)
|
*/

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public post & tag browsing
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);
Route::get('/tags', [TagController::class, 'index']);
Route::get('/tags/{id}', [TagController::class, 'show']);

// Public post search
Route::get('/search/posts', [PostController::class, 'search']);

// Public comments
Route::get('/posts/{postId}/comments', [CommentController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Protected routes (authentication required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Post CRUD
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::get('/my-posts', [PostController::class, 'myPosts']);

    // Comments
    Route::post('/posts/{postId}/comments', [CommentController::class, 'store']);
    Route::delete('/posts/{postId}/comments/{id}', [CommentController::class, 'destroy']);
});
