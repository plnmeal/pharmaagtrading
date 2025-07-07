<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Manufacturer;
use App\Models\DosageForm;
use App\Models\TherapeuticCategory;
// Removed: use App\Models\Setting; // Handled by NavigationComposer
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request): View | JsonResponse
    {
        // Removed: $settings = Setting::firstOrCreate(...); // Handled by NavigationComposer

        $query = Product::with(['manufacturer', 'dosageForm', 'therapeuticCategory'])
                        ->where('is_active', true);

        // Apply Filters based on request parameters (these will come from JS AJAX calls)
        if ($request->filled('search')) {
            $search = strtolower($request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"])
                  ->orWhereHas('manufacturer', function ($q_man) use ($search) {
                      $q_man->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                  })
                  ->orWhereHas('dosageForm', function ($q_df) use ($search) {
                      $q_df->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                  })
                  ->orWhereHas('therapeuticCategory', function ($q_tc) use ($search) {
                      $q_tc->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                  });
            });
        }

        if ($request->filled('manufacturer')) {
            $query->whereHas('manufacturer', function ($q) use ($request) {
                $q->where('name', $request->get('manufacturer'));
            });
        }

        if ($request->filled('dosageForm')) {
            $query->whereHas('dosageForm', function ($q) use ($request) {
                $q->where('name', $request->get('dosageForm'));
            });
        }

        if ($request->filled('therapeuticCategory')) {
            $query->whereHas('therapeuticCategory', function ($q) use ($request) {
                $q->where('name', $request->get('therapeuticCategory'));
            });
        }

        if ($request->filled('availability')) {
            $query->where('availability_status', $request->get('availability'));
        }

        $products = $query->orderBy('order')->paginate(12);

        if ($request->ajax()) {
            return response()->json([
                'products' => $products->items(),
                'next_page_url' => $products->nextPageUrl(),
                'has_more_pages' => $products->hasMorePages()
            ]);
        }

        // For the initial page load, return the Blade view
        return view('products.index', [
            // 'settings' is now handled by NavigationComposer
            'products' => $products, // Pass the Paginator object
            'manufacturers' => Manufacturer::where('is_active', true)->orderBy('name')->get(),
            'dosageForms' => DosageForm::where('is_active', true)->orderBy('name')->get(),
            'therapeuticCategories' => TherapeuticCategory::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    /**
     * Display the specified product.
     */
    public function show(string $slug): View
    {
        // Removed: $settings = Setting::firstOrCreate(...); // Handled by NavigationComposer

        $product = Product::with(['manufacturer', 'dosageForm', 'therapeuticCategory'])
                          ->where('slug', $slug)
                          ->where('is_active', true)
                          ->firstOrFail();

        $relatedProducts = Product::where('therapeutic_category_id', $product->therapeutic_category_id)
                                  ->where('id', '!=', $product->id)
                                  ->where('is_active', true)
                                  ->orderBy('name')
                                  ->take(4)
                                  ->get();

        return view('products.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}