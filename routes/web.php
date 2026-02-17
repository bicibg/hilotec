<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReferenceController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/angebot', [ServiceController::class, 'index'])->name('services.index');
Route::get('/angebot/{slug}', [ServiceController::class, 'show'])->name('services.show');

Route::get('/referenzen', [ReferenceController::class, 'index'])->name('references.index');

Route::get('/ueber-uns', [AboutController::class, 'index'])->name('about');

Route::get('/aktuelles', [PostController::class, 'index'])->name('posts.index');
Route::get('/aktuelles/{slug}', [PostController::class, 'show'])->name('posts.show');

Route::get('/kontakt', [ContactController::class, 'index'])->name('contact');
Route::post('/kontakt', [ContactController::class, 'send'])->name('contact.send');

// Catch-all for generic pages (Impressum, Datenschutz) — must be last
Route::get('/{slug}', [PageController::class, 'show'])->name('pages.show');
