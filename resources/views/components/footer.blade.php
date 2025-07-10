{{-- resources/views/components/footer.blade.php --}}
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-column">
                <h4>{{ __('messages.about_pharmaagtrading_dr') }}</h4>
                <p>{!! $settings->site_description ?? __('messages.site_description_short') !!}</p>
                <div class="social-links" style="margin-top: 15px;">
                    @if($settings->twitter_url)<a href="{{ $settings->twitter_url }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-twitter"></i></a>@endif
                    @if($settings->facebook_url)<a href="{{ $settings->facebook_url }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i></a>@endif
                    @if($settings->linkedin_url)<a href="{{ $settings->linkedin_url }}" target="_blank" rel="noopener noreferrer"><i class="fab fa-linkedin-in"></i></a>@endif
                </div>
            </div>
            <div class="footer-column">
                <h4>{{ __('messages.navigate') }}</h4>
                <ul>
                    @php
                        $footerNavigateNav = $navigationItems['footer_navigate'] ?? collect();
                    @endphp
                    @forelse($footerNavigateNav->sortBy('order') as $item)
                        <li><a href="{{ $item->url }}">{{ $item->label }}</a></li>
                    @empty
                        <li><a href="{{ route('products.index') }}">{{ __('messages.products') }}</a></li>
                        <li><a href="{{ url('/#services') }}">{{ __('messages.services') }}</a></li>
                        <li><a href="{{ route('news.index') }}">{{ __('messages.news') }}</a></li>
                        <li><a href="#">{{ __('messages.careers') }}</a></li>
                    @endforelse
                </ul>
            </div>
            <div class="footer-column">
                <h4>{{ __('messages.legal') }}</h4>
                <ul>
                    @php
                        $footerLegalNav = $navigationItems['footer_legal'] ?? collect();
                    @endphp
                    @forelse($footerLegalNav->sortBy('order') as $item)
                        <li><a href="{{ $item->url }}">{{ $item->label }}</a></li>
                    @empty
                        <li><a href="{{ route('pages.show', 'privacy-policy') }}">{{ __('messages.privacy_policy') }}</a></li>
                        <li><a href="{{ route('pages.show', 'terms-and-conditions') }}">{{ __('messages.terms_of_service') }}</a></li>
                    @endforelse
                </ul>
            </div>
            <div class="footer-column">
                <h4>{{ __('messages.contact_info') }}</h4>
                <p>{!! nl2br(e($settings->contact_address ?? __('messages.contact_address_default'))) !!}</p>
                <p class="footer-contact-link"><a href="mailto:{{ $settings->contact_email ?? 'info.do@pharmaagtrading.net' }}" style="color: var(--text-light); text-decoration: none;">{{ $settings->contact_email ?? 'info.do@pharmaagtrading.net' }}</a></p>
                @if($settings->contact_phone)<p class="footer-contact-link"><a href="tel:{{ $settings->contact_phone }}" style="color: var(--text-light); text-decoration: none;">{{ $settings->contact_phone }}</a></p>@endif
            </div>
        </div>

    </div>
            <div class="footer-bottom" style="background-color: var(--light-background);">

            <p>&copy; {{ date('Y') }} {{ $settings->site_name ?? __('messages.pharmaagtrading_name_default') }}. {{ __('messages.copyright_all_rights') }}</p>

            {{-- "Powered by Ayvua (Bhandaris)" section --}}
            {{-- Removed inline style and added class for CSS targeting --}}
            <table class="powered-by-table" border="0" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                    <td style="padding: 0; text-align: center;">
                        <a href="https://bhandaris.co" target="_blank" rel="noopener noreferrer" style="text-decoration: none; color: #999999; display: inline-block;">
                            <table border="0" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td style="width: 20px; padding-right: 8px; vertical-align: middle;">
                                        <img src="{{ asset('images/bhandaris.png') }}" alt="{{ __('messages.unp_owered_by_company') }}" width="20" height="20" style="display: block;">
                                    </td>
                                    <td style="vertical-align: middle; font-family: 'Roboto', sans-serif; font-size: 12px; color: inherit;"> {{-- Changed color to inherit from parent --}}
                                        {{ __('messages.unp_owered_by') }} {{ __('messages.unp_owered_by_company') }}
                                    </td>
                                </tr>
                            </table>
                        </a>
                    </td>
                </tr>
            </table>
        </div>
</footer>