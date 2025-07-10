{{-- resources/views/components/header.blade.php --}}
<header class="sticky-header">
    <a href="{{ url('/') }}" class="logo">{{ $settings->site_name ?? __('messages.pharmaagtrading_name_default') }}</a>
    <nav class="desktop-nav">
        @php
            $headerNav = $navigationItems['header'] ?? collect();
        @endphp
        @forelse($headerNav->sortBy('order') as $item)
            {{-- Logic for active-link class based on URL or type --}}
            @php
                $isActive = false;
                $currentPath = request()->path(); // Get current path without domain
                $itemUrlPath = ltrim(parse_url($item->url, PHP_URL_PATH) ?? '', '/'); // Get path from item URL

                if ($item->type === 'home_index') {
                    $isActive = ($currentPath === '' || $currentPath === '/');
                } else if (!empty($itemUrlPath) && Str::startsWith($currentPath, $itemUrlPath)) {
                    // Check for exact match for simple routes first, then prefix for others
                    if ($currentPath === $itemUrlPath) { // E.g., /products exactly
                         $isActive = true;
                    } else if (Str::length($itemUrlPath) > 0 && Str::startsWith($currentPath, $itemUrlPath . '/')) { // E.g., /products/slug
                         $isActive = true;
                    }
                }
                if ($item->type === 'products_index' && request()->is('products*')) $isActive = true;
                if ($item->type === 'news_index' && request()->is('news*')) $isActive = true;
                if ($item->type === 'contact_index' && request()->is('contact')) $isActive = true;
                if ($item->type === 'page' && $item->page && request()->is($item->page->slug)) $isActive = true;

                // Handle homepage sections only if on the homepage
                if ($item->type === 'homepage_section' && request()->is('/')) {
                    // For active state based on anchor, you'd need JS to track scroll position.
                    // For now, it will not be 'active-link' unless manually handled.
                }
            @endphp
            <a href="{{ $item->url }}" class="{{ $isActive ? 'active-link' : '' }}">
                {{ $item->label }}
            </a>
        @empty
            {{-- Fallback static links if no dynamic items are found in CMS --}}
            <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active-link' : '' }}">{{ __('messages.home') }}</a>
            <a href="{{ url('/#network') }}">{{ __('messages.network') }}</a>
            <a href="{{ route('products.index') }}" class="{{ request()->is('products*') ? 'active-link' : '' }}">{{ __('messages.products') }}</a>
            <a href="{{ route('news.index') }}" class="{{ request()->is('news*') ? 'active-link' : '' }}">{{ __('messages.news') }}</a>
            <a href="{{ route('contact.index') }}" class="{{ request()->is('contact') ? 'active-link' : '' }}">{{ __('messages.contact') }}</a>
        @endforelse
    </nav>

    <div class="mobile-nav-toggle" id="mobileNavToggle">
        <i class="fa-solid fa-bars"></i>
    </div>
</header>

<div class="mobile-menu" id="mobileMenu">
    <div class="close-btn" id="closeBtn">&times;</div>
    {{-- Mobile menu uses the same header navigation items --}}
    @php
        $headerNav = $navigationItems['header'] ?? collect();
    @endphp
    @forelse($headerNav->sortBy('order') as $item)
        <a href="{{ $item->url }}">{{ $item->label }}</a>
    @empty
        <a href="{{ url('/') }}">{{ __('messages.home') }}</a>
        <a href="{{ url('/#network') }}">{{ __('messages.network') }}</a>
        <a href="{{ route('products.index') }}">{{ __('messages.products') }}</a>
        <a href="{{ route('news.index') }}">{{ __('messages.news') }}</a>
        <a href="{{ route('contact.index') }}">{{ __('messages.contact') }}</a>
    @endforelse
    {{-- LANGUAGE SWITCHER FOR MOBILE --}}
    <div class="language-switcher" style="margin-top: 20px; font-size: 1.5rem; text-align: center;">
        <a href="{{ route('locale.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active-lang' : '' }}" style="text-decoration: none; color: {{ app()->getLocale() === 'en' ? 'var(--secondary-color)' : 'var(--white)' }}; font-weight: {{ app()->getLocale() === 'en' ? '700' : '500' }}; margin-right: 10px;">EN</a>
        <span style="color: var(--white);">|</span>
        <a href="{{ route('locale.switch', 'es') }}" class="{{ app()->getLocale() === 'es' ? 'active-lang' : '' }}" style="text-decoration: none; color: {{ app()->getLocale() === 'es' ? 'var(--secondary-color)' : 'var(--white)' }}; font-weight: {{ app()->getLocale() === 'es' ? '700' : '500' }}; margin-left: 10px;">ES</a>
    </div>
    {{-- END LANGUAGE SWITCHER --}}
</div>