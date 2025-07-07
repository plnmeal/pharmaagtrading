{{-- resources/views/components/header.blade.php --}}
<header class="sticky-header">
    <a href="{{ url('/') }}" class="logo">{{ $settings->site_name ?? 'PharmaAGTrading' }}</a>
    <nav class="desktop-nav">
        @forelse($headerNav as $item)
            <a href="{{ $item->url }}" class="{{ request()->is(ltrim($item->url, '/')) || (request()->is('/') && $item->type === 'home_index') ? 'active-link' : '' }}">
                {{ $item->label }}
            </a>
        @empty
            {{-- Fallback static links if no dynamic items are found --}}
            <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active-link' : '' }}">Home</a>
            <a href="{{ url('/#network') }}">Network</a>
            <a href="{{ route('products.index') }}" class="{{ request()->is('products*') ? 'active-link' : '' }}">Products</a>
            <a href="{{ route('news.index') }}" class="{{ request()->is('news*') ? 'active-link' : '' }}">News</a>
            <a href="{{ route('contact.index') }}" class="{{ request()->is('contact') ? 'active-link' : '' }}">Contact</a>
        @endforelse
    </nav>
    <div class="mobile-nav-toggle" id="mobileNavToggle">
        <i class="fa-solid fa-bars"></i>
    </div>
</header>

<div class="mobile-menu" id="mobileMenu">
    <div class="close-btn" id="closeBtn">&times;</div>
    @forelse($headerNav as $item) {{-- Using headerNav for mobile menu too --}}
        <a href="{{ $item->url }}">{{ $item->label }}</a>
    @empty
        {{-- Fallback static links for mobile menu --}}
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ url('/#network') }}">Network</a>
        <a href="{{ route('products.index') }}">Products</a>
        <a href="{{ route('news.index') }}">News</a>
        <a href="{{ route('contact.index') }}">Contact</a>
    @endforelse
</div>