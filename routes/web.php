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

// Homepage
Route::get('/', [HomeController::class, 'index']);

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
// These are standard Laravel routes, often including login, register, profile, and dashboard.
// They MUST come before any generic catch-all routes.
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Laravel Breeze's authentication routes (e.g., /login, /register, /forgot-password)
// These are crucial and are loaded from a separate file.
require __DIR__.'/auth.php';

Route::get('/lang/{locale}', function (string $locale) {
    if (! in_array($locale, ['en', 'es'])) { abort(400); }
    App::setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
})->name('locale.switch');

// --- LANGUAGE SWITCHER ROUTE (Must be before the generic {slug} route) ---
// This allows specific URLs like /lang/es to work without being caught by {slug}.
Route::get('/lang/{locale}', function (string $locale) {
    if (! in_array($locale, ['en', 'es'])) {
        abort(400); // Bad Request if locale is not 'en' or 'es'
    }
    App::setLocale($locale); // Set the application locale for the current request
    session()->put('locale', $locale); // Store the locale in session for persistence
    return redirect()->back(); // Redirect back to the page the user was on
})->name('locale.switch');

// --- GENERIC PAGE DISPLAY ROUTE (CATCH-ALL - MUST BE THE ABSOLUTE LAST ROUTE) ---
// This route will only match if no other, more specific route above it has matched the URL.
// It is designed to handle dynamic pages like /about-us, /privacy-policy, /terms-and-conditions.
Route::get('/{slug}', [PageController::class, 'show'])->name('pages.show');
