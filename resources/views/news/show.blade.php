{{-- resources/views/news/show.blade.php --}}
@extends('layouts.app')

@section('title', $article->title)

@section('content')
    <section class="page-header" style="padding-top: 50px; padding-bottom: 50px;">
        <div class="container" style="max-width: 900px;">
            <h1 style="font-size: 2.5rem;">{{ $article->title }}</h1>
            <p style="font-size: 0.95rem; color: #6c757d;">
                <i class="fas fa-calendar-alt"></i> {{ $article->published_at ? $article->published_at->format('F d, Y') : 'No Date' }}
                @if($article->newsCategory)
                    | <i class="fas fa-tag"></i> {{ $article->newsCategory->name }}
                @endif
            </p>
        </div>
    </section>

    <section class="news-listing-section"> {{-- Reusing news-listing-section style --}}
        <div class="container" style="max-width: 900px;">
            <div class="content-panel">
                @if($article->featured_image_path)
                    <img src="{{ asset('storage/' . $article->featured_image_path) }}" alt="{{ $article->title }}" style="width: 100%; height: auto; border-radius: 8px; margin-bottom: 30px; object-fit: cover;" loading="lazy" decoding="async">
                @endif

                <div class="article-content" style="line-height: 1.8; font-size: 1.1rem;">
                    {!! $article->content !!} {{-- Render rich text content --}}
                </div>

                <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                    <h4>Share This Article:</h4>
                    <div class="social-links" style="margin-top: 10px;">
                        <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ urlencode($article->title) }}" target="_blank" style="margin-right: 15px; color: #1DA1F2; font-size: 1.8rem;"><i class="fab fa-twitter-square"></i></a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" style="margin-right: 15px; color: #1877F2; font-size: 1.8rem;"><i class="fab fa-facebook-square"></i></a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ url()->current() }}&title={{ urlencode($article->title) }}" target="_blank" style="color: #0A66C2; font-size: 1.8rem;"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>

            @if($relatedArticles->isNotEmpty())
                <div class="related-articles" style="margin-top: 60px; padding-top: 40px; border-top: 1px solid var(--border-color);">
                    <h2 style="text-align: center; margin-bottom: 30px;">Related Articles</h2>
                    <div class="news-grid">
                        @foreach($relatedArticles as $related)
                            <a href="{{ route('news.show', $related->slug) }}" class="news-card reveal">
                                @if($related->featured_image_path)
                                    <img src="{{ asset('storage/' . $related->featured_image_path) }}" alt="{{ $related->title }}" loading="lazy" decoding="async">
                                @else
                                    <div class="product-image-placeholder" style="height: 200px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa; color: var(--primary-color);">
                                        <i class="fas fa-newspaper" style="font-size: 4rem; opacity: 0.5;"></i>
                                    </div>
                                @endif
                                <div class="news-card-content">
                                    <span class="news-meta">
                                        {{ $related->newsCategory->name ?? 'Uncategorized' }} • {{ $related->published_at ? $related->published_at->format('M d, Y') : 'No Date' }}
                                    </span>
                                    <h3>{{ $related->title }}</h3>
                                    <p>{{ $related->snippet }}</p>
                                    <span class="read-more-btn">Read More &rarr;</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection