<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Removed: use App\Models\Setting; // Composer handles this
use App\Models\Manufacturer;
use App\Models\DosageForm;
use App\Models\Service;
use App\Models\NewsArticle;
// Removed: use App\Models\NavigationItem; // Composer handles this
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the homepage.
     */
    public function index(): View
    {
        // REMOVED: $settings = Setting::firstOrCreate(...); // Now handled by NavigationComposer
        // REMOVED: All $navigationItems, $headerNav, $footerNavigateNav, $footerLegalNav fetches // Now handled by NavigationComposer

        // Fetch dynamic data for homepage sections (these are still specific to HomeController):
        $manufacturers = Manufacturer::where('is_active', true)
                                     ->where('is_featured', true)
                                     ->orderBy('order')
                                     ->get();

        $dosageForms = DosageForm::where('is_active', true)
                                 ->where('is_featured', true)
                                 ->orderBy('order')
                                 ->get();

        $services = Service::where('is_active', true)
                           ->orderBy('order')
                           ->get();

        $newsArticles = NewsArticle::with('newsCategory')
                                   ->where('is_active', true)
                                   ->where('is_featured', true)
                                   ->orderBy('published_at', 'desc')
                                   ->take(3)
                                   ->get();

        // Pass only content-specific data. $settings and navigation vars come from Composer.
        return view('welcome', compact(
            'manufacturers',
            'dosageForms',
            'services',
            'newsArticles'
        ));
    }
}