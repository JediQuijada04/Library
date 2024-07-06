<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\MuserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\RatingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});

Route::middleware('jwt.verify')->group(function () {


    Route::get('/book/{book}', [BookController::class, 'getBook']);
    Route::get('/book', [BookController::class, 'getBooks']);
    Route::post('/book-order', [BookController::class, 'orderBook']);
    Route::post('/rate/{book_id}', [RatingsController::class, 'addRating']);

    Route::middleware('manager')->group(function () {
        Route::get('/sections/{id}/count-books', [SectionsController::class, 'countBooks']);
        Route::post('/section', [SectionsController::class, 'addSection']);
        Route::post('/data-entry', [MuserController::class, 'addDataEntry']);
        Route::post('/book', [BookController::class, 'addBook']);
        Route::delete('/book/{id}', [BookController::class, 'deleteBook']);
        Route::delete('/delete/{id}', [BookController::class, 'forceDeleteBook']);
        Route::get('/trash', [BookController::class, 'trashedBooks']);
        Route::get('/restore/{id}', [BookController::class, 'restoreBook']);
        Route::put('/book/{id}', [BookController::class, 'editBook']);
    });

    Route::middleware('data-entry')->group(function () {
        Route::get('/sections/{id}/count-books', [SectionsController::class, 'countBooks']);
        Route::post('/section', [SectionsController::class, 'addSection']);
        Route::post('/book', [BookController::class, 'addBook']);
        Route::delete('/book/{id}', [BookController::class, 'deleteBook']);
        Route::delete('/delete/{id}', [BookController::class, 'forceDeleteBook']);
        Route::get('/trash', [BookController::class, 'trashedBooks']);
        Route::get('/restore/{id}', [BookController::class, 'restoreBook']);
        Route::put('/book/{id}', [BookController::class, 'editBook']);
    });
});
