{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings->site_name ?? 'Ayuva' }} | @yield('title', 'Your Gateway to Quality Pharma Solutions')</title>

    {{-- Dynamic Meta Description from CMS --}}
    <meta name="description" content="@yield('meta_description', $settings->site_description ?? 'Ayuva is a leading pharmaceutical distributor in the Dominican Republic, offering intelligent supply chain services, a comprehensive product portfolio, and a nationwide logistics network.')">
    <meta name="keywords" content="pharmaceutical distribution, pharma logistics, healthcare supply chain, Dominican Republic, Santo Domingo, medicine distribution, cold chain logistics">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    {{-- Font Awesome (CDN) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    {{-- Placeholder for page-specific styles, like Leaflet CSS --}}
    @stack('styles')
</head>
<body>

{{-- Include Header Component, passing all necessary data --}}
{{-- Make sure $headerNav is passed here --}}
@include('components.header', compact('settings', 'headerNav'))

<main>
    @yield('content')
</main>

{{-- Include Footer Component, passing all necessary data --}}
{{-- Make sure $footerNavigateNav and $footerLegalNav are passed here --}}
@include('components.footer', compact('settings', 'footerNavigateNav', 'footerLegalNav'))

{{-- Common JavaScript --}}
<script src="{{ asset('js/app.js') }}"></script>
{{-- Page-specific JavaScript will be pushed here --}}
@stack('scripts')

</body>
</html>