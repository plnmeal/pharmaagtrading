// public/js/products.js

console.log("products.js: File loaded successfully (outside DOMContentLoaded).");

document.addEventListener('DOMContentLoaded', () => {
    console.log("products.js: DOMContentLoaded event fired.");

    const productGrid = document.getElementById('product-grid');
    console.log("products.js: productGrid element check. Found:", !!productGrid);
    if (!productGrid) {
        console.warn("products.js: productGrid element (id='product-grid') not found. Exiting script.");
        return;
    }

    // --- 1. GLOBAL DATA & STATE ---
    const dataContainer = document.getElementById('product-data-container');
    console.log("products.js: dataContainer element check. Found:", !!dataContainer);
    if (!dataContainer) {
        console.error("Error: products.js: product-data-container (id='product-data-container') not found. Initial product data cannot be loaded.");
        return;
    }

    console.log("products.js: Attempting to read data-attributes from dataContainer.");
    console.log("  data-products attribute content (first 100 chars):", dataContainer.dataset.products ? dataContainer.dataset.products.substring(0, 100) + '...' : 'empty');
    console.log("  data-next-page-url attribute content:", dataContainer.dataset.nextPageUrl);
    console.log("  data-has-more-pages attribute content:", dataContainer.dataset.hasMorePages);

    let allProducts;
    try {
        allProducts = JSON.parse(dataContainer.dataset.products);
        console.log("products.js: JSON.parse for data-products successful. Raw product count:", allProducts.length);
    } catch (e) {
        console.error("Error: products.js: Failed to parse initial product data JSON from data-products attribute!", e);
        return;
    }

    // --- IMPORTANT: Process rawProductsData into the format needed by the JS filtering logic ---
    // This mapping is for the initial page load data (from data-products)
    const processedProducts = allProducts.map(product => {
        const currentLocale = document.documentElement.lang;

        // --- FIX 1: Initial Load Data ---
        // 'product_image_path_raw' is the key sent by the controller for initial load.
        // So, access it directly from the incoming 'product' object.
        const imagePathForJs = product.product_image_path_raw;
        // --- END FIX 1 ---

        return {
            id: product.id,
            name: (currentLocale === 'es' && product.name_es) ? product.name_es : product.name,
            slug: product.slug,
            manufacturer: (currentLocale === 'es' && product.manufacturer?.name_es) ? product.manufacturer.name_es : (product.manufacturer?.name || ''),
            dosageForm: (currentLocale === 'es' && product.dosage_form?.name_es) ? product.dosage_form.name_es : (product.dosage_form?.name || ''),
            therapeuticCategory: (currentLocale === 'es' && product.therapeutic_category?.name_es) ? product.therapeutic_category.name_es : (product.therapeutic_category?.name || ''),
            availability_status: product.availability_status,
            description: (currentLocale === 'es' && product.description_es) ? product.description_es : product.description,
            product_image_path_raw: imagePathForJs // Consistent key for internal JS use
        };
    });

    allProducts = processedProducts; // Use the processed and localized data for all subsequent operations
    let filteredProducts = [...allProducts];

    let nextProductsPageUrl = dataContainer.dataset.nextPageUrl === 'null' ? null : dataContainer.dataset.nextPageUrl;
    let hasMorePages = dataContainer.dataset.hasMorePages === 'true';

    console.log("products.js: Initial Pagination State Loaded (Processed):");
    console.log("  Next Page URL:", nextProductsPageUrl);
    console.log("  Has More Pages:", hasMorePages);

    const storageBaseUrl = dataContainer.dataset.storageBaseUrl;
    // Your console.log showed 'http://ayuva.local:8897/storage' (no trailing slash).
    // The `asset('storage/')` in Blade should add a slash. Let's make the JS robust.
    const finalStorageBaseUrl = storageBaseUrl.endsWith('/') ? storageBaseUrl : `${storageBaseUrl}/`;

    const defaultProductImageUrl = dataContainer.dataset.defaultProductImageUrl;
    const productShowRoute = dataContainer.dataset.productShowRoute;
    const productBaseUrl = dataContainer.dataset.productBaseUrl;
    console.log("products.js: Base URLs and routes extracted.");

    let isLoading = false;
    let currentPage = 1; // Initialize currentPage for infinite scroll

    // ... DOM Elements (no changes needed here) ...
    const loader = document.getElementById('loader');
    const noResultsMessage = document.getElementById('no-results-message');
    const searchInput = document.getElementById('search-input');
    const manufacturerFilter = document.getElementById('manufacturer-filter');
    const dosageFormFilter = document.getElementById('dosage-form-filter');
    const therapeuticCategoryFilter = document.getElementById('therapeutic-category-filter');
    const availabilityFilter = document.getElementById('availability-filter');
    const applyBtn = document.getElementById('apply-filters-btn');
    const resetBtn = document.getElementById('reset-filters-btn');
    const header = document.querySelector('.sticky-header');
    const filterSection = document.getElementById('filter-section');
    console.log("products.js: All main DOM elements queried.");


    // --- CORE FUNCTIONS ---

    const createProductCard = (product) => {
        const cardLink = document.createElement('a');
        cardLink.href = `${productShowRoute}/${product.slug}`;
        cardLink.className = 'product-card-link';
        cardLink.style.textDecoration = 'none';

        const isOutOfStock = product.availability_status === 'Out of Stock';
        const statusClass = isOutOfStock ? 'status-out-of-stock' : 'status-available';
        const buttonText = isOutOfStock ? __('messages.out_of_stock') : __('messages.view_details');

        // --- FIX 2: Ensure correct base URL and path concatenation ---
        // product.product_image_path_raw is the consistent key from the mapped product objects
        const imageUrl = product.product_image_path_raw
            ? `${finalStorageBaseUrl}${product.product_image_path_raw}`
            : defaultProductImageUrl;
        // --- END FIX 2 ---

        // Debug logs (remove these once working)
        console.log('DEBUG - Product Name:', product.name);
        console.log('DEBUG - storageBaseUrl (from dataset):', storageBaseUrl); // Log raw dataset value
        console.log('DEBUG - finalStorageBaseUrl (adjusted):', finalStorageBaseUrl); // Log adjusted value
        console.log('DEBUG - product.product_image_path_raw:', product.product_image_path_raw);
        console.log('DEBUG - Final imageUrl being set to img src:', imageUrl);


        const descriptionSnippet = product.description
            ? product.description.substring(0, 100) + (product.description.length > 100 ? '...' : '')
            : '';

        cardLink.innerHTML = `
            <article class="product-card ${isOutOfStock ? 'out-of-stock' : ''}">
                <div class="status-badge ${statusClass}">${product.availability_status}</div>
                <img src="${imageUrl}" alt="${product.name}" class="product-image" loading="lazy" decoding="async">
                <div class="product-card-content">
                    <h3 style="color: var(--text-color);">${product.name}</h3>
                    <p class="product-manufacturer" style="color: #6c757d;">${product.manufacturer || ''}</p>
                    <div class="tag-container">
                        <span class="tag">${product.dosageForm || ''}</span>
                        <span class="tag">${product.therapeuticCategory || ''}</span>
                    </div>
                    <p class="product-description">${descriptionSnippet}</p>
                    <span class="enquire-btn">${buttonText}</span>
                </div>
            </article>
        `;
        return cardLink;
    };


    const renderProducts = (productsToRender, append = true) => {
        if (!append) {
            productGrid.innerHTML = '';
            allProducts = [];
            currentPage = 1;
        }

        const fragment = document.createDocumentFragment();
        productsToRender.forEach(product => {
            fragment.appendChild(createProductCard(product));
        });
        productGrid.appendChild(fragment);

        if (append) {
            allProducts = allProducts.concat(productsToRender);
        } else {
            allProducts = productsToRender;
        }

        noResultsMessage.style.display = allProducts.length === 0 ? 'block' : 'none';
        if (loader) loader.style.opacity = '0';
    };

    const fetchProducts = async (url, append = true) => {
        if (!append) {
            currentPage = 1;
        }

        if (isLoading || (append && !hasMorePages && url !== productBaseUrl)) {
            console.log("products.js: Fetch request skipped. Loading:", isLoading, "Has more pages:", hasMorePages, "URL:", url);
            return;
        }

        isLoading = true;
        if (loader) loader.style.opacity = '1';

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

            // --- FIX 3: AJAX Fetched Data ---
            // The controller sends `product.product_image_path` in AJAX response.
            // Map it to `product_image_path_raw` for internal JS consistency.
            const fetchedAndProcessedProducts = data.products.map(product => {
                const currentLocale = document.documentElement.lang;
                const pathFromAjax = product.product_image_path; // Key received from AJAX response

                return {
                    id: product.id,
                    name: (currentLocale === 'es' && product.name_es) ? product.name_es : product.name,
                    slug: product.slug,
                    manufacturer: (currentLocale === 'es' && product.manufacturer?.name_es) ? product.manufacturer.name_es : (product.manufacturer?.name || ''),
                    dosageForm: (currentLocale === 'es' && product.dosage_form?.name_es) ? product.dosage_form.name_es : (product.dosage_form?.name || ''),
                    therapeuticCategory: (currentLocale === 'es' && product.therapeutic_category?.name_es) ? product.therapeutic_category.name_es : (product.therapeutic_category?.name || ''),
                    availability_status: product.availability_status,
                    description: (currentLocale === 'es' && product.description_es) ? product.description_es : product.description,
                    product_image_path_raw: pathFromAjax // Assign to the consistent JS property
                };
            });
            // --- END FIX 3 ---


            renderProducts(fetchedAndProcessedProducts, append);

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

    // ... (rest of the functions: applyFilters, resetAllFilters, setStickyFilterTop) ...
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

    const resetAllFilters = () => {
        searchInput.value = '';
        manufacturerFilter.value = 'all';
        dosageFormFilter.value = 'all';
        therapeuticCategoryFilter.value = 'all';
        availabilityFilter.value = 'all';
        applyFilters();
    };

    const setStickyFilterTop = () => {
        if (header && filterSection) {
            const headerHeight = header.offsetHeight;
            filterSection.style.top = `${headerHeight}px`;
        }
    };

    // ... (Event Listeners and Infinite Scroll setup - no changes needed here) ...
    if (applyBtn) applyBtn.addEventListener('click', applyFilters);
    if (resetBtn) resetBtn.addEventListener('click', resetAllFilters);

    if (searchInput) searchInput.addEventListener('keyup', (event) => {
        if (event.key === 'Enter') applyFilters();
    });
    if (manufacturerFilter) manufacturerFilter.addEventListener('change', applyFilters);
    if (dosageFormFilter) dosageFormFilter.addEventListener('change', applyFilters);
    if (therapeuticCategoryFilter) therapeuticCategoryFilter.addEventListener('change', applyFilters);
    if (availabilityFilter) availabilityFilter.addEventListener('change', applyFilters);
    if (searchInput) searchInput.addEventListener('input', applyFilters);

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