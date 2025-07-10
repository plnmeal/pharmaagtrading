{{-- resources/views/products/show.blade.php --}}
@extends('layouts.app')

@section('title', ($product->{'name_' . app()->getLocale()} ?? $product->name) . ' | ' . __('messages.our_product_catalog_page_title'))
@section('meta_description', $product->{'description_' . app()->getLocale()} ?? $product->description)

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
                        <div class="thumbnail-container">
                            @if($product->product_image_path)
                                <img class="thumbnail active" src="{{ asset('storage/' . $product->product_image_path) }}" alt="{{ $product->name }} Thumbnail">
                            @else
                                <img class="thumbnail active" src="{{ asset('images/default-product.png') }}" alt="{{ $product->name }} Thumbnail">
                            @endif
                            {{-- Placeholder thumbnails --}}
                            <img class="thumbnail" src="https://via.placeholder.com/100x100?text=Thumb+2" alt="Thumbnail 2">
                            <img class="thumbnail" src="https://via.placeholder.com/100x100?text=Thumb+3" alt="Thumbnail 3">
                            <img class="thumbnail" src="https://via.placeholder.com/100x100?text=Thumb+4" alt="Thumbnail 4">
                        </div>
                    </div>

                    <div class="product-info">
                        <h1>{{ $product->{'name_' . app()->getLocale()} ?? $product->name }}</h1>
                        @if($product->manufacturer)
                            <p class="product-manufacturer">{{ $product->manufacturer->{'name_' . app()->getLocale()} ?? $product->manufacturer->name }}</p>
                        @endif

                        @php
                            $statusClass = 'status-available';
                            if ($product->availability_status === 'Out of Stock') $statusClass = 'status-out-of-stock';
                            // Add more cases for other statuses if you want distinct colors
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ $product->availability_status }}</span>

                        <p class="product-intro">{{ $product->{'description_' . app()->getLocale()} ?? $product->description }}</p>

                        <table class="info-table">
                            @if($product->dosageForm)
                                <tr>
                                    <td>{{ __('messages.dosage_form') }}:</td>
                                    <td>{{ $product->dosageForm->{'name_' . app()->getLocale()} ?? $product->dosageForm->name }}</td>
                                </tr>
                            @endif
                            @if($product->therapeuticCategory)
                                <tr>
                                    <td>{{ __('messages.therapeutic_category') }}:</td>
                                    <td>{{ $product->therapeuticCategory->{'name_' . app()->getLocale()} ?? $product->therapeuticCategory->name }}</td>
                                </tr>
                            @endif
                            @if($product->ingredients)
                                <tr>
                                    <td>{{ __('messages.ingredients') }}:</td>
                                    <td>{{ $product->{'ingredients_' . app()->getLocale()} ?? $product->ingredients }}</td>
                                </tr>
                            @endif
                        </table>

                        <div class="cta-buttons">
                            <button id="select-btn" class="btn {{ $product->availability_status === 'Out of Stock' ? 'disabled' : '' }}" {{ $product->availability_status === 'Out of Stock' ? 'disabled' : '' }}>
                                {{ $product->availability_status === 'Out of Stock' ? __('messages.out_of_stock') : __('messages.select') }}
                            </button>
                            <a href="mailto:sales@yourclientdomain.com?subject={{ urlencode($product->{'name_' . app()->getLocale()} ?? $product->name) }}" class="btn btn-outline {{ $product->availability_status === 'Out of Stock' ? 'disabled' : '' }}" {{ $product->availability_status === 'Out of Stock' ? 'disabled' : '' }}>{{ __('messages.request_a_quote') }}</a>
                        </div>
                    </div>
                </div>

                <div class="product-tabs-container">
                    <div class="tab-links">
                        <button class="tab-link active" data-tab="description">{{ __('messages.description_tab') }}</button>
                        <button class="tab-link" data-tab="benefits">{{ __('messages.benefits_tab') }}</button>
                    </div>
                    <div class="tab-content-wrapper">
                        <div id="description" class="tab-content active">
                            <h3>{{ __('messages.product_overview') }}</h3>
                            {!! $product->{'description_' . app()->getLocale()} ?? $product->description !!}
                        </div>
                        <div id="benefits" class="tab-content">
                            <h3>{{ __('messages.benefits_usage') }}</h3>
                            {!! $product->{'benefits_' . app()->getLocale()} ?? $product->benefits !!}
                        </div>
                    </div>
                </div>
            </div>

            @if($relatedProducts->isNotEmpty())
                <div class="related-articles related-products">
                    <h2>{{ __('messages.related_products') }}</h2>
                    <div class="related-articles-grid">
                        @foreach($relatedProducts as $related)
                            <a href="{{ route('products.show', $related->slug) }}" class="related-product-card">
                                @if($related->product_image_path)
                                    <img src="{{ asset('storage/' . $related->product_image_path) }}" alt="{{ $related->name }}" style="width: 100%; height: 100px; object-fit: cover; border-radius: 4px; margin-bottom: 10px;">
                                @else
                                    <div class="product-image-placeholder" style="height: 100px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa; color: var(--primary-color);">
                                        <i class="fas fa-pills" style="font-size: 2.5rem; opacity: 0.5;"></i>
                                    </div>
                                @endif
                                <h4>{{ $related->{'name_' . app()->getLocale()} ?? $related->name }}</h4>
                                <p style="font-size: 0.85rem; color: #6c757d;">{{ $related->manufacturer->{'name_' . app()->getLocale()} ?? $related->manufacturer->name ?? '' }}</p>
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