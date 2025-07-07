{{-- resources/views/pages/show.blade.php --}}
@extends('layouts.app')

@section('title', $page->meta_title ?? $page->title)

@section('meta_description', $page->meta_description)

@section('content')
    <section class="page-header" style="padding-top: 50px; padding-bottom: 50px;">
        <div class="container" style="max-width: 900px;">
            <h1>{{ $page->title }}</h1>
        </div>
    </section>

    <section class="page-content-section">
        <div class="container" style="max-width: 900px;">
            <div class="content-panel">
                @if($page->featured_image_path)
                    <img src="{{ asset('storage/' . $page->featured_image_path) }}" alt="{{ $page->title }}" style="width: 100%; height: auto; border-radius: 8px; margin-bottom: 30px; object-fit: cover;" loading="lazy" decoding="async">
                @endif

                <div class="page-content" style="line-height: 1.8; font-size: 1.1rem;">
                    {!! $page->content !!} {{-- Render rich text content --}}
                </div>
            </div>
        </div>
    </section>
@endsection