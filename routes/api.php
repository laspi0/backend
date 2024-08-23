<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ListingController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/listings', [ListingController::class, 'index']);
    Route::get('/listings/{id}', [ListingController::class, 'show']);
    Route::post('/listings', [ListingController::class, 'store']);
    Route::put('/listings/{id}', [ListingController::class, 'update']);
    Route::delete('/listings/{id}', [ListingController::class, 'destroy']);
    Route::delete('/listing-photos/{id}', [ListingController::class, 'deletePhoto']);
});


use App\Http\Controllers\API\FavoriteController;
// Routes API pour les favoris
Route::middleware('auth:sanctum')->group(function () {
    // Ajouter un favori
    Route::post('/favorites/{listingId}', [FavoriteController::class, 'store']);
    
    // Supprimer un favori
    Route::delete('/favorites/{listingId}', [FavoriteController::class, 'destroy']);
    
    // Lister les favoris
    Route::get('/favorites', [FavoriteController::class, 'index']);
});

use App\Http\Controllers\API\LikeController;
use App\Http\Controllers\API\CommentController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/listings/{listing}/like', [LikeController::class, 'like']);
    Route::post('/listings/{listing}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
});
