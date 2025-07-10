{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', ($settings->hero_title ?? __('messages.home_default_title')) . ' | ' . ($settings->site_name ?? __('messages.pharmaagtrading_name_default')))</title>

    <meta name="description" content="@yield('meta_description', $settings->site_description ?? __('messages.site_default_description'))">
    <meta name="keywords" content="pharmaceutical distribution, pharma logistics, healthcare supply chain, Dominican Republic, Santo Domingo, medicine distribution, cold chain logistics">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">

    {{-- Expose Laravel translations to JavaScript --}}
    <script>
        window.translations = {
            // Add any messages your JavaScript needs here.
            // Ensure you use addslashes() to properly escape strings for JavaScript.
            'messages.out_of_stock': '{{ addslashes(__('messages.out_of_stock')) }}',
            'messages.view_details': '{{ addslashes(__('messages.view_details')) }}',
            'messages.no_products_found': '{{ addslashes(__('messages.no_products_found')) }}',
            // Example for other filter options if needed in JS:
            // 'messages.available': '{{ addslashes(__('messages.available')) }}',
            // 'messages.coming_soon': '{{ addslashes(__('messages.coming_soon')) }}',
            // 'messages.discontinued': '{{ addslashes(__('messages.discontinued')) }}',
            // 'messages.all_statuses': '{{ addslashes(__('messages.all_statuses')) }}',
            // 'messages.apply': '{{ addslashes(__('messages.apply')) }}',
            // 'messages.reset': '{{ addslashes(__('messages.reset')) }}',
            // ... add other messages as required by your JavaScript
        };

        // Define a global __ function that uses these translations
        function __(key, replace = {}) {
            let translation = window.translations[key] || key; // Fallback to key if not found

            for (let placeholder in replace) {
                translation = translation.replace(`:${placeholder}`, replace[placeholder]);
            }

            return translation;
        }
    </script>

    @stack('styles')
</head>
<body>

{{-- START SUBHEADER SECTION --}}
<div class="subheader">
    <div class="container subheader-container"> {{-- Added subheader-container class for specific flex control --}}
        <div class="social-links-subheader"> {{-- Social media icons on the left --}}
            @if($settings->facebook_url)
                <a href="{{ $settings->facebook_url }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a>
            @endif
            @if($settings->twitter_url)
                <a href="{{ $settings->twitter_url }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-twitter"></i></a>
            @endif
            @if($settings->linkedin_url)
                <a href="{{ $settings->linkedin_url }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-linkedin-in"></i></a>
            @endif
        </div>
        <div class="subheader-right-content"> {{-- Language switcher and contact details on the right --}}

            {{-- LANGUAGE SWITCHER --}}
            <div class="language-switcher">
                <a href="{{ route('locale.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active-lang' : '' }}">EN</a>
                <span>|</span>
                <a href="{{ route('locale.switch', 'es') }}" class="{{ app()->getLocale() === 'es' ? 'active-lang' : '' }}">ES</a>
            </div>
        </div>
    </div>
</div>
{{-- END SUBHEADER SECTION --}}

@include('components.header') {{-- Your existing header component --}}

<main>
    @yield('content')
</main>

@include('components.footer')

{{-- Common JavaScript --}}
<script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
@stack('scripts')

</body>
</html>