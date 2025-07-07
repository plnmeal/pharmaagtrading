{{-- resources/views/welcome.blade.php --}}
@extends('layouts.app') {{-- EXTEND THE MASTER LAYOUT --}}

{{-- Dynamically set title and meta description from Global Site Settings --}}
@section('title', $settings->hero_title ?? 'Quality Pharma Solutions in the Dominican Republic')
@section('meta_description', $settings->site_description ?? 'PharmaAGTrading is a leading pharmaceutical distributor in the Dominican Republic, offering intelligent supply chain services, a comprehensive product portfolio, and a nationwide logistics network.')

{{-- Push Leaflet CSS to the head (only on this page, as map is here) --}}
@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
    <section class="hero-section" id="home">
        <div class="hero-content-wrapper reveal active" style="background: rgba(0, 105, 148, 0.8) url('{{ asset('storage/' . ($settings->hero_image_path ?? 'images/pharma-facility.jpg')) }}') no-repeat center center; background-size: cover;">
            <h1>{{ $settings->hero_title ?? 'Your Gateway to Quality Pharma Solutions' }}</h1>
            <p>{!! $settings->hero_subtitle ?? 'Powering the health of the Dominican Republic with a distribution network built on precision, reliability, and an unwavering commitment to quality.' !!}</p>
            <div>
                <a href="{{ route('products.index') }}" class="btn">Explore Our Portfolio</a>
            </div>
        </div>
    </section>

    <section class="partners-section" id="partners">
        <div class="container reveal">
            <div class="section-title">
                <h2>Trusted by Healthcare Leaders</h2>
            </div>
            <div class="partner-logos">
                {{-- Dynamic Featured Manufacturers --}}
                @forelse($manufacturers as $manufacturer)
                    <img src="{{ asset('storage/' . $manufacturer->logo_path) }}" alt="{{ $manufacturer->name }} Logo" class="partner-logo" loading="lazy" decoding="async">
                @empty
                    {{-- Fallback if no manufacturers --}}
                    <p>No trusted partners found.</p>
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
                        <span class="subtitle">Our Infrastructure</span>
                        <h2>A Nationwide Distribution Grid</h2>
                    </div>
                    <p>Our network is strategically designed for maximum efficiency, ensuring vital products are delivered where they are needed most, on time, every time.</p>
                    <div class="map-stats">
                        {{-- Map stats are currently hardcoded in HTML, but can be dynamic from Global Settings if you add fields like 'num_hubs', 'provinces_served' to 'settings' table --}}
                        <div class="stat-item"><div class="number" data-goal="5">0</div><div class="text">Major Hubs</div></div>
                        <div class="stat-item"><div class="number" data-goal="32">0</div><div class="text">Provinces Served</div></div>
                        <div class="stat-item"><div class="number" data-goal="24">0</div><div class="text">/7 Logistics Support</div></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="offerings-section" id="portfolio">
        <div class="container">
            <div class="section-title reveal">
                <h2>A Comprehensive Product Portfolio</h2>
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
                    <p>No product offerings found.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="news-section" id="news">
        <div class="container">
            <div class="section-title reveal">
                <h2>Latest News & Insights</h2>
                <p>Stay informed about the latest developments in healthcare logistics and distribution.</p>
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
                                {{ $article->newsCategory->name ?? 'Uncategorized' }} • {{ $article->published_at ? $article->published_at->format('M d, Y') : 'No Date' }}
                            </span>
                            <h3>{{ $article->title }}</h3>
                            <p>{{ $article->snippet }}</p>
                            <span class="read-more-btn">Read More &rarr;</span>
                        </div>
                    </a>
                @empty
                    <p>No latest news found.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="supply-chain-section" id="services">
        <div class="container">
            <div class="section-title reveal">
                <h2>Intelligent Supply Chain Services</h2>
            </div>
            <div class="supply-chain-content reveal">
                <div class="supply-chain-text">
                    <p>We leverage AI-powered inventory management, end-to-end validated cold chain logistics, and expert regulatory guidance to create a resilient and efficient supply chain.</p>
                    <a href="#" class="btn" style="margin-top:20px;">Our Solutions</a>
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
                    <p>No services found.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="connect-section" id="contact" style="background: rgba(0, 105, 148, 0.85) url('{{ asset('storage/' . ($settings->cta_background_image_path ?? 'images/professional-background.png')) }}') no-repeat center center; background-size: cover;">
        <div class="container reveal">
            <h2>{{ $settings->cta_title ?? 'Build the Future of Healthcare, Together.' }}</h2>
            <p>{!! $settings->cta_description ?? 'Join our network of leading manufacturers and healthcare providers. Let\'s discuss how we can create a more resilient and efficient supply chain in the Dominican Republic.' !!}</p>
            <a href="{{ $settings->cta_button_link ?? route('contact.index') }}" class="btn">{{ $settings->cta_button_text ?? 'Become a Partner' }}</a>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/home.js') }}"></script> {{-- Home page specific JS --}}
@endpush