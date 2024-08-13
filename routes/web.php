<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReviewController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/book/{id}', [HomeController::class, 'details'])->name('book.details');
Route::post('/save-book-review', [HomeController::class, 'saveReview'])->name('book.saveReview');


Route::group(['prefix' => 'account'], function(){
    Route::group(['middleware' => 'guest'], function(){
        Route::get('register', [AccountController::class, 'register'])->name('account.register');
        Route::post('register', [AccountController::class, 'processRegister'])->name('account.processRegister');
        Route::get('login', [AccountController::class, 'login'])->name('account.login');
        Route::post('login', [AccountController::class, 'authenticate'])->name('account.authenticate');
    });
    Route::group(['middleware' => 'auth'], function(){
        Route::get('profile', [AccountController::class, 'profile'])->name('account.profile');
        Route::get('logout', [AccountController::class, 'logout'])->name('account.logout');
        Route::post('update_profile', [AccountController::class, 'updateProfile'])->name('account.update_profile');

        Route::group(['middleware' => 'check-admin'], function(){
            Route::get('books', [BookController::class, 'index'])->name('books.index');
            Route::get('books/create', [BookController::class, 'create'])->name('books.create');
            Route::post('books', [BookController::class, 'store'])->name('books.store');
            Route::get('books/edit/{id}', [BookController::class, 'edit'])->name('books.edit');
            Route::post('books/update/{id}', [BookController::class, 'update'])->name('books.update');
            Route::delete('books', [BookController::class, 'destroy'])->name('books.destroy');

            Route::get('reviews', [ReviewController::class, 'index'])->name('account.reviews');
            Route::get('reviews/{id}', [ReviewController::class, 'edit'])->name('account.reviews.edit');
            Route::post('reviews/{id}', [ReviewController::class, 'updateReview'])->name('account.reviews.updateReview');
            Route::post('delete-review', [ReviewController::class, 'deleteReview'])->name('account.reviews.deleteReview');
        });
        
        Route::get('my-reviews', [AccountController::class, 'myReviews'])->name('account.my-reviews');
        Route::get('my-reviews/{id}', [AccountController::class, 'editReview'])->name('account.my-reviews.editReview');
        Route::post('my-reviews/{id}', [AccountController::class, 'updateReview'])->name('account.my-reviews.updateReview');
        Route::post('delete-my-review', [ReviewController::class, 'deleteReview'])->name('account.my-reviews.deleteReview');
        Route::get('show-password', [AccountController::class, 'showPassword'])->name('account.showPassword');
        Route::post('show-password', [AccountController::class, 'updatePassword'])->name('account.updatePassword');
    });
});