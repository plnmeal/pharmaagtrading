{{-- resources/views/contact.blade.php --}}
@extends('layouts.app') {{-- EXTEND THE MASTER LAYOUT --}}

@section('title', 'Contact Us | ' . ($settings->site_name ?? 'Ayuva'))

{{-- Push Leaflet CSS to the head (only on this page) --}}
@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
    <section class="page-header">
        <h1>Contact PharmaAGTrading</h1>
        <p>We're here to help! Reach out to us for any inquiries, partnerships, or support.</p>
    </section>

    <section class="contact-section"> {{-- Use contact-section class from your style.css --}}
        <div class="container">
            <div class="contact-layout"> {{-- Use contact-layout from your style.css --}}
                <div class="contact-details"> {{-- Use contact-details from your style.css --}}
                    <h2 style="font-size: 2rem; margin-bottom: 25px;">Our Office</h2>
                    <div class="info-item"> {{-- Use info-item from your style.css --}}
                        <i class="fas fa-map-marker-alt"></i>
                        <p>
                            <strong>{{ $settings->site_name ?? 'PharmaAGTrading Dominicana' }}</strong><br>
                            {!! nl2br(e($settings->contact_address ?? 'Av. John F. Kennedy, No. 10,<br>Ensanche Miraflores, Santo Domingo,<br>Dominican Republic, 10122')) !!}
                        </p>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone-alt"></i>
                        <p><a href="tel:{{ $settings->contact_phone ?? '+1 (809) 555-0101' }}">{{ $settings->contact_phone ?? '+1 (809) 555-0101' }}</a></p>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <p><a href="mailto:{{ $settings->contact_email ?? 'info.do@PharmaAGTrading.com' }}">{{ $settings->contact_email ?? 'info.do@PharmaAGTrading.com' }}</a></p>
                    </div>

                    <h3 style="margin-top: 40px;">Business Hours</h3>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <p><strong>Monday - Friday:</strong> 8:00 AM - 5:00 PM<br><strong>Saturday - Sunday:</strong> Closed</p> {{-- Note: Business hours are hardcoded here for now --}}
                    </div>
                    <div id="contactMap"></div> {{-- Map will be initialized by JS --}}
                </div>

                <div class="contact-form"> {{-- Use contact-form from your style.css --}}
                    <h2>Send Us a Message</h2>

                    {{-- Display Success/Error Messages from Laravel Session --}}
                    @if(session('success'))
                        <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; margin-bottom: 20px; border-radius: 5px;" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 15px; margin-bottom: 20px; border-radius: 5px;" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div style="background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 15px; margin-bottom: 20px; border-radius: 5px;" role="alert">
                            Please correct the following errors:
                            <ul style="margin: 0; padding-left: 20px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('contact.submit') }}" method="POST">
                        @csrf {{-- CSRF token for Laravel forms --}}

                        <div class="form-group"> {{-- Use form-group from your style.css --}}
                            <label for="name">Your Name:</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required class="form-control"> {{-- Use form-control from style.css --}}
                        </div>

                        <div class="form-group">
                            <label for="email">Your Email:</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone (Optional):</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject (Optional):</label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="message">Your Message:</label>
                            <textarea id="message" name="message" rows="5" required class="form-control"></textarea>
                        </div>

                        <button type="submit" class="btn">Send Message</button> {{-- Use btn from style.css --}}
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
                // Coordinates for the address in Santo Domingo - these can be dynamic from settings later
                // You can add map_lat, map_lng, map_zoom to your 'settings' table and update them in Filament
                const mapLat = {{ $settings->map_lat ?? '18.4719' }};
                const mapLng = {{ $settings->map_lng ?? '-69.9409' }};
                const mapZoom = {{ $settings->map_zoom ?? '15' }};

                const map = L.map('contactMap').setView([mapLat, mapLng], mapZoom);

                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>'
                }).addTo(map);

                const customIcon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div style='background-color: var(--primary-color); width: 24px; height: 24px; border-radius: 50%; border: 4px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.3);'></div>`,
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                });

                L.marker([mapLat, mapLng], { icon: customIcon })
                    .addTo(map)
                    .bindPopup('<b>{{ $settings->site_name ?? 'PharmaAGTrading Dominicana' }}</b><br>Our Main Office')
                    .openPopup();
            }
        });
    </script>
@endpush