{{-- resources/views/products/index.blade.php --}}
@extends('layouts.app')

{{-- Page Title and Meta Description --}}
@section('title', __('messages.our_product_catalog_page_title') . ' | ' . ($settings->site_name ?? __('messages.pharmaagtrading_name_default')))
@section('meta_description', __('messages.explore_product_catalog_desc'))

@section('content')
    <section class="page-header">
        <h1>{{ __('messages.our_product_catalog_page_title') }}</h1>
        <p>{{ __('messages.explore_product_catalog_desc') }}</p>
    </section>

    <section id="filter-section" class="filter-section">
        <div class="filter-controls">
            <div class="filter-group">
                <label for="search-input">{{ __('messages.product_name_keyword') }}</label>
                <input type="text" id="search-input" placeholder="{{ __('messages.product_name_keyword_placeholder') }}">
            </div>
            <div class="filter-group">
                <label for="manufacturer-filter">{{ __('messages.manufacturer') }}</label>
                <select id="manufacturer-filter">
                    <option value="all">{{ __('messages.all_manufacturers') }}</option>
                    {{-- Manufacturer names are dynamic content, will be localized here --}}
                    @foreach($manufacturers as $manufacturer)
                        <option value="{{ $manufacturer->name }}">{{ $manufacturer->{'name_' . app()->getLocale()} ?? $manufacturer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label for="dosage-form-filter">{{ __('messages.dosage_form') }}</label>
                <select id="dosage-form-filter">
                    <option value="all">{{ __('messages.all_dosage_forms') }}</option>
                    {{-- Dosage Form names are dynamic content, will be localized here --}}
                    @foreach($dosageForms as $dosageForm)
                        <option value="{{ $dosageForm->name }}">{{ $dosageForm->{'name_' . app()->getLocale()} ?? $dosageForm->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label for="therapeutic-category-filter">{{ __('messages.therapeutic_category') }}</label>
                <select id="therapeutic-category-filter">
                    <option value="all">{{ __('messages.all_categories') }}</option>
                    {{-- Therapeutic Category names are dynamic content, will be localized here --}}
                    @foreach($therapeuticCategories as $category)
                        <option value="{{ $category->name }}">{{ $category->{'name_' . app()->getLocale()} ?? $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label for="availability-filter">{{ __('messages.availability') }}</label>
                <select id="availability-filter">
                    <option value="all">{{ __('messages.all_statuses') }}</option>
                    <option value="Available">{{ __('messages.available') }}</option>
                    <option value="Out of Stock">{{ __('messages.out_of_stock') }}</option>
                    <option value="Discontinued">{{ __('messages.discontinued') }}</option>
                    <option value="Coming Soon">{{ __('messages.coming_soon') }}</option>
                </select>
            </div>
            <div class="filter-buttons">
                <button id="apply-filters-btn"><i class="fas fa-filter"></i> {{ __('messages.apply') }}</button>
                <button id="reset-filters-btn">{{ __('messages.reset') }}</button>
            </div>
        </div>
    </section>

    <section class="catalog-section">
        <div class="container">
            <div class="content-panel-grid">
            {{-- Data passed as raw JSON to products.js --}}
            {{-- The 'localizedProducts' variable is now prepared in the Controller --}}
            <div id="product-data-container"
                 data-products='@json($localizedProducts)'
                 data-next-page-url="{{ $products->nextPageUrl() ?? 'null' }}"
                 data-has-more-pages="{{ $products->hasMorePages() ? 'true' : 'false' }}"
                 data-storage-base-url="{{ asset('storage/') }}"
                 data-default-product-image-url="{{ asset('images/default-product.png') }}"
                 data-product-show-route="{{ route('products.show', '') }}"
                 data-product-base-url="{{ route('products.index') }}"
                 style="display: none;">
            </div>

            <div id="product-grid" class="product-grid">
                {{-- Products will be rendered by JavaScript --}}
            </div>

            <div id="loader" class="loader">
                <div class="spinner"></div>
            </div>
            <div id="no-results-message" class="no-results" style="display: none;">
                <i class="fas fa-search" style="font-size: 4rem; color: #ced4da; margin-bottom: 20px;"></i>
                <p style="font-size: 1.5rem; color: #6c757d;">{{ __('messages.no_products_found') }}</p>
            </div>
        </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/products.js') }}"></script>
@endpush