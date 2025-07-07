<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsArticle;
use App\Models\NewsCategory;
// Removed: use App\Models\Setting; // Handled by NavigationComposer
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class NewsController extends Controller
{
    /**
     * Display a listing of the news articles.
     */
    public function index(Request $request): View | JsonResponse
    {
        // Removed: $settings = Setting::firstOrCreate(...); // Handled by NavigationComposer

        $query = NewsArticle::with('newsCategory')
                            ->where('is_active', true)
                            ->orderBy('published_at', 'desc');

        // Apply filters if needed (from initial implementation)
        // Example: if ($request->filled('category')) { $query->whereHas(...); }

        $articles = $query->get(); // Get all for simple list. Use paginate() for infinite scroll.

        if ($request->ajax()) {
            // If using paginate() instead of get(), return paginator items and links here
            return response()->json([
                'articles' => $articles->items(), // if using paginate()
                'next_page_url' => $articles->nextPageUrl(), // if using paginate()
                'has_more_pages' => $articles->hasMorePages() // if using paginate()
            ]);
        }

        return view('news.index', [
            'articles' => $articles,
        ]);
    }

    /**
     * Display the specified news article.
     */
    public function show(string $slug): View
    {
        // Removed: $settings = Setting::firstOrCreate(...); // Handled by NavigationComposer

        $article = NewsArticle::with('newsCategory')
                              ->where('slug', $slug)
                              ->where('is_active', true)
                              ->firstOrFail();

        $relatedArticles = NewsArticle::where('news_category_id', $article->news_category_id)
                                      ->where('id', '!=', $article->id)
                                      ->where('is_active', true)
                                      ->orderBy('published_at', 'desc')
                                      ->take(3)
                                      ->get();

        return view('news.show', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }
}