<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaveController;


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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/', [App\Http\Controllers\HomeController::class, 'welcome'])->name('welcome');

Route::get('/settings', [App\Http\Controllers\HomeController::class, 'settings'])->name('settings');
Route::get('/upload-video', [App\Http\Controllers\HomeController::class, 'uploadVideo'])->name('upload-video');

Route::post('/save-videos', [SaveController::class, 'saveVideos'])->name('upload_videos');
