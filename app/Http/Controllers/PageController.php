<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
// Removed: use App\Models\Setting; // Handled by NavigationComposer
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display the specified content page.
     */
    public function show(string $slug): View
    {
        // Removed: $settings = Setting::firstOrCreate(...); // Handled by NavigationComposer

        $page = Page::where('slug', $slug)
                    ->where('is_active', true)
                    ->firstOrFail();

        return view('pages.show', [
            'page' => $page,
        ]);
    }
}