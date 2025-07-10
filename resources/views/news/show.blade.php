{{-- resources/views/news/show.blade.php --}}
@extends('layouts.app')

@section('title', ($article->{'title_' . app()->getLocale()} ?? $article->title) . ' | ' . __('messages.news_insights_page_title'))
@section('meta_description', $article->{'snippet_' . app()->getLocale()} ?? $article->snippet)

@section('content')
    <section class="page-header" style="padding-top: 50px; padding-bottom: 50px;">
        <div class="container" style="max-width: 900px;">
            <h1>{{ $article->{'title_' . app()->getLocale()} ?? $article->title }}</h1>
            <p style="font-size: 0.95rem; color: #6c757d;">
                <i class="fas fa-calendar-alt"></i> {{ $article->published_at ? $article->published_at->format('M d, Y') : __('messages.no_date') }}
                @if($article->newsCategory)
                    | <i class="fas fa-tag"></i> {{ $article->newsCategory->name ?? __('messages.uncategorized') }}
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
                    {!! $article->{'content_' . app()->getLocale()} ?? $article->content !!} {{-- Render localized rich text content --}}
                </div>

                <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                    <h4>{{ __('messages.share_this_article') }}:</h4> {{-- New translation key --}}
                    <div class="social-links" style="margin-top: 10px;">
                        <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ urlencode($article->{'title_' . app()->getLocale()} ?? $article->title) }}" target="_blank" style="margin-right: 15px; color: #1DA1F2; font-size: 1.8rem;"><i class="fab fa-twitter-square"></i></a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" style="margin-right: 15px; color: #1877F2; font-size: 1.8rem;"><i class="fab fa-facebook-square"></i></a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ url()->current() }}&title={{ urlencode($article->{'title_' . app()->getLocale()} ?? $article->title) }}" target="_blank" style="color: #0A66C2; font-size: 1.8rem;"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>

            @if($relatedArticles->isNotEmpty())
                <div class="related-articles related-products">
                    <h2>{{ __('messages.related_articles') }}</h2> {{-- New translation key --}}
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
                                        {{ $related->newsCategory->name ?? __('messages.uncategorized') }} • {{ $related->published_at ? $related->published_at->format('M d, Y') : __('messages.no_date') }}
                                    </span>
                                    <h3>{{ $related->{'title_' . app()->getLocale()} ?? $related->title }}</h3>
                                    <p>{{ $related->{'snippet_' . app()->getLocale()} ?? $related->snippet }}</p>
                                    <span class="read-more-btn">{{ __('messages.read_more') }} &rarr;</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection