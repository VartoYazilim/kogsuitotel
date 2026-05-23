<?php

use App\Http\Controllers\GalleryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Site Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/hakkimizda', [PageController::class, 'about'])->name('about');
Route::get('/iletisim', [PageController::class, 'contact'])->name('contact');
Route::get('/sss', [PageController::class, 'faq'])->name('faq');
Route::get('/kvkk', [PageController::class, 'kvkk'])->name('kvkk');
Route::get('/gizlilik', [PageController::class, 'privacy'])->name('privacy');

Route::get('/odalar', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/odalar/{room:slug}', [RoomController::class, 'show'])->name('rooms.show');

Route::get('/galeri', [GalleryController::class, 'index'])->name('gallery.index');

Route::get('/rezervasyon', [ReservationController::class, 'create'])->name('reservations.create');
Route::post('/rezervasyon', [ReservationController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('reservations.store');
Route::get('/rezervasyon/basarili/{code}', [ReservationController::class, 'success'])
    ->middleware('throttle:20,1') // IDOR enumeration koruması (kod tahmin saldırısı yavaşlar)
    ->name('reservations.success');

/*
|--------------------------------------------------------------------------
| API Routes (public, JSON)
|--------------------------------------------------------------------------
*/

Route::get('/api/rooms/{room:slug}/unavailable-dates', [RoomController::class, 'unavailableDates'])
    ->name('rooms.unavailable-dates');

/*
|--------------------------------------------------------------------------
| SEO Routes
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
