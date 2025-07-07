{{-- resources/views/news/index.blade.php --}}
@extends('layouts.app')

@section('title', 'News & Insights')

@section('content')
    <section class="page-header">
        <h1>News & Insights</h1>
        <p>Stay informed about the latest developments in healthcare logistics, distribution, and regulatory news in the Dominican Republic.</p>
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
                                    {{ $article->newsCategory->name ?? 'Uncategorized' }} • {{ $article->published_at ? $article->published_at->format('M d, Y') : 'No Date' }}
                                </span>
                                <h3>{{ $article->title }}</h3>
                                <p>{{ $article->snippet }}</p>
                                <span class="read-more-btn">Read More &rarr;</span>
                            </div>
                        </a>
                    @empty
                        <div class="no-results" style="display: block; grid-column: 1 / -1;">
                            <i class="fas fa-search" style="font-size: 4rem; color: #ced4da; margin-bottom: 20px;"></i>
                            <p style="font-size: 1.5rem; color: #6c757d;">No news articles found.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- Loader for infinite scroll (if implemented later) --}}
    <div id="loader" class="loader" style="display: none;">
        <div class="spinner"></div>
    </div>
@endsection

{{-- Push news.js to the end of the body (if you put news-specific JS here) --}}
{{-- @push('scripts')
    <script src="{{ asset('js/news.js') }}"></script>
@endpush --}}