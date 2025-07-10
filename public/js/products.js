// public/js/products.js

console.log("products.js: File loaded successfully (outside DOMContentLoaded)."); // VERY FIRST LOG

document.addEventListener('DOMContentLoaded', () => {
    console.log("products.js: DOMContentLoaded event fired.");

    const productGrid = document.getElementById('product-grid');
    console.log("products.js: productGrid element check. Found:", !!productGrid);
    if (!productGrid) {
        console.warn("products.js: productGrid element (id='product-grid') not found. Exiting script.");
        return;
    }

    const dataContainer = document.getElementById('product-data-container');
    console.log("products.js: dataContainer element check. Found:", !!dataContainer);
    if (!dataContainer) {
        console.error("Error: products.js: product-data-container (id='product-data-container') not found. Initial product data cannot be loaded.");
        return;
    }

    // --- Logs before parsing data-attributes ---
    console.log("products.js: Attempting to read data-attributes from dataContainer.");
    console.log("  data-products attribute content (first 100 chars):", dataContainer.dataset.products ? dataContainer.dataset.products.substring(0, 100) + '...' : 'empty');
    console.log("  data-next-page-url attribute content:", dataContainer.dataset.nextPageUrl);
    console.log("  data-has-more-pages attribute content:", dataContainer.dataset.hasMorePages);
    // --- End logs before parsing ---

    let allProducts;
    try {
        allProducts = JSON.parse(dataContainer.dataset.products);
        console.log("products.js: JSON.parse for data-products successful. Total products count:", allProducts.length);
    } catch (e) {
        console.error("Error: products.js: Failed to parse initial product data JSON from data-products attribute!", e);
        return;
    }

    let nextProductsPageUrl = dataContainer.dataset.nextPageUrl === 'null' ? null : dataContainer.dataset.nextPageUrl;
    let hasMorePages = dataContainer.dataset.hasMorePages === 'true';

    console.log("products.js: Initial Pagination State Loaded:");
    console.log("  Next Page URL:", nextProductsPageUrl);
    console.log("  Has More Pages:", hasMorePages);

    const storageBaseUrl = dataContainer.dataset.storageBaseUrl;
    const defaultProductImageUrl = dataContainer.dataset.defaultProductImageUrl;
    const productShowRoute = dataContainer.dataset.productShowRoute;
    const productBaseUrl = dataContainer.dataset.productBaseUrl;
    console.log("products.js: Base URLs and routes extracted.");

    let isLoading = false;

    // --- DOM ELEMENTS ---
    const loader = document.getElementById('loader');
    const noResultsMessage = document.getElementById('no-results-message');
    const searchInput = document.getElementById('search-input');
    const manufacturerFilter = document.getElementById('manufacturer-filter');
    const dosageFormFilter = document.getElementById('dosage-form-filter');
    const therapeuticCategoryFilter = document.getElementById('therapeutic-category-filter');
    const availabilityFilter = document.getElementById('availability-filter');
    const applyBtn = document.getElementById('apply-filters-btn');
    const resetBtn = document.getElementById('reset-filters-btn');
    const header = document.querySelector('.sticky-header'); // For sticky filter calculation
    const filterSection = document.getElementById('filter-section'); // For sticky filter calculation
    console.log("products.js: All main DOM elements queried.");


    // --- CORE FUNCTIONS ---

    // Function to create a single product card HTML element
    const createProductCard = (product) => {
        const cardLink = document.createElement('a');
        cardLink.href = `${productShowRoute}/${product.slug}`;
        cardLink.className = 'product-card-link';
        cardLink.style.textDecoration = 'none';

        const isOutOfStock = product.availability_status === 'Out of Stock';
        const statusClass = isOutOfStock ? 'status-out-of-stock' : 'status-available';
        const buttonText = isOutOfStock ? 'Out of Stock' : 'View Details';

        const imageUrl = product.product_image_path_raw
            ? `${storageBaseUrl}${product.product_image_path_raw}`
            : defaultProductImageUrl;

        const descriptionSnippet = product.description
            ? product.description.substring(0, 100) + (product.description.length > 100 ? '...' : '')
            : '';

        cardLink.innerHTML = `
            <article class="product-card ${isOutOfStock ? 'out-of-stock' : ''}">
                <div class="status-badge-list ${statusClass}">${product.availability_status}</div>
                <img src="${imageUrl}" alt="${product.name}" class="product-image" loading="lazy" decoding="async">
                <div class="product-card-content">
                    <h3 style="color: var(--text-color);">${product.name}</h3>
                    <p class="product-manufacturer" style="color: #6c757d;">${product.manufacturer.name || ''}</p>
                    <div class="tag-container">
                        <span class="tag">${product.dosage_form.name || ''}</span>
                        <span class="tag">${product.therapeutic_category ? product.therapeutic_category.name : ''}</span>
                    </div>
                    <p class="product-description">${descriptionSnippet}</p>
                    <span class="enquire-btn">${buttonText}</span>
                </div>
            </article>
        `;
        return cardLink;
    };

    // Function to render products to the grid (either append or replace)
    const renderProducts = (productsToRender, append = true) => {
        if (!append) {
            productGrid.innerHTML = '';
            allProducts = [];
            currentPage = 1;
        }

        const fragment = document.createDocumentFragment();
        let countInBatch = 0;
        const productsPerRow = 4;

        productsToRender.forEach(product => {
            fragment.appendChild(createProductCard(product));
            countInBatch++;

            if (countInBatch % productsPerRow === 0 || countInBatch === productsToRender.length) {
                productGrid.appendChild(fragment);
            }
        });

        allProducts = allProducts.concat(productsToRender);

        noResultsMessage.style.display = allProducts.length === 0 ? 'block' : 'none';
        if (loader) loader.style.opacity = '0'; // Hide loader once products are rendered
    };

    // Function to fetch products from the server via AJAX
    const fetchProducts = async (url, append = true) => {
        if (isLoading || (append && !hasMorePages && url !== productBaseUrl)) {
            console.log("products.js: Fetch request skipped. Loading:", isLoading, "Has more pages:", hasMorePages, "URL:", url);
            return;
        }

        isLoading = true;
        if (loader) loader.style.opacity = '1'; // Show loader when fetching starts

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            renderProducts(data.products, append);

            nextProductsPageUrl = data.next_page_url;
            hasMorePages = data.has_more_pages;
            if (append) {
                currentPage++;
            }

        } catch (error) {
            console.error('products.js: Error fetching products:', error);
        } finally {
            isLoading = false;
            if (!hasMorePages && loader) {
                loader.style.opacity = '0';
            }
            if (!append && loader) {
                loader.style.opacity = '0';
            }
        }
    };

    // Function to apply filters and trigger a new product fetch
    const applyFilters = () => {
        if (loader && infiniteScrollObserver) {
            infiniteScrollObserver.unobserve(loader);
        }

        let url = new URL(productBaseUrl);
        url.searchParams.append('ajax', '1');
        url.searchParams.append('page', 1);

        if (searchInput.value.trim() !== '') url.searchParams.append('search', searchInput.value.trim());
        if (manufacturerFilter.value !== 'all') url.searchParams.append('manufacturer', manufacturerFilter.value);
        if (dosageFormFilter.value !== 'all') url.searchParams.append('dosageForm', dosageFormFilter.value);
        if (therapeuticCategoryFilter.value !== 'all') url.searchParams.append('therapeuticCategory', therapeuticCategoryFilter.value);
        if (availabilityFilter.value !== 'all') url.searchParams.append('availability', availabilityFilter.value);

        nextProductsPageUrl = null;
        hasMorePages = false;

        fetchProducts(url.toString(), false).then(() => {
            if (loader && infiniteScrollObserver) {
                infiniteScrollObserver.observe(loader);
            }
        });
    };

    // Function to reset all filter inputs to their default states
    const resetAllFilters = () => {
        searchInput.value = '';
        manufacturerFilter.value = 'all';
        dosageFormFilter.value = 'all';
        therapeuticCategoryFilter.value = 'all';
        availabilityFilter.value = 'all';
        applyFilters();
    };

    // Function to calculate and set the position of the sticky filter bar
    const setStickyFilterTop = () => {
        if (header && filterSection) {
            const headerHeight = header.offsetHeight;
            filterSection.style.top = `${headerHeight}px`;
        }
    };


    // --- EVENT LISTENERS ---

    // Filter button click listeners
    if (applyBtn) applyBtn.addEventListener('click', applyFilters);
    if (resetBtn) resetBtn.addEventListener('click', resetAllFilters);

    // Filter change listeners (input and select elements)
    if (searchInput) searchInput.addEventListener('keyup', (event) => {
        if (event.key === 'Enter') applyFilters();
    });
    if (manufacturerFilter) manufacturerFilter.addEventListener('change', applyFilters);
    if (dosageFormFilter) dosageFormFilter.addEventListener('change', applyFilters);
    if (therapeuticCategoryFilter) therapeuticCategoryFilter.addEventListener('change', applyFilters);
    if (availabilityFilter) availabilityFilter.addEventListener('change', applyFilters);
    if (searchInput) searchInput.addEventListener('input', applyFilters);


    // Infinite Scroll Listener setup
    const infiniteScrollObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            console.groupCollapsed("Observer Debug Entry Details (Click to expand)");
            console.log("  Timestamp:", entry.time);
            console.log("  isIntersecting:", entry.isIntersecting);
            console.log("  intersectionRatio:", entry.intersectionRatio);
            console.log("  Target Element:", entry.target);
            console.log("  Target Bounding Rect:", entry.boundingClientRect.toJSON());
            console.log("  Root Bounds (Viewport):", entry.rootBounds.toJSON());
            console.log("  Intersection Rect:", entry.intersectionRect.toJSON());
            console.groupEnd();

            if (entry.isIntersecting && !isLoading && hasMorePages) {
                if (nextProductsPageUrl) {
                    fetchProducts(nextProductsPageUrl, true);
                } else {
                    console.warn("products.js: nextProductsPageUrl is null, cannot fetch more pages. (This should only happen if hasMorePages is true but URL is null, a state unlikely to be reached if initial data is correct)");
                    if (loader) loader.style.opacity = '0';
                }
            } else if (!hasMorePages) {
                if (loader) loader.style.opacity = '0';
            }
        });
    }, {
        rootMargin: '0px 0px 400px 0px'
    });

    if (loader) {
        infiniteScrollObserver.observe(loader);
    } else {
        console.warn("products.js: Loader element (id='loader') not found for IntersectionObserver. Infinite scroll will not function.");
    }

    window.addEventListener('resize', setStickyFilterTop);


    // --- INITIALIZATION ---

    renderProducts(allProducts, false);
    setStickyFilterTop();
});