{{-- resources/views/news/index.blade.php --}}
@extends('layouts.app')

@section('title', __('messages.news_insights_page_title'))
@section('meta_description', __('messages.stay_informed_news_section')) {{-- Reusing desc from homepage for consistency --}}

@section('content')
    <section class="page-header">
        <h1>{{ __('messages.news_insights_page_title') }}</h1>
        <p>{{ __('messages.stay_informed_news_section') }}</p>
    </section>

    <section class="news-listing-section">
        <div class="container">
            <div class="content-panel">
                <div id="news-grid" class="news-grid">
                    {{-- Loop through dynamic news articles --}}
                    @forelse($articles as $article)
                        <a href="{{ route('news.show', $article->slug) }}" class="news-card reveal">
                            @if($article->featured_image_path)
                                <img src="{{ asset('storage/' . $article->featured_image_path) }}" alt="{{ $article->title }}" loading="lazy" decoding="async">
                            @else
                                {{-- Placeholder if no image --}}
                                <div class="product-image-placeholder" style="height: 200px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa; color: var(--primary-color);">
                                    <i class="fas fa-newspaper" style="font-size: 4rem; opacity: 0.5;"></i>
                                </div>
                            @endif
                            <div class="news-card-content">
                                <span class="news-meta">
                                    {{ $article->newsCategory->name ?? __('messages.uncategorized') }} • {{ $article->published_at ? $article->published_at->format('M d, Y') : __('messages.no_date') }}
                                </span>
                                {{-- Display localized title and snippet --}}
                                <h3>{{ $article->{'title_' . app()->getLocale()} ?? $article->title }}</h3> {{-- Dynamically select title based on locale --}}
                                <p>{{ $article->{'snippet_' . app()->getLocale()} ?? $article->snippet }}</p> {{-- Dynamically select snippet based on locale --}}
                                <span class="read-more-btn">{{ __('messages.read_more') }} &rarr;</span>
                            </div>
                        </a>
                    @empty
                        <div class="no-results" style="display: block; grid-column: 1 / -1;">
                            <i class="fas fa-search" style="font-size: 4rem; color: #ced4da; margin-bottom: 20px;"></i>
                            <p style="font-size: 1.5rem; color: #6c757d;">{{ __('messages.no_news_articles_found') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- Loader (controlled by JS and CSS, remains visually subtle) --}}
        <div id="loader" class="loader">
            <div class="spinner"></div>
        </div>
    @endsection

    {{-- Push news.js to the end of the body (if you decide to use it for infinite scroll later) --}}
    {{-- @push('scripts')
        <script src="{{ asset('js/news.js') }}"></script>
    @endpush --}}