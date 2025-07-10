<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App; // Import App facade

// --- IMPORTANT: Ensure all your custom controllers are imported ---
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
// --- END CUSTOM CONTROLLERS ---


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// --- PRIMARY APPLICATION ROUTES (Order matters for specific paths) ---

// Homepage - ADDED ->name('home') HERE
Route::get('/', [HomeController::class, 'index'])->name('home');

// News Pages (Listing and Detail)
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

// Product Pages (Listing and Detail)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Contact Us Pages (Display and Form Submission)
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// --- AUTHENTICATION & DASHBOARD ROUTES (Laravel Breeze / Filament / Core) ---
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// --- LANGUAGE SWITCHER ROUTE (Moved and consolidated, was duplicated) ---
Route::get('/lang/{locale}', function (string $locale) {
    if (! in_array($locale, ['en', 'es'])) { abort(400); }
    App::setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
})->name('locale.switch'); // Ensure this is only defined once

// --- GENERIC PAGE DISPLAY ROUTE (CATCH-ALL - MUST BE THE ABSOLUTE LAST ROUTE) ---
Route::get('/{slug}', [PageController::class, 'show'])->name('pages.show');