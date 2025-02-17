<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ListingController;
use Illuminate\Support\Facades\Route;

// Routes d'authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});

// Routes pour les annonces (listings)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/listings', [ListingController::class, 'index']);
    Route::get('/listings/{id}', [ListingController::class, 'show']);
    Route::post('/listings', [ListingController::class, 'store']);
    Route::put('/listings/{id}', [ListingController::class, 'update']);
    Route::delete('/listings/{id}', [ListingController::class, 'destroy']);
    Route::delete('/listing-photos/{id}', [ListingController::class, 'deletePhoto']);
});

use App\Http\Controllers\API\FavoriteController;
// Routes pour les favoris
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/favorites/{listingId}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{listingId}', [FavoriteController::class, 'destroy']);
    Route::get('/favorites', [FavoriteController::class, 'index']);
});

use App\Http\Controllers\API\LikeController;
use App\Http\Controllers\API\CommentController;

// Routes pour les likes et les commentaires
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/listings/{listing}/like', [LikeController::class, 'like']);
    Route::post('/listings/{listing}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
});

use App\Http\Controllers\API\ConversationController;
// Routes pour les conversations et messages
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/listings/{listing}/start-conversation', [ConversationController::class, 'startConversation']);
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'sendMessage']);
    Route::get('/conversations', [ConversationController::class, 'getConversations']);
    Route::get('/conversations/{conversation}/messages', [ConversationController::class, 'getMessages']);
    Route::post('/conversations/{conversation}/mark-as-read', [ConversationController::class, 'markAsRead']);
});

// Route pour vérifier l'existence d'une conversation pour une annonce
Route::get('/listings/{listing}/check-conversation', [ListingController::class, 'checkConversation'])->middleware('auth:sanctum');