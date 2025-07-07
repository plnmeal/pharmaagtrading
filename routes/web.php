<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PageController; 
use App\Http\Controllers\ContactController; 

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

Route::get('/', [HomeController::class, 'index']);

// News Listing Page
Route::get('/news', [NewsController::class, 'index'])->name('news.index');

// News Detail Page (using slug for Route Model Binding)
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

// Product Listing Page
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// Product Detail Page (using slug for Route Model Binding)
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Contact Us Page
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';

// IMPORTANT: ADD THIS ROUTE AT THE VERY END OF YOUR routes/web.php FILE
// Generic Page Display (e.g., /about-us, /privacy-policy, /terms-and-conditions)
// It must be last so it doesn't intercept other specific routes like /products or /news
Route::get('/{slug}', [PageController::class, 'show'])->name('pages.show'); // ADD THIS LINE