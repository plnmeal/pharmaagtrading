{{-- resources/views/products/show.blade.php --}}
@extends('layouts.app')

@section('title', $product->name . ' | Product Detail')

@section('content')
    <section class="page-content-section">
        <div class="container">
            <div class="content-panel">
                <div class="product-detail-layout">
                    <div class="product-gallery">
                        <div class="main-image">
                            @if($product->product_image_path)
                                <img id="mainProductImage" src="{{ asset('storage/' . $product->product_image_path) }}" alt="{{ $product->name }}">
                            @else
                                <img id="mainProductImage" src="{{ asset('images/default-product.png') }}" alt="{{ $product->name }}">
                            @endif
                        </div>
                        {{-- Additional thumbnails can be added here if product has multiple images --}}
                        {{-- For now, using placeholder thumbnails or the main image --}}
                        <div class="thumbnail-container">
                            @if($product->product_image_path)
                                <img class="thumbnail active" src="{{ asset('storage/' . $product->product_image_path) }}" alt="{{ $product->name }} Thumbnail">
                            @else
                                <img class="thumbnail active" src="{{ asset('images/default-product.png') }}" alt="{{ $product->name }} Thumbnail">
                            @endif
                            {{-- Add more thumbnails if needed (e.g., from a product_images relationship/gallery) --}}
                            <img class="thumbnail" src="https://via.placeholder.com/100x100?text=Thumb+2" alt="Thumbnail 2">
                            <img class="thumbnail" src="https://via.placeholder.com/100x100?text=Thumb+3" alt="Thumbnail 3">
                            <img class="thumbnail" src="https://via.placeholder.com/100x100?text=Thumb+4" alt="Thumbnail 4">
                        </div>
                    </div>

                    <div class="product-info">
                        <h1>{{ $product->name }}</h1>
                        @if($product->manufacturer)
                            <p class="product-manufacturer">{{ $product->manufacturer->name }}</p>
                        @endif

                        @php
                            $statusClass = 'status-available';
                            if ($product->availability_status === 'Out of Stock') $statusClass = 'status-out-of-stock';
                            // Add more cases for other statuses if you want distinct colors
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ $product->availability_status }}</span>

                        <p class="product-intro">{{ $product->description }}</p>

                        <table class="info-table">
                            @if($product->dosageForm)
                                <tr>
                                    <td>Dosage Form:</td>
                                    <td>{{ $product->dosageForm->name }}</td>
                                </tr>
                            @endif
                            @if($product->therapeuticCategory)
                                <tr>
                                    <td>Therapeutic Category:</td>
                                    <td>{{ $product->therapeuticCategory->name }}</td>
                                </tr>
                            @endif
                            @if($product->ingredients)
                                <tr>
                                    <td>Ingredients:</td>
                                    <td>{{ $product->ingredients }}</td>
                                </tr>
                            @endif
                        </table>

                        <div class="cta-buttons">
                            <button id="select-btn" class="btn {{ $product->availability_status === 'Out of Stock' ? 'disabled' : '' }}" {{ $product->availability_status === 'Out of Stock' ? 'disabled' : '' }}>
                                {{ $product->availability_status === 'Out of Stock' ? 'Out of Stock' : 'Select' }}
                            </button>
                            <a href="mailto:sales@yourclientdomain.com?subject=Inquiry about {{ $product->name }}" class="btn btn-outline {{ $product->availability_status === 'Out of Stock' ? 'disabled' : '' }}" {{ $product->availability_status === 'Out of Stock' ? 'disabled' : '' }}>Request a Quote</a>
                        </div>
                    </div>
                </div>

                <div class="product-tabs-container">
                    <div class="tab-links">
                        <button class="tab-link active" data-tab="description">Description</button>
                        <button class="tab-link" data-tab="benefits">Benefits</button>
                        {{-- Add more tabs here for other product info if needed --}}
                    </div>
                    <div class="tab-content-wrapper">
                        <div id="description" class="tab-content active">
                            <h3>Product Overview</h3>
                            {!! $product->description !!}
                        </div>
                        <div id="benefits" class="tab-content">
                            <h3>Benefits & Usage</h3>
                            {!! $product->benefits !!}
                        </div>
                    </div>
                </div>
            </div>

            @if($relatedProducts->isNotEmpty())
                <div class="related-articles related-products"> {{-- Reusing CSS class, but for products --}}
                    <h2>Related Products</h2>
                    <div class="related-articles-grid"> {{-- Reusing CSS class --}}
                        @foreach($relatedProducts as $related)
                            <a href="{{ route('products.show', $related->slug) }}" class="related-product-card">
                                @if($related->product_image_path)
                                    <img src="{{ asset('storage/' . $related->product_image_path) }}" alt="{{ $related->name }}" style="width: 100%; height: 100px; object-fit: cover; border-radius: 4px; margin-bottom: 10px;">
                                @else
                                    <i class="fas fa-pills"></i>
                                @endif
                                <h4>{{ $related->name }}</h4>
                                <p style="font-size: 0.85rem; color: #6c757d;">{{ $related->manufacturer->name ?? '' }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/product-detail.js') }}"></script>
@endpush