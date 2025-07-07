{{-- resources/views/components/footer.blade.php --}}
<footer class="site-footer">
    <div class="container" style="padding-top: 60px; padding-bottom: 40px;">
        <div class="footer-grid">
            <div class="footer-column">
                <h4>About {{ $settings->site_name ?? 'PharmaAGTrading DR' }}</h4>
                <p>{!! $settings->site_description ?? 'Redefining pharmaceutical distribution with technology, precision, and reliability.' !!}</p>
                <div class="social-links" style="margin-top: 15px;">
                    @if($settings->twitter_url)<a href="{{ $settings->twitter_url }}"><i class="fab fa-twitter"></i></a>@endif
                    @if($settings->facebook_url)<a href="{{ $settings->facebook_url }}"><i class="fab fa-facebook-f"></i></a>@endif
                    @if($settings->linkedin_url)<a href="{{ $settings->linkedin_url }}"><i class="fab fa-linkedin-in"></i></a>@endif
                </div>
            </div>
            <div class="footer-column">
                <h4>Navigate</h4>
                <ul>
                    @forelse($footerNavigateNav as $item)
                        <li><a href="{{ $item->url }}">{{ $item->label }}</a></li>
                    @empty
                        {{-- Fallback static links if no dynamic items are found --}}
                        <li><a href="{{ route('products.index') }}">Products</a></li>
                        <li><a href="{{ url('/#services') }}">Services</a></li>
                        <li><a href="{{ route('news.index') }}">News</a></li>
                        <li><a href="#">Careers</a></li>
                    @endforelse
                </ul>
            </div>
            <div class="footer-column">
                <h4>Legal</h4>
                <ul>
                    @forelse($footerLegalNav as $item)
                        <li><a href="{{ $item->url }}">{{ $item->label }}</a></li>
                    @empty
                        {{-- Fallback static links if no dynamic items are found --}}
                        <li><a href="{{ route('pages.show', 'privacy-policy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('pages.show', 'terms-and-conditions') }}">Terms of Service</a></li>
                    @endforelse
                </ul>
            </div>
            <div class="footer-column">
                <h4>Contact Info</h4>
                <p>{!! nl2br(e($settings->contact_address ?? 'Av. John F. Kennedy,<br>Santo Domingo, D.N., 10122')) !!}</p>
                <p style="margin-top:10px;"><a href="mailto:{{ $settings->contact_email ?? 'info.do@PharmaAGTrading.com' }}" style="color: var(--text-light); text-decoration: none;">{{ $settings->contact_email ?? 'info.do@PharmaAGTrading.com' }}</a></p>
                @if($settings->contact_phone)<p style="margin-top:10px;"><a href="tel:{{ $settings->contact_phone }}" style="color: var(--text-light); text-decoration: none;">{{ $settings->contact_phone }}</a></p>@endif
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} {{ $settings->site_name ?? 'PharmaAGTrading' }}. All Rights Reserved.</p>

        {{-- NEW: "Powered by Ayvua (Bhandaris)" section --}}
        <table border="0" cellpadding="0" cellspacing="0" role="presentation" align="center" style="margin:8px auto 0 auto; text-align: center;">
            <tr>
                <td style="padding: 0; text-align: center;">
                    <a href="https://bhandaris.co" target="_blank" style="text-decoration: none; color: #999999; display: inline-block;">
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                                <td style="width: 20px; padding-right: 8px; vertical-align: middle;">
                                    <img src="{{ asset('images/bhandaris.png') }}" alt="Ayvua - Pharma CMS/ ERP by Bhandaris" width="20" height="20" style="display: block;">
                                </td>
                                <td style="vertical-align: middle; font-family: 'Roboto', sans-serif; font-size: 12px; color: #999999;">
                                    Powered by Ayvua (Bhandaris)
                                </td>
                            </tr>
                        </table>
                    </a>
                </td>
            </tr>
        </table>
        {{-- END NEW SECTION --}}

    </div>
</footer>