<?php

namespace App\View\Composers;

use App\Models\Setting;
use App\Models\NavigationItem;
use Illuminate\View\View;
use Illuminate\Support\Collection; // Import Collection for groupBy result

class NavigationComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // Fetch global settings
        $settings = Setting::firstOrCreate(
            ['id' => 1],
            ['site_name' => 'PharmaAGTrading']
        );

        // Fetch all active navigation items, eager load relationships needed for URL generation
        // and group them by location for easy access in Blade.
        $navigationItemsGrouped = NavigationItem::with(['page', 'newsCategory', 'therapeuticCategory', 'dosageForm'])
                                         ->where('is_active', true)
                                         ->orderBy('order')
                                         ->get()
                                         ->groupBy('location');

        // Pass data to the view. These variables will now be available in layouts.app and its child views.
        $view->with('settings', $settings);
        $view->with('navigationItems', $navigationItemsGrouped); // The grouped collection
        $view->with('headerNav', $navigationItemsGrouped['header'] ?? collect()); // Direct access for header
        $view->with('footerNavigateNav', $navigationItemsGrouped['footer_navigate'] ?? collect()); // Direct access for footer nav
        $view->with('footerLegalNav', $navigationItemsGrouped['footer_legal'] ?? collect()); // Direct access for footer legal
    }
}