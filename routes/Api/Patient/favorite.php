<?php

use App\Http\Controllers\Api\Patient\FavoriteController;
use Illuminate\Support\Facades\Route;


Route::prefix('patient')->middleware('auth:patient')->group(function () {

  
    Route::post('add-favorite', [FavoriteController::class, 'addFavorite'])->name('favorites.add');
    Route::post('remove-favorite', [FavoriteController::class, 'removeFavorite'])->name('favorites.remove');
    Route::get('my-favorites', [FavoriteController::class, 'myFavorites'])->name('favorites.index');
});