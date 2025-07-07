{{-- resources/views/products/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Our Product Catalog')

@section('content')
    <section class="page-header">
        <h1>Our Product Catalog</h1>
        <p>Explore our extensive range of high-quality pharmaceutical products. Use the filters below to find exactly what you need.</p>
    </section>

    <section id="filter-section" class="filter-section">
        <div class="filter-controls">
            <div class="filter-group">
                <label for="search-input">Product Name or Keyword</label>
                <input type="text" id="search-input" placeholder="e.g., Analgesic, Antibiotic...">
            </div>
            <div class="filter-group">
                <label for="manufacturer-filter">Manufacturer</label>
                <select id="manufacturer-filter">
                    <option value="all">All Manufacturers</option>
                    @foreach($manufacturers as $manufacturer)
                        <option value="{{ $manufacturer->name }}">{{ $manufacturer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label for="dosage-form-filter">Dosage Form</label>
                <select id="dosage-form-filter">
                    <option value="all">All Dosage Forms</option>
                    @foreach($dosageForms as $dosageForm)
                        <option value="{{ $dosageForm->name }}">{{ $dosageForm->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label for="therapeutic-category-filter">Therapeutic Category</label>
                <select id="therapeutic-category-filter">
                    <option value="all">All Categories</option>
                    @foreach($therapeuticCategories as $category)
                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label for="availability-filter">Availability</label>
                <select id="availability-filter">
                    <option value="all">All Statuses</option>
                    <option value="Available">Available</option>
                    <option value="Out of Stock">Out of Stock</option>
                    <option value="Discontinued">Discontinued</option>
                    <option value="Coming Soon">Coming Soon</option>
                </select>
            </div>
            <div class="filter-buttons">
                <button id="apply-filters-btn"><i class="fas fa-filter"></i> Apply</button>
                <button id="reset-filters-btn">Reset</button>
            </div>
        </div>
    </section>

    <section class="catalog-section">
        <div class="container">
                 {{-- ADD THIS HIDDEN DIV TO PASS DATA --}}
            <div id="product-data-container"
                data-products='@json($products->items())'
                data-next-page-url="{{ $products->nextPageUrl() ?? 'null' }}"
                data-has-more-pages="{{ $products->hasMorePages() ? 'true' : 'false' }}"
                data-storage-base-url="{{ asset('storage/') }}"
                data-default-product-image-url="{{ asset('images/default-product.png') }}"
                data-product-show-route="{{ route('products.show', '') }}"
                data-product-base-url="{{ route('products.index') }}"
                style="display: none;"> {{-- IMPORTANT: Keep display:none to hide it --}}
            </div>
            {{-- END HIDDEN DIV --}}
            <div id="product-grid" class="product-grid">
                {{-- Products will be rendered by JavaScript --}}
            </div>
            {{-- Loader for infinite scroll --}}
<div id="loader" class="loader"> 
    <div class="spinner"></div>
</div>
            <div id="no-results-message" class="no-results" style="display: none;">
                <i class="fas fa-search" style="font-size: 4rem; color: #ced4da; margin-bottom: 20px;"></i>
                <p style="font-size: 1.5rem; color: #6c757d;">No products found matching your criteria.</p>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Pass the initial paginated product data to JavaScript via the global window object
        window.initialProductsData = @json($products->items());
        window.initialNextPageUrl = "{{ $products->nextPageUrl() ?? 'null' }}"; // Ensure 'null' string if PHP null
        window.initialHasMorePages = {{ $products->hasMorePages() ? 'true' : 'false' }};
        window.productBaseUrl = "{{ route('products.index') }}"; // Base URL for AJAX requests

        // Pass base URLs for asset generation in JS
        window.storageBaseUrl = "{{ asset('storage/') }}"; // Path to public/storage
        window.defaultProductImageUrl = "{{ asset('images/default-product.png') }}";
        window.productShowRoute = "{{ route('products.show', '') }}"; // Base route for detail page
    </script>
    <script src="{{ asset('js/products.js') }}"></script>
@endpush