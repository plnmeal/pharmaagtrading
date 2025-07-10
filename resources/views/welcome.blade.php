{{-- resources/views/welcome.blade.php --}}
@extends('layouts.app') {{-- EXTEND THE MASTER LAYOUT --}}

{{-- Dynamically set title and meta description from Global Site Settings --}}
{{-- Using translation keys for default fallbacks --}}
@section('title', ($settings->hero_title ?? __('messages.home_default_title')) . ' | ' . ($settings->site_name ?? 'Ayuva'))
@section('meta_description', $settings->site_description ?? __('messages.site_default_description'))

{{-- Push Leaflet CSS to the head (only on this page, as map is here) --}}
@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
    <section class="hero-section" id="home">
        <div class="hero-content-wrapper reveal active" style="background: rgba(0, 105, 148, 0.8) url('{{ asset('storage/' . ($settings->hero_image_path ?? 'images/pharma-facility.jpg')) }}') no-repeat center center; background-size: cover;">
            <h1>{{ $settings->hero_title ?? __('messages.your_gateway_title') }}</h1> {{-- Updated fallback to translation key --}}
            <p>{!! $settings->hero_subtitle ?? __('messages.your_gateway_subtitle') !!}</p> {{-- Updated fallback to translation key --}}
            <div>
                <a href="{{ route('products.index') }}" class="btn">{{ __('messages.explore_portfolio_button') }}</a> {{-- Updated to translation key --}}
            </div>
        </div>
    </section>

    <section class="partners-section" id="partners">
        <div class="container reveal">
            <div class="section-title">
                <h2>{{ __('messages.trusted_by_healthcare_leaders') }}</h2> {{-- Updated to translation key --}}
            </div>
            <div class="partner-logos">
                {{-- Dynamic Featured Manufacturers --}}
                @forelse($manufacturers as $manufacturer)
                    <img src="{{ asset('storage/' . $manufacturer->logo_path) }}" alt="{{ $manufacturer->name }} Logo" class="partner-logo" loading="lazy" decoding="async">
                @empty
                    <p>{{ __('messages.no_trusted_partners') }}</p> {{-- Updated to translation key --}}
                @endforelse
            </div>
        </div>
    </section>

    <section class="map-section" id="network">
        <div class="container reveal">
            <div class="map-grid">
                <div class="map-container">
                    <div id="distributionMap"></div>
                </div>
                <div class="map-content">
                    <div class="section-header">
                        <span class="subtitle">{{ __('messages.our_infrastructure') }}</span> {{-- Updated to translation key --}}
                        <h2>{{ __('messages.a_nationwide_distribution_grid') }}</h2> {{-- Updated to translation key --}}
                    </div>
                    <p>{{ __('messages.nationwide_distribution_desc') }}</p> {{-- Added translation key --}}
                    <div class="map-stats">
                        {{-- Map stats are currently hardcoded in HTML, but can be dynamic from Global Settings --}}
                        <div class="stat-item"><div class="number" data-goal="5">0</div><div class="text">{{ __('messages.major_hubs') }}</div></div> {{-- Updated to translation key --}}
                        <div class="stat-item"><div class="number" data-goal="32">0</div><div class="text">{{ __('messages.provinces_served') }}</div></div> {{-- Updated to translation key --}}
                        <div class="stat-item"><div class="number" data-goal="24">0</div><div class="text">24{{ __('messages.logistics_support') }}</div></div> {{-- Updated to translation key --}}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="offerings-section" id="portfolio">
        <div class="container">
            <div class="section-title reveal">
                <h2>{{ __('messages.a_comprehensive_product_portfolio') }}</h2> {{-- Updated to translation key --}}
            </div>
            <div class="offerings-grid">
                {{-- Dynamic Featured Dosage Forms --}}
                @forelse($dosageForms as $dosageForm)
                    <div class="offering-card reveal">
                        <i class="{{ $dosageForm->icon_class }}"></i>
                        <h3>{{ $dosageForm->name }}</h3>
                        <p>{{ $dosageForm->description }}</p>
                    </div>
                @empty
                    <p>{{ __('messages.no_product_offerings') }}</p> {{-- Added translation key --}}
                @endforelse
            </div>
        </div>
    </section>

    <section class="news-section" id="news">
        <div class="container">
            <div class="section-title reveal">
                <h2>{{ __('messages.latest_news_insights') }}</h2> {{-- Updated to translation key --}}
                <p>{{ __('messages.stay_informed_news_section') }}</p> {{-- Updated to translation key --}}
            </div>
            <div class="news-grid">
                {{-- Dynamic Latest News Articles (featured) --}}
                @forelse($newsArticles as $article)
                    <a href="{{ route('news.show', $article->slug) }}" class="news-card reveal">
                        @if($article->featured_image_path)
                            <img src="{{ asset('storage/' . $article->featured_image_path) }}" alt="{{ $article->title }}" loading="lazy" decoding="async">
                        @else
                            <div class="product-image-placeholder" style="height: 200px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa; color: var(--primary-color);">
                                <i class="fas fa-newspaper" style="font-size: 4rem; opacity: 0.5;"></i>
                            </div>
                        @endif
                        <div class="news-card-content">
                            <span class="news-meta">
                                {{ $article->newsCategory->name ?? __('messages.uncategorized') }} • {{ $article->published_at ? $article->published_at->format('M d, Y') : __('messages.no_date') }}
                            </span>
                            <h3>{{ $article->title }}</h3>
                            <p>{{ $article->snippet }}</p>
                            <span class="read-more-btn">{{ __('messages.read_more') }} &rarr;</span> {{-- Updated to translation key --}}
                        </div>
                    </a>
                @empty
                    <p>{{ __('messages.no_latest_news') }}</p> {{-- Added translation key --}}
                @endforelse
            </div>
        </div>
    </section>

    <section class="supply-chain-section" id="services">
        <div class="container">
            <div class="section-title reveal">
                <h2>{{ __('messages.intelligent_supply_chain_services') }}</h2> {{-- Updated to translation key --}}
            </div>
            <div class="supply-chain-content reveal">
                <div class="supply-chain-text">
                    <p>{{ __('messages.supply_chain_desc') }}</p> {{-- Added translation key --}}
                    <a href="#" class="btn" style="margin-top:20px;">{{ __('messages.our_solutions') }}</a> {{-- Updated to translation key --}}
                </div>
            </div>
            <div class="supply-grid">
                {{-- Dynamic Services --}}
                @forelse($services as $service)
                    <div class="data-card reveal">
                        <div class="icon"><i class="{{ $service->icon_class }}"></i></div>
                        <h3>{{ $service->title }}</h3>
                        <p>{{ $service->description }}</p>
                    </div>
                @empty
                    <p>{{ __('messages.no_services_found') }}</p> {{-- Added translation key --}}
                @endforelse
            </div>
        </div>
    </section>

    <section class="connect-section" id="contact" style="background: rgba(0, 105, 148, 0.85) url('{{ asset('storage/' . ($settings->cta_background_image_path ?? 'images/professional-background.png')) }}') no-repeat center center; background-size: cover;">
        <div class="container reveal">
            <h2>{{ $settings->cta_title ?? __('messages.build_future_together') }}</h2> {{-- Updated fallback to translation key --}}
            <p>{!! $settings->cta_description ?? __('messages.join_network_cta_desc') !!}</p> {{-- Updated fallback to translation key --}}
            <a href="{{ $settings->cta_button_link ?? route('contact.index') }}" class="btn">{{ $settings->cta_button_text ?? __('messages.become_a_partner') }}</a> {{-- Updated fallback to translation key --}}
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/home.js') }}"></script> {{-- Home page specific JS --}}
@endpush