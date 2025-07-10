{{-- resources/views/contact.blade.php --}}
@extends('layouts.app')

@section('title', __('messages.contact') . ' | ' . ($settings->site_name ?? __('messages.pharmaagtrading_name_default')))
@section('meta_description', $settings->site_description ?? __('messages.get_in_touch_desc'))

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
    <section class="page-header">
        <h1>{{ __('messages.get_in_touch_title') }}</h1>
        <p>{{ __('messages.get_in_touch_desc') }}</p>
    </section>

    <section class="contact-section">
        <div class="container">
            <div class="contact-layout">
                <div class="contact-details">
                    <h2 style="font-size: 2rem; margin-bottom: 25px;">{{ __('messages.our_office') }}</h2>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>
                            <strong>{{ $settings->site_name ?? __('messages.pharmaagtrading_name_default') }}</strong><br>
                            {!! nl2br(e($settings->{'contact_address_' . app()->getLocale()} ?? $settings->contact_address ?? __('messages.contact_address_default'))) !!}
                        </p>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone-alt"></i>
                        <p><a href="tel:{{ $settings->contact_phone ?? '+1 (809) 555-0101' }}">{{ $settings->contact_phone ?? '+1 (809) 555-0101' }}</a></p>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <p><a href="mailto:{{ $settings->contact_email ?? 'info.do@pharmaagtrading.net' }}">{{ $settings->contact_email ?? 'info.do@pharmaagtrading.net' }}</a></p>
                    </div>

                    <h3 style="margin-top: 40px;">{{ __('messages.business_hours') }}</h3>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <p><strong>{{ __('messages.monday_friday') }}</strong> 8:00 AM - 5:00 PM<br><strong>{{ __('messages.saturday_sunday') }}</strong> {{ __('messages.closed') }}</p>
                    </div>
                    <div id="contactMap"></div>
                </div>

                <div class="contact-form">
                    <h2>{{ __('messages.send_message') }}</h2>

                    {{-- Display Success/Error Messages from Laravel Session --}}
                    @if(session('success'))
                        <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; margin-bottom: 20px; border-radius: 5px;" role="alert">
                            {{ __('messages.email_success_message') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 15px; margin-bottom: 20px; border-radius: 5px;" role="alert">
                            {{ __('messages.email_error_message') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div style="background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 15px; margin-bottom: 20px; border-radius: 5px;" role="alert">
                            {{ __('messages.form_error_message') }}
                            <ul style="margin: 0; padding-left: 20px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('contact.submit') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="name">{{ __('messages.your_name') }}</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="email">{{ __('messages.your_email') }}</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="phone">{{ __('messages.phone_optional') }}</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="subject">{{ __('messages.subject_optional') }}</label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="message">{{ __('messages.your_message') }}</label>
                            <textarea id="message" name="message" rows="5" required class="form-control"></textarea>
                        </div>

                        <button type="submit" class="btn">{{ __('messages.send_message_button') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapElement = document.getElementById('contactMap');
            if (mapElement) {
                const mapLat = {{ $settings->map_lat ?? '18.4719' }};
                const mapLng = {{ $settings->map_lng ?? '-69.9409' }};
                const mapZoom = {{ $settings->map_zoom ?? '15' }};

                const map = L.map('contactMap').setView([mapLat, mapLng], mapZoom);

                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
                }).addTo(map);

                const customIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div style='background-color: var(--primary-color); width: 24px; height: 24px; border-radius: 50%; border: 4px solid #fff; box-shadow: 0 0 10px rgba(0,0,0,0.3);'></div>`,
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                });

                L.marker([mapLat, mapLng], { icon: customIcon })
                    .addTo(map)
                    .bindPopup('<b>{{ $settings->site_name ?? __('messages.pharmaagtrading_name_default') }}</b><br>{{ __('messages.pharmacorp_office_location') }}')
                    .openPopup();
            }
        });
    </script>
@endpush