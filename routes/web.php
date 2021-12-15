<?php

use App\Http\Controllers\AppealController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\WebAuthController;
use App\Http\Middleware\RedirectToAppeal;
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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/news', [NewsController::class, 'getList'])->name('news_list');

Route::get('/news/{slug}', [NewsController::class, 'getDetails'])->name('news_item');

Route::get('/appeal', [AppealController::class, 'appealGet'])
    ->name('appeal')
    ->withoutMiddleware(RedirectToAppeal::class);
Route::post('/appeal', [AppealController::class, 'appealPost'])
    ->name('appeal_post')
    ->withoutMiddleware(RedirectToAppeal::class);


Route::match(['GET', 'POST'], '/registration', [WebAuthController::class, 'registration'])->name('registration');
Route::match(['GET', 'POST'], '/login', [WebAuthController::class, 'login'])->name('login');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [WebAuthController::class, 'profile'])->name('profile');
    Route::get('/logout', [WebAuthController::class, 'logout'])->name('logout');
});


