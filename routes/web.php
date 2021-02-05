<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('/', [App\Http\Controllers\PhotoController::class, 'home']);
Route::get('/home', [App\Http\Controllers\PhotoController::class, 'home']);

Route::get('/api/keywords', [App\Http\Controllers\Api\KeywordController::class, 'getAll']);
Route::post('/api/keywords/photo/{id}', [App\Http\Controllers\Api\KeywordController::class, 'addPhotoKeyword']);
Route::delete('/api/keywords/{keywordId}/photo/{photoId}', [App\Http\Controllers\Api\KeywordController::class, 'removePhotoKeyword']);

Route::post('/api/photos/upload', [App\Http\Controllers\Api\PhotoController::class, 'upload']);
Route::get('/api/photos/keyword/{id}', [App\Http\Controllers\Api\PhotoController::class, 'showForKeyword']);
Route::get('/api/photos/search', [App\Http\Controllers\Api\PhotoController::class, 'search']);
Route::get('/api/photos', [App\Http\Controllers\Api\PhotoController::class,'index']);
Route::get('/api/photos/{id}', [App\Http\Controllers\Api\PhotoController::class,'show']);
Route::get('/api/photos/{id}/keywords', [App\Http\Controllers\Api\PhotoController::class,'getKeywordsForPhoto']);
Route::post('/api/photos/{id}/description', [App\Http\Controllers\Api\PhotoController::class, 'updateDescription']);
Route::post('/api/photos/{id}/title', [App\Http\Controllers\Api\PhotoController::class, 'updateTitle']);
Route::get('/api/photos/{id}/next', [App\Http\Controllers\Api\PhotoController::class, 'getNextPhoto']);
Route::get('/api/photos/{id}/prev', [App\Http\Controllers\Api\PhotoController::class,'getPreviousPhoto']);
Route::delete('/api/photos/{id}', [App\Http\Controllers\Api\PhotoController::class, 'delete']);

Route::view('/{any}', 'photos.photo-manager')->where('any', '.*');

